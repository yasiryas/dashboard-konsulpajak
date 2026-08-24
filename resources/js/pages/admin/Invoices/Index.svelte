<script module lang="ts">
    import { dashboard } from '@/routes';
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
            },
            { title: 'Invoice' },
        ],
    });
</script>

<script lang="ts">
    import { router, useForm } from '@inertiajs/svelte';
    import StatusBadge from '@/components/admin/StatusBadge.svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Dialog,
        DialogContent,
        DialogDescription,
        DialogFooter,
        DialogTitle,
    } from '@/components/ui/dialog';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import * as Select from '@/components/ui/select';
    import { currentPath } from '@/lib/currentUrl.svelte';
    import { formatIDR, toUrl } from '@/lib/utils';
    import invoicesRoutes from '@/routes/admin/invoices';
    import type { InvoiceRow, Paginated } from '@/types';
    import { Plus, Trash2 } from 'lucide-svelte';

    let {
        invoices,
        ringkasan,
        filters,
        clients = [],
    }: {
        invoices: Paginated<InvoiceRow>;
        ringkasan: { totalTagihan: number; lunas: number; outstanding: number };
        filters: { status: string };
        clients?: { id: number; namaEntitas: string }[];
    } = $props();

    const teamSlug = $derived(currentPath().split('/')[1]);

    const STATUS_OPTIONS = [
        { value: 'belum_dibayar', label: 'Belum Dibayar' },
        { value: 'lunas', label: 'Lunas' },
        { value: 'batal', label: 'Batal' },
    ];

    let statusFilter = $state(filters.status);
    let createOpen = $state(false);
    let statusUpdateOpen = $state(false);
    let deleteOpen = $state(false);
    let selectedId = $state<number | null>(null);
    let selectedStatus = $state<string>('');

    const form = useForm({
        client_profile_id: '',
        periode: '',
        nominal: '',
    });

    function applyFilters() {
        router.get(
            currentPath(),
            { status: statusFilter || undefined },
            { preserveState: true, preserveScroll: true },
        );
    }

    function gotoPage(targetPage: number) {
        router.get(
            currentPath(),
            { status: statusFilter || undefined, page: targetPage },
            { preserveState: true, preserveScroll: true },
        );
    }

    function openCreate() {
        form.reset();
        createOpen = true;
    }

    function submit(event: SubmitEvent) {
        event.preventDefault();
        form.post(toUrl(invoicesRoutes.store.url({ current_team: teamSlug })), {
            preserveScroll: true,
            onSuccess: () => (createOpen = false),
        });
    }

    function openStatusUpdate(invoice: InvoiceRow) {
        selectedId = invoice.id;
        selectedStatus = invoice.status;
        statusUpdateOpen = true;
    }

    function submitStatusUpdate() {
        if (!selectedId) return;

        router.put(
            toUrl(invoicesRoutes.status.url({ current_team: teamSlug, invoice: selectedId })),
            { status_bayar: selectedStatus },
            {
                preserveScroll: true,
                onSuccess: () => (statusUpdateOpen = false),
            },
        );
    }

    function openDelete(invoice: InvoiceRow) {
        selectedId = invoice.id;
        deleteOpen = true;
    }

    function confirmDelete() {
        if (!selectedId) return;

        router.delete(
            toUrl(invoicesRoutes.destroy.url({ current_team: teamSlug, invoice: selectedId })),
            {
                preserveScroll: true,
                onSuccess: () => (deleteOpen = false),
            },
        );
    }

    const selectedInvoice = $derived(
        invoices.data.find((invoice) => invoice.id === selectedId),
    );
</script>

<AppHead title="Kelola Invoice" />

