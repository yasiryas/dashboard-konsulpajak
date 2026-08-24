<script module lang="ts">
    import { dashboard } from '@/routes';
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam
                    ? dashboard(props.currentTeam.slug)
                    : '/',
            },
            { title: 'Rekap Tahunan', href: '/admin/rekap/tahunan' },
        ],
    });
</script>

<script lang="ts">
    import { page, router } from '@inertiajs/svelte';
    import Printer from 'lucide-svelte/icons/printer';
    import StatusBadge from '@/components/admin/StatusBadge.svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Button from '@/components/ui/button/Button.svelte';
    import rekap from '@/routes/admin/rekap';
    import type { RecapTahunan } from '@/types';

    let {
        recap,
        years = [],
    }: {
        recap: RecapTahunan;
        years?: number[];
    } = $props();

    const currentTeam = $derived(page.props.currentTeam as Team | null);
    const teamSlug = $derived(currentTeam?.slug ?? '');

    // Nilai awal filter diambil dari tahun hasil render pertama saja.
    // svelte-ignore state_referenced_locally
    let tahun = $state(recap.year);

    const pdfUrl = $derived(
        teamSlug ? rekap.tahunan.pdf.url(teamSlug, { query: { tahun } }) : '',
    );

    function applyFilter() {
        router.visit(rekap.tahunan.url(teamSlug, { query: { tahun } }), {
            preserveState: true,
            preserveScroll: true,
        });
    }
</script>

<AppHead title="Rekap Tahunan" />

<div class="flex flex-col gap-6 p-4 sm:p-6 lg:p-8">
    <div
        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <div>
            <h1 class="text-2xl font-bold text-foreground">Rekap Tahunan</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Matriks SPT Masa 12 bulan per klien dan daftar SPT Tahunan —
                siap dicetak ke PDF.
            </p>
        </div>
        <a href={pdfUrl} target="_blank" rel="noopener">
            <Button>
                <Printer class="size-4"></Printer>
                Cetak PDF
            </Button>
        </a>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <select
            bind:value={tahun}
            onchange={applyFilter}
            aria-label="Pilih tahun"
            class="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
        >
            {#each years as year (year)}
                <option value={year}>{year}</option>
            {/each}
        </select>
        <span class="text-sm text-muted-foreground">Tahun: {recap.year}</span>
    </div>

    <div class="overflow-x-auto rounded-xl border border-border bg-card p-5">
        <h2 class="mb-4 font-semibold text-foreground">SPT Masa per Bulan</h2>
        <div class="overflow-x-auto rounded-lg border border-border">
            <table class="w-full min-w-[900px] text-sm">
                <thead>
                    <tr
                        class="border-b border-border text-left text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                    >
                        <th class="w-12 px-3 py-3">No</th>
                        <th class="px-3 py-3">Nama Entitas</th>
                        {#each recap.months as month (month)}
                            <th class="px-2 py-3 text-center">{month}</th>
                        {/each}
                        <th class="px-3 py-3 text-center">S+L</th>
                    </tr>
                </thead>
                <tbody>
                    {#each recap.rows as row (row.no)}
                        <tr
                            class="border-b border-border transition-colors last:border-0 hover:bg-muted/40"
                        >
                            <td
                                class="px-3 py-2.5 text-center text-muted-foreground"
                            >
                                {row.no}
                            </td>
                            <td class="px-3 py-2.5 font-medium text-foreground">
                                {row.namaEntitas}
                            </td>
                            {#each row.cells as code, index (index)}
                                <td
                                    class="px-2 py-2.5 text-center {code
                                        ? 'text-foreground'
                                        : 'text-muted-foreground/50'}"
                                >
                                    {code ?? '-'}
                                </td>
                            {/each}
                            <td class="px-3 py-2.5 text-center font-medium">
                                {row.selesaiCount}/12
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td
                                colspan="15"
                                class="px-4 py-10 text-center text-muted-foreground"
                            >
                                Tidak ada data.
                            </td>
                        </tr>
                    {/each}
                    <tr class="bg-muted/60 font-semibold">
                        <td colspan="2" class="px-3 py-3 text-right">
                            Jumlah Dilaporkan (L + S)
                        </td>
                        {#each recap.monthlyReported as count, index (index)}
                            <td class="px-2 py-3 text-center">{count}</td>
                        {/each}
                        <td class="px-3 py-3 text-center">
                            {recap.monthlyReported.reduce((a, b) => a + b, 0)}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="mt-3 text-xs text-muted-foreground">
            Keterangan: S = Selesai, L = Dilaporkan, P = Diproses, M = Menunggu
            Dokumen, D = Draft, - = Tidak ada laporan. Total SPT Masa
            {recap.year}: {recap.totalMasa} laporan.
        </p>
    </div>

    <div class="overflow-x-auto rounded-xl border border-border bg-card p-5">
        <h2 class="mb-4 font-semibold text-foreground">SPT Tahunan</h2>
        <div class="overflow-x-auto rounded-lg border border-border">
            <table class="w-full min-w-[640px] text-sm">
                <thead>
                    <tr
                        class="border-b border-border text-left text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                    >
                        <th class="w-12 px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Entitas</th>
                        <th class="px-4 py-3">Jenis Laporan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Deadline</th>
                    </tr>
                </thead>
                <tbody>
                    {#each recap.annualReports as row (row.no)}
                        <tr
                            class="border-b border-border transition-colors last:border-0 hover:bg-muted/40"
                        >
                            <td
                                class="px-4 py-3 text-center text-muted-foreground"
                            >
                                {row.no}
                            </td>
                            <td class="px-4 py-3 font-medium text-foreground">
                                {row.namaEntitas}
                            </td>
                            <td class="px-4 py-3">{row.jenisLaporan}</td>
                            <td class="px-4 py-3">
                                <StatusBadge
                                    status={row.status}
                                    label={row.statusLabel}
                                />
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {row.deadline ?? '—'}
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td
                                colspan="5"
                                class="px-4 py-10 text-center text-muted-foreground"
                            >
                                Tidak ada SPT Tahunan pada tahun ini.
                            </td>
                        </tr>
                    {/each}
                    <tr class="bg-muted/60 font-semibold">
                        <td colspan="3" class="px-4 py-3 text-right">
                            Total SPT Tahunan
                        </td>
                        <td colspan="2" class="px-4 py-3 text-center">
                            {recap.annualReports.length}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
