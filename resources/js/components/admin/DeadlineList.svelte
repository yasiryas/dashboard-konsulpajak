<script lang="ts">
    import CalendarClock from 'lucide-svelte/icons/calendar-clock';
    import StatusBadge from '@/components/admin/StatusBadge.svelte';
    import type { DeadlineItem } from '@/types';

    let { deadlines = [] }: { deadlines?: DeadlineItem[] } = $props();

    const today = $derived(
        new Date(new Date().toDateString()).getTime(),
    );

    function daysLeft(deadline: string | null): number | null {
        if (!deadline) {
            return null;
        }

        return Math.round(
            (new Date(deadline).getTime() - today) / 86_400_000,
        );
    }

    function countdownLabel(deadline: string | null): string {
        const left = daysLeft(deadline);

        if (left === null) {
            return '—';
        }

        if (left === 0) {
            return 'Hari ini';
        }

        if (left < 0) {
            return 'Terlewat';
        }

        return `H-${left}`;
    }

    const urgent = $derived(
        deadlines.filter((item) => (daysLeft(item.deadline) ?? 8) <= 2),
    );
</script>

<div class="space-y-3">
    {#if deadlines.length === 0}
        <p class="py-8 text-center text-sm text-muted-foreground">
            Tidak ada deadline dalam 7 hari ke depan.
        </p>
    {:else}
        {#each deadlines as item (item.id)}
            <div
                class="flex items-start gap-3 rounded-lg border border-border bg-background p-3 {urgent.includes(
                    item,
                )
                    ? 'border-gold/50'
                    : ''}"
            >
                <div
                    class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-md border border-gold/40 bg-gold/10 text-gold"
                >
                    <CalendarClock class="size-4" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="truncate font-medium text-foreground">
                        {item.client.namaEntitas}
                    </div>
                    <div class="text-xs text-muted-foreground">
                        {item.jenisLaporan} · {item.periode} · {item.client.jenisKlien}
                    </div>
                </div>
                <div class="flex shrink-0 flex-col items-end gap-1">
                    <StatusBadge status={item.status} label={item.statusLabel} />
                    <span
                        class="text-xs font-medium {urgent.includes(item)
                            ? 'text-gold-light'
                            : 'text-muted-foreground'}"
                    >
                        {countdownLabel(item.deadline)}
                    </span>
                </div>
            </div>
        {/each}
    {/if}
</div>