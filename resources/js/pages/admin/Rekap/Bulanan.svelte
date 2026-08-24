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
            { title: 'Rekap Bulanan', href: '/admin/rekap/bulanan' },
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
    import type { RecapBulanan } from '@/types';

    let {
        recap,
        years = [],
    }: {
        recap: RecapBulanan;
        years?: number[];
    } = $props();

    const MONTHS = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ];

    const currentTeam = $derived(page.props.currentTeam as Team | null);
    const teamSlug = $derived(currentTeam?.slug ?? '');

    // Nilai awal filter diambil dari periode hasil render pertama saja.
    // svelte-ignore state_referenced_locally
    let bulan = $state(Number(recap.periode.slice(5, 7)));
    // svelte-ignore state_referenced_locally
    let tahun = $state(Number(recap.periode.slice(0, 4)));

    const periode = $derived(`${tahun}-${String(bulan).padStart(2, '0')}`);
    const pdfUrl = $derived(
        teamSlug ? rekap.bulanan.pdf.url(teamSlug, { query: { periode } }) : '',
    );

    function applyFilter() {
        router.visit(rekap.bulanan.url(teamSlug, { query: { periode } }), {
            preserveState: true,
            preserveScroll: true,
        });
    }
</script>

<AppHead title="Rekap Bulanan" />

<div class="flex flex-col gap-6 p-4 sm:p-6 lg:p-8">
    <div
        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <div>
            <h1 class="text-2xl font-bold text-foreground">Rekap Bulanan</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Rekap semua laporan pajak klien pada satu bulan — siap dicetak
                ke PDF.
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
            bind:value={bulan}
            onchange={applyFilter}
            aria-label="Pilih bulan"
            class="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
        >
            {#each MONTHS as label, index (label)}
                <option value={index + 1}>{label}</option>
            {/each}
        </select>
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
        <span class="text-sm text-muted-foreground">
            Periode: {recap.periodeLabel}
        </span>
    </div>

    <div class="overflow-x-auto rounded-xl border border-border bg-card">
        <table class="w-full min-w-[640px] text-sm">
            <thead>
                <tr
                    class="border-b border-border text-left text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                >
                    <th class="w-12 px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama Entitas</th>
                    <th class="px-4 py-3">Jenis Klien</th>
                    <th class="px-4 py-3">Jenis Laporan</th>
                    <th class="px-4 py-3">Deadline</th>
                </tr>
            </thead>
            <tbody>
                {#each recap.sections as section (section.status)}
                    <tr class="border-b border-border bg-muted/40">
                        <td colspan="5" class="px-4 py-2">
                            <StatusBadge
                                status={section.status}
                                label="{section.label} ({section.count})"
                            />
                        </td>
                    </tr>
                    {#each section.rows as row (row.no)}
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
                            <td class="px-4 py-3 text-muted-foreground">
                                {row.jenisKlien}
                            </td>
                            <td class="px-4 py-3">{row.jenisLaporan}</td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {row.deadline ?? '—'}
                            </td>
                        </tr>
                    {/each}
                {:else}
                    <tr>
                        <td
                            colspan="5"
                            class="px-4 py-10 text-center text-muted-foreground"
                        >
                            Tidak ada laporan pada periode ini.
                        </td>
                    </tr>
                {/each}
                <tr class="bg-muted/60 font-semibold">
                    <td colspan="4" class="px-4 py-3 text-right"
                        >Total Laporan</td
                    >
                    <td class="px-4 py-3 text-center">{recap.total}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