<div class="flex flex-col gap-6 p-4 sm:p-6 lg:p-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Kelola Invoice</h1>
            <p class="mt-1 text-sm text-muted-foreground">Tagihan per klien dan periode.</p>
        </div>
        <div class="flex gap-3">
            <Select.Root
                type="single"
                bind:value={statusFilter}
                onValueChange={() => applyFilters()}
            >
                <Select.Trigger class="w-full sm:w-40">
                    {statusFilter
                        ? STATUS_OPTIONS.find((o) => o.value === statusFilter)?.label
                        : 'Semua Status'}
                </Select.Trigger>
                <Select.Content>
                    {#each STATUS_OPTIONS as option (option.value)}
                        <Select.Item value={option.value}>{option.label}</Select.Item>
                    {/each}
                </Select.Content>
            </Select.Root>

            <Button variant="primary" onclick={openCreate}>
                <Plus class="size-4" />
                Tambah Invoice
            </Button>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                    <th class="px-5 py-3 font-medium">Klien</th>
                    <th class="px-5 py-3 font-medium">Periode</th>
                    <th class="px-5 py-3 font-medium">Nominal</th>
                    <th class="hidden px-5 py-3 font-medium md:table-cell">Status</th>
                    <th class="px-5 py-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {#if invoices.data.length === 0}
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-muted-foreground">
                            Belum ada invoice. Klik "Tambah Invoice" untuk membuat yang pertama.
                        </td>
                    </tr>
                {:else}
                    {#each invoices.data as invoice (invoice.id)}
                        <tr class="border-b border-border/60 transition-colors last:border-0 hover:bg-muted/50">
                            <td class="px-5 py-3.5 font-medium">{invoice.klien ?? '-'}</td>
                            <td class="px-5 py-3.5 text-muted-foreground">{invoice.periode}</td>
                            <td class="px-5 py-3.5 font-semibold text-gold">{formatIDR(invoice.nominal)}</td>
                            <td class="hidden px-5 py-3.5 md:table-cell">
                                <StatusBadge status={invoice.status} label={invoice.statusLabel} />
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="inline-flex gap-1">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onclick={() => openStatusUpdate(invoice)}
                                    >
                                        Ubah Status
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        onclick={() => openDelete(invoice)}
                                    >
                                        <Trash2 class="size-3.5" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    {/each}
                {/if}
            </tbody>
        </table>

        {#if invoices.meta.lastPage > 1}
            <div class="flex items-center justify-between border-t border-border px-5 py-3">
                <span class="text-sm text-muted-foreground">
                    Halaman {invoices.meta.currentPage} dari {invoices.meta.lastPage}
                </span>
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        disabled={invoices.meta.currentPage <= 1}
                        onclick={() => gotoPage(invoices.meta.currentPage - 1)}
                    >
                        Sebelumnya
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        disabled={invoices.meta.currentPage >= invoices.meta.lastPage}
                        onclick={() => gotoPage(invoices.meta.currentPage + 1)}
                    >
                        Berikutnya
                    </Button>
                </div>
            </div>
        {/if}
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-border bg-card p-4">
            <p class="text-sm text-muted-foreground">Total Tagihan</p>
            <p class="mt-1 text-xl font-bold text-gold">{formatIDR(ringkasan.totalTagihan)}</p>
        </div>
        <div class="rounded-xl border border-border bg-card p-4">
            <p class="text-sm text-muted-foreground">Lunas</p>
            <p class="mt-1 text-xl font-bold text-green-600">{formatIDR(ringkasan.lunas)}</p>
        </div>
        <div class="rounded-xl border border-border bg-card p-4">
            <p class="text-sm text-muted-foreground">Belum Dibayar</p>
            <p class="mt-1 text-xl font-bold text-red-600">{formatIDR(ringkasan.outstanding)}</p>
        </div>
    </div>
</div>

<Dialog bind:open={createOpen}>
    <DialogContent>
        <DialogTitle>Tambah Invoice</DialogTitle>
        <DialogDescription>Buat tagihan manual untuk klien.</DialogDescription>

        <form class="grid gap-4 py-2" onsubmit={submit}>
            <div class="grid gap-2">
                <Label for="klien">Klien</Label>
                <Select.Root
                    type="single"
                    bind:value={form.client_profile_id}
                    required
                >
                    <Select.Trigger id="klien" class="w-full">
                        {form.client_profile_id
                            ? clients.find((c) => String(c.id) === form.client_profile_id)?.namaEntitas
                            : 'Pilih klien'}
                    </Select.Trigger>
                    <Select.Content>
                        {#each clients as client (client.id)}
                            <Select.Item value={String(client.id)}>{client.namaEntitas}</Select.Item>
                        {/each}
                    </Select.Content>
                </Select.Root>
                {#if form.errors.client_profile_id}<p class="text-sm text-destructive">{form.errors.client_profile_id}</p>{/if}
            </div>

            <div class="grid gap-2">
                <Label for="periode">Periode</Label>
                <Input id="periode" type="month" bind:value={form.periode} required />
                {#if form.errors.periode}<p class="text-sm text-destructive">{form.errors.periode}</p>{/if}
            </div>

            <div class="grid gap-2">
                <Label for="nominal">Nominal (Rp)</Label>
                <Input id="nominal" type="number" min="0" bind:value={form.nominal} required />
                {#if form.errors.nominal}<p class="text-sm text-destructive">{form.errors.nominal}</p>{/if}
            </div>

            <DialogFooter>
                <Button type="button" variant="ghost" onclick={() => (createOpen = false)}>Batal</Button>
                <Button type="submit" disabled={form.processing}>
                    {form.processing ? 'Menyimpan...' : 'Simpan'}
                </Button>
            </DialogFooter>
        </form>
    </DialogContent>
</Dialog>

<Dialog bind:open={statusUpdateOpen}>
    <DialogContent>
        <DialogTitle>Ubah Status Invoice</DialogTitle>
        <DialogDescription>
            {selectedInvoice?.klien ?? ''} · periode {selectedInvoice?.periode ?? ''}
        </DialogDescription>

        <div class="grid gap-2">
            {#each STATUS_OPTIONS as option (option.value)}
                <label class="flex cursor-pointer select-none items-center gap-2 transition-colors hover:text-gold">
                    <input
                        type="radio"
                        name="status-update"
                        bind:group={selectedStatus}
                        value={option.value}
                    />
                    <span>{option.label}</span>
                </label>
            {/each}
        </div>

        <DialogFooter class="justify-end">
            <Button variant="outline" onclick={() => (statusUpdateOpen = false)}>Batal</Button>
            <Button variant="primary" onclick={submitStatusUpdate} disabled={form.processing}>
                Perbarui
            </Button>
        </DialogFooter>
    </DialogContent>
</Dialog>

<Dialog bind:open={deleteOpen}>
    <DialogContent>
        <DialogTitle>Hapus Invoice</DialogTitle>
        <DialogDescription>
            Yakin ingin menghapus tagihan {selectedInvoice?.klien ?? 'ini'} periode
            {selectedInvoice?.periode ?? ''}? Tindakan ini tidak dapat dibatalkan.
        </DialogDescription>

        <DialogFooter class="justify-end">
            <Button variant="outline" onclick={() => (deleteOpen = false)}>Batal</Button>
            <Button variant="destructive" onclick={confirmDelete}>Hapus</Button>
        </DialogFooter>
    </DialogContent>
</Dialog>
