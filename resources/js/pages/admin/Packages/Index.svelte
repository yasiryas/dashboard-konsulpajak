<script module lang="ts">
    import { dashboard } from '@/routes';
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
            },
            { title: 'Paket Layanan' },
        ],
    });
</script>

<script lang="ts">
    import { router, useForm } from '@inertiajs/svelte';
    import Pencil from 'lucide-svelte/icons/pencil';
    import Plus from 'lucide-svelte/icons/plus';
    import Trash2 from 'lucide-svelte/icons/trash-2';
    import AppHead from '@/components/AppHead.svelte';
    import { Badge } from '@/components/ui/badge';
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
    import packagesRoutes from '@/routes/admin/packages';
    import type { PackageRow } from '@/types';

    let {
        packages = [],
    }: {
        packages?: PackageRow[];
    } = $props();

    const teamSlug = $derived(currentPath().split('/')[1]);

    const JENIS_OPTIONS = [
        { value: 'dokter', label: 'Dokter' },
        { value: 'pengacara', label: 'Pengacara' },
        { value: 'notaris', label: 'Notaris' },
        { value: 'umkm', label: 'UMKM' },
        { value: 'pt', label: 'PT' },
        { value: 'cv', label: 'CV' },
        { value: 'yayasan', label: 'Yayasan' },
    ];

    let dialogOpen = $state(false);
    let editingId = $state<number | null>(null);
    let hapusOpen = $state(false);
    let hapusId: number | null = null;
    let hapusNama = '';

    const form = useForm({
        nama_paket: '',
        deskripsi: '',
        jenis_klien: '',
        harga: '',
    });

    function openCreate() {
        editingId = null;
        form.defaults({ nama_paket: '', deskripsi: '', jenis_klien: '', harga: '' });
        form.reset();
        dialogOpen = true;
    }

    function openEdit(pkg: PackageRow) {
        editingId = pkg.id;
        form.defaults({
            nama_paket: pkg.namaPaket,
            deskripsi: pkg.deskripsi ?? '',
            jenis_klien: JENIS_OPTIONS.find((o) => o.label === pkg.jenisKlien)?.value ?? '',
            harga: String(pkg.harga),
        });
        form.reset();
        dialogOpen = true;
    }

    function submit(event: SubmitEvent) {
        event.preventDefault();

        if (editingId) {
            form.put(toUrl(packagesRoutes.update.url({ current_team: teamSlug, package: editingId })), {
                onSuccess: () => (dialogOpen = false),
            });
        } else {
            form.post(toUrl(packagesRoutes.store.url({ current_team: teamSlug })), {
                onSuccess: () => (dialogOpen = false),
            });
        }
    }

    function hapus(pkg: PackageRow) {
        dialogOpen = true;
        hapusId = pkg.id;
        hapusNama = pkg.namaPaket;
    }
</script>

<AppHead title="Paket Layanan" />

<div class="flex flex-col gap-6 p-4 sm:p-6 lg:p-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Paket Layanan</h1>
            <p class="mt-1 text-sm text-muted-foreground">Kelola paket dan harga layanan konsultasi.</p>
        </div>
        <Button onclick={openCreate}>
            <Plus class="size-4" />
            Tambah Paket
        </Button>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        {#if packages.length === 0}
            <p class="text-sm text-muted-foreground md:col-span-2 xl:col-span-3">
                Belum ada paket. Klik "Tambah Paket" untuk membuat yang pertama.
            </p>
        {/if}
        {#each packages as pkg (pkg.id)}
            <div class="flex flex-col rounded-xl border border-border bg-card p-5 transition-colors hover:border-gold/40">
                <div class="flex items-start justify-between">
                    <Badge variant="outline">{pkg.jenisKlien}</Badge>
                    <span class="text-xs text-muted-foreground">{pkg.klienCount} klien</span>
                </div>
                <h3 class="mt-3 font-semibold">{pkg.namaPaket}</h3>
                <p class="mt-1 line-clamp-2 min-h-10 text-sm text-muted-foreground">
                    {pkg.deskripsi ?? 'Tanpa deskripsi.'}
                </p>
                <p class="mt-3 text-xl font-bold text-gold">{formatIDR(pkg.harga)}</p>
                <div class="mt-4 flex gap-2">
                    <Button variant="outline" size="sm" onclick={() => openEdit(pkg)}>
                        <Pencil class="size-3.5" />
                        Edit
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        class="text-destructive hover:text-destructive"
                        disabled={pkg.klienCount > 0}
                        onclick={() => hapus(pkg)}
                    >
                        <Trash2 class="size-3.5" />
                        Hapus
                    </Button>
                </div>
            </div>
        {/each}
    </div>
</div>

<Dialog bind:open={dialogOpen}>
    <DialogContent>
        <DialogTitle>{editingId ? 'Edit Paket' : 'Tambah Paket'}</DialogTitle>
        <DialogDescription>
            Isi detail paket layanan untuk jenis klien tertentu.
        </DialogDescription>

        <form class="grid gap-4 py-2" onsubmit={submit}>
            <div class="grid gap-2">
                <Label for="nama_paket">Nama Paket</Label>
                <Input id="nama_paket" bind:value={form.nama_paket} required />
                {#if form.errors.nama_paket}<p class="text-sm text-destructive">{form.errors.nama_paket}</p>{/if}
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label>Jenis Klien</Label>
                    <Select.Root type="single" bind:value={form.jenis_klien} required>
                        <Select.Trigger class="w-full">
                            {form.jenis_klien
                                ? JENIS_OPTIONS.find((o) => o.value === form.jenis_klien)?.label
                                : 'Pilih jenis'}
                        </Select.Trigger>
                        <Select.Content>
                            {#each JENIS_OPTIONS as option (option.value)}
                                <Select.Item value={option.value}>{option.label}</Select.Item>
                            {/each}
                        </Select.Content>
                    </Select.Root>
                    {#if form.errors.jenis_klien}<p class="text-sm text-destructive">{form.errors.jenis_klien}</p>{/if}
                </div>

                <div class="grid gap-2">
                    <Label for="harga">Harga (Rp)</Label>
                    <Input id="harga" type="number" min="0" bind:value={form.harga} required />
                    {#if form.errors.harga}<p class="text-sm text-destructive">{form.errors.harga}</p>{/if}
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="deskripsi">Deskripsi</Label>
                <Input id="deskripsi" bind:value={form.deskripsi} />
            </div>

            <DialogFooter>
                <Button type="button" variant="ghost" onclick={() => (dialogOpen = false)}>Batal</Button>
                <Button type="submit" disabled={form.processing}>
                    {form.processing ? 'Menyimpan...' : 'Simpan'}
                </Button>
            </DialogFooter>
        </form>
    </DialogContent>
</Dialog>

    <Dialog bind:open={hapusOpen}>
        <DialogContent class="max-w-sm">
            <DialogTitle>Hapus Paket</DialogTitle>
            <DialogDescription>
                Apakah Anda yakin ingin menghapus paket <strong>{hapusNama}</strong>? Aksi ini tidak dapat dibatalkan.
            </DialogDescription>

            <DialogFooter class="justify-end">
                <Button variant="outline" onclick={() => (hapusOpen = false)}>Batal</Button>
                <Button variant="destructive" onclick={() => {
                    router.delete(toUrl(packagesRoutes.destroy.url({ current_team: teamSlug, package: hapusId })), { preserveScroll: true });
                    hapusOpen = false;
                }}>
                    <Trash2 class="size-4" />
                    Ya, hapus
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
