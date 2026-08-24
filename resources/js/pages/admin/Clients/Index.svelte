<script module lang="ts">
    import { dashboard } from '@/routes';
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
            },
            { title: 'Daftar Klien' },
        ],
    });
</script>

<script lang="ts">
    import { Link, router, useForm } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import * as Select from '@/components/ui/select';
    import { currentPath } from '@/lib/currentUrl.svelte';
    import clientsRoutes from '@/routes/admin/clients';
    import type { ClientIndexRow, Paginated } from '@/types';

    let {
        clients,
        filters,
    }: {
        clients: Paginated<ClientIndexRow>;
        filters: { cari: string; jenis: string; paket: number | null };
    } = $props();

    let dialogOpen = $state(false);
    let editingId = $state<number | null>(null);

    const teamSlug = $derived(currentPath().split('/')[1]);

    const form = useForm({
        name: '',
        email: '',
        password: '',
        npwp: '',
        nama_entitas: '',
        package_id: '',
        harga: '',
    });

    const JENIS_OPTIONS = [
        { value: 'dokter', label: 'Dokter' },
        { value: 'pengacara', label: 'Pengacara' },
        { value: 'notaris', label: 'Notaris' },
        { value: 'umkm', label: 'UMKM' },
        { value: 'pt', label: 'PT' },
        { value: 'cv', label: 'CV' },
        { value: 'yayasan', label: 'Yayasan' },
    ];

    let cari = $state(filters.cari);
    let jenis = $state(filters.jenis);
    let timer: ReturnType<typeof setTimeout> | undefined;

    function applyFilters(overrides: Record<string, unknown> = {}) {
        router.get(currentPath(), { ...currentFilters(), ...overrides }, {
            preserveState: true,
            preserveScroll: true,
        });
    }

    function currentFilters() {
        return {
            cari: cari || undefined,
            jenis: jenis || undefined,
        };
    }

    function onSearchInput() {
        clearTimeout(timer);
        timer = setTimeout(() => applyFilters(), 400);
    }

    function gotoPage(targetPage: number) {
        router.get(currentPath(), { ...currentFilters(), page: targetPage }, {
            preserveState: true,
            preserveScroll: true,
        });
    }

    function submit(event: SubmitEvent) {
        event.preventDefault();

        form.post(clientsRoutes.store.url({ current_team: teamSlug }), {
            forceFormData: true,
            onSuccess: () => (dialogOpen = false),
        });
    }
</script>

