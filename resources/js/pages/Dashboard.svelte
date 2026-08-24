<script module lang="ts">
    import { dashboard } from '@/routes';
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
            },
        ],
    });
</script>

<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import StatusBadge from '@/components/admin/StatusBadge.svelte';
    import AppHead from '@/components/AppHead.svelte';
    import PendingInvitationsModal from '@/components/PendingInvitationsModal.svelte';
    import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
    import { currentPath } from '@/lib/currentUrl.svelte';
    import { formatDate } from '@/lib/utils';
    import laporanRoutes from '@/routes/laporan';
    import riwayatRoutes from '@/routes/riwayat';
    import type {
        DashboardInvitation,
        LaporanAktifItem,
        RingkasanKlien,
    } from '@/types';

    let {
        pendingInvitations = [],
        ringkasan = { menungguDokumen: 0, diproses: 0, dilaporkan: 0, deadlineTerdekat: null },
        laporanAktif = [],
    }: {
        pendingInvitations?: DashboardInvitation[];
        ringkasan?: RingkasanKlien;
        laporanAktif?: LaporanAktifItem[];
    } = $props();

    const teamSlug = $derived(currentPath().split('/')[1]);
</script>

<AppHead title="Dashboard" />

{#if pendingInvitations.length > 0}
    <PendingInvitationsModal invitations={pendingInvitations} />
{/if}

<div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Ringkasan Laporan Anda</h1>
        <p class="mt-1 text-sm text-muted-foreground">
            Pantau progres kewajiban pelaporan pajak entitas Anda.
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <Card class="rounded-xl border border-border bg-card">
            <CardHeader class="pb-2">
                <CardDescription>Menunggu Dokumen</CardDescription>
                <CardTitle class="text-3xl">{ringkasan.menungguDokumen}</CardTitle>
            </CardHeader>
        </Card>
        <Card class="rounded-xl border border-border bg-card">
            <CardHeader class="pb-2">
                <CardDescription>Sedang Diproses</CardDescription>
                <CardTitle class="text-3xl">{ringkasan.diproses}</CardTitle>
            </CardHeader>
        </Card>
        <Card class="rounded-xl border border-border bg-card">
            <CardHeader class="pb-2">
                <CardDescription>Sudah Dilaporkan</CardDescription>
                <CardTitle class="text-3xl">{ringkasan.dilaporkan}</CardTitle>
            </CardHeader>
        </Card>
        <Card class="rounded-xl border border-border bg-card">
            <CardHeader class="pb-2">
                <CardDescription>Deadline Terdekat</CardDescription>
                <CardTitle class="text-3xl">
                    {ringkasan.deadlineTerdekat ? formatDate(ringkasan.deadlineTerdekat) : '-'}
                </CardTitle>
            </CardHeader>
        </Card>
    </div>

    <div class="rounded-xl border border-border bg-card">
        <div class="flex items-center justify-between border-b border-border px-5 py-4">
            <h2 class="font-semibold">Laporan Berjalan</h2>
            <span class="text-sm text-muted-foreground">{laporanAktif.length} laporan</span>
        </div>

        {#if laporanAktif.length === 0}
            <p class="px-5 py-10 text-center text-sm text-muted-foreground">
                Belum ada laporan berjalan. Laporan baru akan muncul di sini setelah dibuat konsultan.
            </p>
        {:else}
            <ul class="divide-y divide-border">
                {#each laporanAktif as laporan (laporan.id)}
                    <li>
                        <a
                            href={laporanRoutes.show.url({ current_team: teamSlug, taxReport: laporan.id })}
                            class="flex flex-col gap-2 px-5 py-4 transition-colors hover:bg-muted/50 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div>
                                <p class="font-medium">
                                    {laporan.jenisLaporan}
                                    <span class="text-muted-foreground">&middot; {laporan.periode}</span>
                                </p>
                                <p class="mt-0.5 text-sm text-muted-foreground">
                                    {laporan.dokumenCount} dokumen
                                    {#if laporan.deadline}
                                        &middot; deadline {formatDate(laporan.deadline)}
                                    {/if}
                                </p>
                            </div>
                            <StatusBadge status={laporan.status} label={laporan.statusLabel} />
                        </a>
                    </li>
                {/each}
            </ul>
        {/if}
    </div>

    <div class="flex justify-end">
        <Link
            href={riwayatRoutes.index.url(teamSlug)}
            class="inline-flex h-9 items-center rounded-full border border-input bg-transparent px-5 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
        >
            Lihat Riwayat Laporan
        </Link>
    </div>
</div>
