<script module lang="ts">
    import { dashboard } from '@/routes';
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
            },
            { title: 'Riwayat Laporan' },
        ],
    });
</script>

<script lang="ts">
    import StatusBadge from '@/components/admin/StatusBadge.svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { Card, CardContent } from '@/components/ui/card';
    import { formatDate } from '@/lib/utils';
    import type { RiwayatGroup } from '@/types';

    let {
        groups = [],
    }: {
        groups?: RiwayatGroup[];
    } = $props();
</script>

<AppHead title="Riwayat Laporan" />

<div class="mx-auto flex w-full max-w-4xl flex-col gap-6 p-4 md:p-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Riwayat Laporan</h1>
        <p class="mt-1 text-sm text-muted-foreground">
            Semua laporan yang sudah selesai dilaporkan, dikelompokkan per tahun.
        </p>
    </div>

    {#if groups.length === 0}
        <Card class="rounded-xl border border-border bg-card">
            <CardContent class="py-10 text-center text-sm text-muted-foreground">
                Belum ada laporan yang selesai.
            </CardContent>
        </Card>
    {:else}
        {#each groups as group (group.tahun)}
            <section class="overflow-hidden rounded-xl border border-border bg-card">
                <h2 class="border-b border-border px-5 py-3 font-semibold">{group.tahun}</h2>
                <ul class="divide-y divide-border">
                    {#each group.items as item (item.id)}
                        <li class="flex items-center justify-between gap-4 px-5 py-3.5">
                            <div>
                                <p class="font-medium">{item.jenisLaporan}</p>
                                <p class="mt-0.5 text-sm text-muted-foreground">
                                    Periode {item.periode}
                                    {#if item.deadline}
                                        &middot; deadline {formatDate(item.deadline)}
                                    {/if}
                                </p>
                            </div>
                            <StatusBadge status={item.status} label={item.statusLabel} />
                        </li>
                    {/each}
                </ul>
            </section>
        {/each}
    {/if}
</div>