<AppHead title="Daftar Klien" />

    <Dialog bind:open={dialogOpen}>
        <DialogContent>
            <DialogTitle>Tambah Klien Baru</DialogTitle>
            <DialogDescription>
                Buat akun klien baru dan profil proyek.
            </DialogDescription>

            <form class="grid gap-4 py-2" onsubmit={submit}>
                <div class="grid gap-2">
                    <Label for="name">Nama Lengkap</Label>
                    <Input id="name" bind:value={form.name} required />
                    {#if form.errors.name}<p class="text-sm text-destructive">{form.errors.name}</p>{/if}
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" bind:value={form.email} required />
                    {#if form.errors.email}<p class="text-sm text-destructive">{form.errors.email}</p>{/if}
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input id="password" type="password" bind:value={form.password} required minlength="8" />
                    {#if form.errors.password}<p class="text-sm text-destructive">{form.errors.password}</p>{/if}
                </div>

                <div class="grid grid-cols-2 gap-2">
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
                        <Label>NPWP</Label>
                        <Input id="npwp" bind:value={form.npwp} />
                        {#if form.errors.npwp}<p class="text-sm text-destructive">{form.errors.npwp}</p>{/if}
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="nama_entitas">Nama Entitas</Label>
                    <Input id="nama_entitas" bind:value={form.nama_entitas} required />
                    {#if form.errors.nama_entitas}<p class="text-sm text-destructive">{form.errors.nama_entitas}</p>{/if}
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="grid gap-2">
                        <Label>Paket Layanan</Label>
                        <Select.Root type="single" bind:value={form.package_id} required>
                            <Select.Trigger class="w-full">
                                <Select.Content>
                                    {#each packages as pkg (pkg.id)}
                                        <Select.Item value={pkg.id}>{pkg.namaPaket}</Select.Item>
                                    {/each}
                                </Select.Content>
                            </Select.Trigger>
                        </Select.Root>
                        {#if form.errors.package_id}<p class="text-sm text-destructive">{form.errors.package_id}</p>{/if}
                    </div>

                    <div>
                        <Label>Harga Paket (opsional)</Label>
                        <Input type="number" min="0" bind:value={form.harga} />
                    </div>
                </div>

                <DialogFooter>
                    <Button type="button" variant="ghost" onclick={() => (dialogOpen = false)}>Batal</Button>
                    <Button type="submit" disabled={form.processing}>
                        {form.processing ? 'Membuat...' : 'Buat Klien'}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <div class="flex flex-col gap-6 p-4 sm:p-6 lg:p-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Daftar Klien</h1>
            <p class="mt-1 text-sm text-muted-foreground">{clients.meta.total} klien terdaftar.</p>
        </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <Input
          placeholder="Cari nama entitas / NPWP / email..."
          bind:value={cari}
          oninput={onSearchInput}
          class="sm:max-w-xs"
        />
        <Select.Root
            type="single"
            bind:value={jenis}
            onValueChange={() => applyFilters()}
        >
            <Select.Trigger class="w-full sm:w-44">
                {jenis
                    ? JENIS_OPTIONS.find((o) => o.value === jenis)?.label
                    : 'Semua Jenis Klien'}
            </Select.Trigger>
            <Select.Content>
                {#each JENIS_OPTIONS as option (option.value)}
                    <Select.Item value={option.value}>{option.label}</Select.Item>
                {/each}
            </Select.Content>
        </Select.Root>
        {#if cari || jenis}
            <Button
                variant="ghost"
                onclick={() => {
                    cari = '';
                    jenis = '';
                    applyFilters();
                }}
            >
                Reset
            </Button>
        {/if}
        <Button variant="primary" onclick={openCreate}>
            <Plus class="size-4" />
            Tambah Klien
        </Button>
    </div>

    <Dialog bind:open={dialogOpen}>
        <DialogContent>
            <DialogTitle>Tambah Klien Baru</DialogTitle>
            <DialogDescription>
                Buat akun klien baru dan profil proyek.
            </DialogDescription>

            <form class="grid gap-4 py-2" onsubmit={submit}>
                <div class="grid gap-2">
                    <Label for="name">Nama Lengkap</Label>
                    <Input id="name" bind:value={form.name} required />
                    {#if form.errors.name}<p class="text-sm text-destructive">{form.errors.name}</p>{/if}
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" bind:value={form.email} required />
                    {#if form.errors.email}<p class="text-sm text-destructive">{form.errors.email}</p>{/if}
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input id="password" type="password" bind:value={form.password} required minlength="8" />
                    {#if form.errors.password}<p class="text-sm text-destructive">{form.errors.password}</p>{/if}
                </div>

                <div class="grid grid-cols-2 gap-2">
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
                        <Label>NPWP</Label>
                        <Input id="npwp" bind:value={form.npwp} />
                        {#if form.errors.npwp}<p class="text-sm text-destructive">{form.errors.npwp}</p>{/if}
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="nama_entitas">Nama Entitas</Label>
                    <Input id="nama_entitas" bind:value={form.nama_entitas} required />
                    {#if form.errors.nama_entitas}<p class="text-sm text-destructive">{form.errors.nama_entitas}</p>{/if}
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="grid gap-2">
                        <Label>Paket Layanan</Label>
                        <Select.Root type="single" bind:value={form.package_id} required>
                            <Select.Trigger class="w-full">
                                <Select.Content>
                                    {#each packages as pkg (pkg.id)}
                                        <Select.Item value={pkg.id}>{pkg.namaPaket}</Select.Item>
                                    {/each}
                                </Select.Content>
                            </Select.Trigger>
                        </Select.Root>
                        {#if form.errors.package_id}<p class="text-sm text-destructive">{form.errors.package_id}</p>{/if}
                    </div>

                    <div>
                        <Label>Harga Paket (opsional)</Label>
                        <Input type="number" min="0" bind:value={form.harga} />
                    </div>
                </div>

                <DialogFooter>
                    <Button type="button" variant="ghost" onclick={() => (dialogOpen = false)}>Batal</Button>
                    <Button type="submit" disabled={form.processing}>
                        {form.processing ? 'Membuat...' : 'Buat Klien'}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <div class="overflow-hidden rounded-xl border border-border bg-card">
    </div>

    <div class="overflow-hidden rounded-xl border border-border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-muted-foreground">
                    <th class="px-5 py-3 font-medium">Nama Entitas</th>
                    <th class="hidden px-5 py-3 font-medium md:table-cell">Email</th>
                    <th class="hidden px-5 py-3 font-medium lg:table-cell">NPWP</th>
                    <th class="px-5 py-3 font-medium">Paket</th>
                    <th class="px-5 py-3 text-right font-medium">Laporan Aktif</th>
                </tr>
            </thead>
            <tbody>
                {#if clients.data.length === 0}
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-muted-foreground">
                            Tidak ada klien yang cocok dengan filter.
                        </td>
                    </tr>
                {:else}
                    {#each clients.data as klien (klien.id)}
                        <tr class="border-b border-border/60 transition-colors last:border-0 hover:bg-muted/50">
                            <td class="px-5 py-3.5">
                                <Link
                                    href={clientsRoutes.show.url({ current_team: teamSlug, client: klien.id })}
                                    class="font-medium hover:text-gold"
                                >
                                    {klien.namaEntitas}
                                </Link>
                                <p class="text-xs text-muted-foreground md:hidden">{klien.email}</p>
                            </td>
                            <td class="hidden px-5 py-3.5 md:table-cell">{klien.email}</td>
                            <td class="hidden px-5 py-3.5 lg:table-cell">{klien.npwp ?? '-'}</td>
                            <td class="px-5 py-3.5">
                                <Badge variant="outline">{klien.paket ?? 'Tanpa Paket'}</Badge>
                            </td>
                            <td class="px-5 py-3.5 text-right">{klien.laporanAktifCount}</td>
                        </tr>
                    {/each}
                {/if}
            </tbody>
        </table>

        {#if clients.meta.lastPage > 1}
            <div class="flex items-center justify-between border-t border-border px-5 py-3">
                <span class="text-sm text-muted-foreground">
                    Halaman {clients.meta.currentPage} dari {clients.meta.lastPage}
                </span>
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        disabled={clients.meta.currentPage <= 1}
                        onclick={() => gotoPage(clients.meta.currentPage - 1)}
                    >
                        Sebelumnya
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        disabled={clients.meta.currentPage >= clients.meta.lastPage}
                        onclick={() => gotoPage(clients.meta.currentPage + 1)}
                    >
                        Berikutnya
                    </Button>
                </div>
            </div>
        {/if}
    </div>
</div>
