<script module lang="ts">
    import { dashboard } from '@/routes';
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
            },
            { title: 'Log Notifikasi' },
        ],
    });
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import StatusBadge from '@/components/admin/StatusBadge.svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import * as Select from '@/components/ui/select';
    import { currentPath } from '@/lib/currentUrl.svelte';
    import type { NotificationLogRow, Paginated } from '@/types';

    let {
        logs,
        filters,
    }: {
        logs: Paginated<NotificationLogRow>;
        filters: { status: string; tipe: string };
    } = $props();

    const STATUS_OPTIONS = [
        { value: 'pending', label: 'Pending' },
        { value: 'terkirim', label: 'Terkirim' },
        { value: 'gagal', label: 'Gagal' },
    ];

    const TIPE_OPTIONS = [
        { value: 'reminder_deadline', label: 'Reminder Deadline' },
        { value: 'status_update', label: 'Update Status Laporan' },
    ];

    let status = $state(filters.status);
    let tipe = $state(filters.tipe);

    function applyFilters() {
        router.get(
            currentPath(),
            { status: status || undefined, tipe: tipe || undefined },
            { preserveState: true, preserveScroll: true },
        );
    }

    function gotoPage(targetPage: number) {
        router.get(
            currentPath(),
            { status: status || undefined, tipe: tipe || undefined, page: targetPage },
            { preserveState: true, preserveScroll: true },
        );
    }
</script>

<AppHead title="Log Notifikasi" />

<div class="flex flex-col gap-6 p-4 sm:p-6 lg:p-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Log Notifikasi</h1>
            <p class="mt-1 text-sm text-muted-foreground">{logs.meta.total} notifikasi WhatsApp tercatat.</p>
        </div>
        <div class="flex gap-3">
            <Select.Root
                type="single"
                bind:value={status}
                onValueChange={() => applyFilters()}
            >
                <Select.Trigger class="w-full sm:w-40">
                    {status
                        ? STATUS_OPTIONS.find((o) => o.value === status)?.label
                        : 'Semua Status'}
                </Select.Trigger>
                <Select.Content>
                    {#each STATUS_OPTIONS as option (option.value)}
                        <Select.Item value={option.value}>{option.label}</Select.Item>
                    {/each}
                </Select.Content>
            </Select.Root>

            <Select.Root
                type="single"
                bind:value={tipe}
                onValueChange={() => applyFilters()}
            >
                <Select.Trigger class="w-full sm:w-48">
                    {tipe
                        ? TIPE_OPTIONS.find((o) => o.value === tipe)?.label
                        : 'Semua Tipe'}
                </Select.Trigger>
                <Select.Content>
                    {#each TIPE_OPTIONS as option (option.value)}
                        <Select.Item value={option.value}>{option.label}</Select.Item>
                    {/each}
                </Select.Content>
            </Select.Root>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-muted-foreground">
                    <th class="px-5 py-3 font-medium">Klien</th>
                    <th class="px-5 py-3 font-medium">Tipe</th>
                    <th class="hidden px-5 py-3 font-medium md:table-cell">Status</th>
                    <th class="hidden px-5 py-3 font-medium lg:table-cell">Terkirim</th>
                    <th class="px-5 py-3 font-medium">Dibuat</th>
                </tr>
            </thead>
            <tbody>
                {#if logs.data.length === 0}
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-muted-foreground">
                            Tidak ada notifikasi yang cocok dengan filter.
                        </td>
                    </tr>
                {:else}
                    {#each logs.data as log (log.id)}
                        <tr class="border-b border-border/60 transition-colors last:border-0 hover:bg-muted/50">
                            <td class="px-5 py-3.5 font-medium">{log.klien ?? '-'}</td>
                            <td class="px-5 py-3.5">{log.tipe}</td>
                            <td class="hidden px-5 py-3.5 md:table-cell">
                                <StatusBadge status={log.status} label={log.statusLabel} />
                            </td>
                            <td class="hidden px-5 py-3.5 lg:table-cell">{log.sentAt ?? '-'}</td>
                            <td class="px-5 py-3.5 text-muted-foreground">{log.createdAt}</td>
                        </tr>
                    {/each}
                {/if}
            </tbody>
        </table>

        {#if logs.meta.lastPage > 1}
            <div class="flex items-center justify-between border-t border-border px-5 py-3">
                <span class="text-sm text-muted-foreground">
                    Halaman {logs.meta.currentPage} dari {logs.meta.lastPage}
                </span>
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        disabled={logs.meta.currentPage <= 1}
                        onclick={() => gotoPage(logs.meta.currentPage - 1)}
                    >
                        Sebelumnya
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        disabled={logs.meta.currentPage >= logs.meta.lastPage}
                        onclick={() => gotoPage(logs.meta.currentPage + 1)}
                    >
                        Berikutnya
                    </Button>
                </div>
            </div>
        {/if}
    </div>
</div>
