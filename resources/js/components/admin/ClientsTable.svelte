<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import Search from 'lucide-svelte/icons/search';
    import StatusBadge from '@/components/admin/StatusBadge.svelte';
    import { Input } from '@/components/ui/input';
    import { toUrl } from '@/lib/utils';
    import admin from '@/routes/admin';
    import type { ClientRow } from '@/types';

    const statusOptions = [
        { value: 'draft', label: 'Draft' },
        { value: 'menunggu_dokumen', label: 'Menunggu Dokumen' },
        { value: 'diproses', label: 'Diproses' },
        { value: 'dilaporkan', label: 'Dilaporkan' },
        { value: 'selesai', label: 'Selesai' },
    ];

    let {
        teamSlug,
        clients = [],
    }: {
        teamSlug: string;
        clients?: ClientRow[];
    } = $props();

    let search = $state('');
    let filterJenis = $state('semua');
    let filterStatus = $state('semua');

    const jenisOptions = $derived(
        ['semua', ...new Set(clients.map((client) => client.jenisKlien))].map(
            (value) => ({
                value,
                label: value === 'semua' ? 'Semua Jenis' : value,
            }),
        ),
    );

    const filtered = $derived(
        clients.filter((client) => {
            const q = search.trim().toLowerCase();
            const matchesSearch =
                !q ||
                client.namaEntitas.toLowerCase().includes(q) ||
                client.email.toLowerCase().includes(q);
            const matchesJenis =
                filterJenis === 'semua' || client.jenisKlien === filterJenis;
            const matchesStatus =
                filterStatus === 'semua' ||
                client.laporanTerakhir?.status === filterStatus;

            return matchesSearch && matchesJenis && matchesStatus;
        }),
    );

    function updateStatus(client: ClientRow, status: string) {
        const report = client.laporanTerakhir;

        if (!report) {
            return;
        }

        router.put(admin.taxReports.status.url([teamSlug, report.id]), {
            status,
        }, {
            preserveScroll: true,
        });
    }
</script>

<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
            <Input
                bind:value={search}
                type="search"
                placeholder="Cari nama entitas atau email..."
                class="pl-9"
            />
        </div>
        <select
            bind:value={filterJenis}
            class="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background"
        >
            {#each jenisOptions as opt (opt.value)}
                <option value={opt.value}>{opt.label}</option>
            {/each}
        </select>
        <select
            bind:value={filterStatus}
            class="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background"
        >
            <option value="semua">Semua Status</option>
            {#each statusOptions as opt (opt.value)}
                <option value={opt.value}>{opt.label}</option>
            {/each}
        </select>
    </div>

    <div class="overflow-x-auto rounded-xl border border-border">
        <table class="w-full min-w-[720px] text-sm">
            <thead>
                <tr class="border-b border-border text-left text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                    <th class="px-4 py-3">Klien</th>
                    <th class="px-4 py-3">Jenis</th>
                    <th class="px-4 py-3">Paket</th>
                    <th class="px-4 py-3">Laporan Terakhir</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {#each filtered as client (client.id)}
                    <tr class="border-b border-border transition-colors last:border-0 hover:bg-muted/40">
                        <td class="px-4 py-3">
                            <div class="font-medium text-foreground">
                                {client.namaEntitas}
                            </div>
                            <div class="text-xs text-muted-foreground">
                                {client.email}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-muted-foreground">
                                {client.jenisKlien}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {client.paket ?? '—'}
                        </td>
                        <td class="px-4 py-3">
                            {#if client.laporanTerakhir}
                                <div class="text-foreground">
                                    {client.laporanTerakhir.jenisLaporan}
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    {client.laporanTerakhir.periode}
                                </div>
                            {:else}
                                <span class="text-muted-foreground">—</span>
                            {/if}
                        </td>
                        <td class="px-4 py-3">
                            {#if client.laporanTerakhir}
                                <div class="flex items-center gap-2">
                                    <StatusBadge
                                        status={client.laporanTerakhir.status}
                                        label={client.laporanTerakhir.statusLabel}
                                    />
                                    <select
                                        value={client.laporanTerakhir.status}
                                        onchange={(event) =>
                                            updateStatus(
                                                client,
                                                event.currentTarget.value,
                                            )}
                                        aria-label="Ubah status"
                                        class="h-7 rounded-md border border-border bg-background px-1.5 text-xs text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    >
                                        {#each statusOptions as opt (opt.value)}
                                            <option value={opt.value}>
                                                {opt.label}
                                            </option>
                                        {/each}
                                    </select>
                                </div>
                                {#if client.laporanTerakhir.deadline}
                                    <div class="mt-1 text-xs text-muted-foreground">
                                        Deadline: {client.laporanTerakhir.deadline}
                                    </div>
                                {/if}
                            {:else}
                                <span class="text-muted-foreground">Tidak ada laporan</span>
                            {/if}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <Link
                                href={toUrl(
                                    admin.clients.show.get([teamSlug, client.id]),
                                )}
                                class="text-sm font-medium text-gold hover:text-gold-light hover:underline"
                            >
                                Detail
                            </Link>
                        </td>
                    </tr>
                {:else}
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-muted-foreground">
                            Tidak ada klien yang cocok dengan filter.
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>

    <p class="text-xs text-muted-foreground">
        Menampilkan {filtered.length} dari {clients.length} klien.
    </p>
</div>