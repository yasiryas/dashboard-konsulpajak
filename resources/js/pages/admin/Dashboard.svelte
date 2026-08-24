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
    import { page } from '@inertiajs/svelte';
    import CalendarClock from 'lucide-svelte/icons/calendar-clock';
    import FileClock from 'lucide-svelte/icons/file-clock';
    import FileSearch from 'lucide-svelte/icons/file-search';
    import Users from 'lucide-svelte/icons/users';
    import AdminRealtime from '@/components/admin/AdminRealtime.svelte';
    import ClientsTable from '@/components/admin/ClientsTable.svelte';
    import CreateClientDialog from '@/components/admin/CreateClientDialog.svelte';
    import DeadlineList from '@/components/admin/DeadlineList.svelte';
    import StatCard from '@/components/admin/StatCard.svelte';
    import AppHead from '@/components/AppHead.svelte';
    import admin from '@/routes/admin';
    import type {
        ClientRow,
        DashboardStats,
        DeadlineItem,
        PackageOption,
    } from '@/types';

    let {
        stats = undefined,
        clients = [],
        deadlines = [],
        packages = [],
    }: {
        stats: DashboardStats | undefined;
        clients: ClientRow[];
        deadlines: DeadlineItem[];
        packages?: PackageOption[];
    } = $props();

    const currentTeam = $derived(page.props.currentTeam as Team | null);
    const teamSlug = $derived(currentTeam?.slug ?? '');
    const realtimeUrl = $derived(
        currentTeam ? admin.realtime.url(currentTeam.slug) : '',
    );

    const statsArray = $derived([
        {
            title: 'Total Klien',
            value: stats?.totalKlien ?? 0,
            icon: Users,
        },
        {
            title: 'Laporan Berjalan',
            value: stats?.laporanBerjalan ?? 0,
            icon: FileClock,
        },
        {
            title: 'Butuh Dokumen',
            value: stats?.butuhDokumen ?? 0,
            icon: FileSearch,
        },
        {
            title: 'Deadline 7 Hari',
            value: stats?.deadlineTerdekat ?? 0,
            icon: CalendarClock,
        },
    ]);
</script>

<AppHead title="Dashboard Admin" />

{#if currentTeam}
    <AdminRealtime url={realtimeUrl} />
{/if}

<div class="flex flex-col gap-6 p-4 sm:p-6 lg:p-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-foreground">Dashboard Admin</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Ringkasan klien, status laporan, dan deadline mendatang — diperbarui
                realtime.
            </p>
        </div>
        <CreateClientDialog {teamSlug} {packages} />
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {#each statsArray as card (card.title)}
            {@const CardIcon = card.icon}
            <StatCard title={card.title} value={card.value}>
                {#snippet icon()}
                    <CardIcon class="size-5"></CardIcon>
                {/snippet}
            </StatCard>
        {/each}
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-xl border border-border bg-card p-5 xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold text-foreground">Daftar Klien</h2>
                <span class="text-xs text-muted-foreground">
                    Update status laporan langsung dari tabel
                </span>
            </div>
            <ClientsTable {teamSlug} clients={clients ?? []} />
        </div>

        <div class="rounded-xl border border-border bg-card p-5">
            <h2 class="mb-4 font-semibold text-foreground">Deadline 7 Hari</h2>
            <DeadlineList deadlines={deadlines ?? []} />
        </div>
    </div>
</div>