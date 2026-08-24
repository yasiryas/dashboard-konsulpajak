<script module lang="ts">
    import { dashboard } from '@/routes';
    import type { ClientDetail, Team } from '@/types';

    export const layout = (props: {
        currentTeam?: Team | null;
        client?: ClientDetail;
    }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
            },
            {
                title: props.client?.namaEntitas ?? 'Klien',
                href: props.currentTeam
                    ? `/admin/klien/${props.client?.id}`
                    : '/',
            },
        ],
    });
</script>

<script lang="ts">
    import { Link, page, router } from '@inertiajs/svelte';
    import ArrowLeft from 'lucide-svelte/icons/arrow-left';
    import Building2 from 'lucide-svelte/icons/building-2';
    import FileText from 'lucide-svelte/icons/file-text';
    import Paperclip from 'lucide-svelte/icons/paperclip';
    import StatusBadge from '@/components/admin/StatusBadge.svelte';
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
    import { currentPath } from '@/lib/currentUrl.svelte';
    import { toUrl } from '@/lib/utils';
    import taxReportsRoutes from '@/routes/admin/taxReports';
    import type { ClientDetail, Team } from '@/types';

    let { client }: { client: ClientDetail } = $props();

    const teamSlug = $derived(currentPath().split('/')[1]);
    const currentTeam = $derived(page.props.currentTeam as Team | null);
    const dashboardUrl = $derived(
        currentTeam ? dashboard(currentTeam.slug) : '/',
    );

    const StatusLaporan = [
        { value: 'draft', label: 'Draft' },
        { value: 'menunggu_dokumen', label: 'Menunggu Dokumen' },
        { value: 'diproses', label: 'Diproses' },
        { value: 'dilaporkan', label: 'Dilaporkan' },
        { value: 'selesai', label: 'Selesai' },
    ];

    let statusUpdateOpen = $state(false);
    let statusUpdateReportId = $state<number | null>(null);
    let statusUpdateValue = $state<string>('');

    function openStatusUpdate(id: number) {
        statusUpdateReportId = id;
        const report = client.taxReports.find((r) => r.id === id);
        statusUpdateValue = report?.status ?? 'draft';
        statusUpdateOpen = true;
    }

    function submitStatusUpdate() {
        if (!statusUpdateReportId) return;

        router.put(
            toUrl(taxReportsRoutes.status.url({
                current_team: teamSlug,
                taxReport: statusUpdateReportId,
            })),
            { status: statusUpdateValue },
            {
                preserveScroll: true,
                onSuccess: () => (statusUpdateOpen = false),
            },
        );
    }
</script>

<AppHead title={client?.namaEntitas ?? 'Detail Klien'} />

    <Dialog bind:open={statusUpdateOpen}>
        <DialogContent>
            <DialogTitle>Ubah Status Laporan</DialogTitle>
            <DialogDescription>
                Pilih status baru untuk laporan ini.
            </DialogDescription>

            <div class="grid gap-2">
                {#each StatusLaporan as option (option.value)}
                    <label class="flex cursor-pointer select-none items-center gap-2 transition-colors hover:text-gold">
                        <input
                            type="radio"
                            name="status-update"
                            bind:group={statusUpdateValue}
                            value={option.value}
                        />
                        <span>{option.label}</span>
                    </label>
                {/each}
            </div>

            <DialogFooter class="justify-end">
                <Button variant="outline" onclick={() => (statusUpdateOpen = false)}>Batal</Button>
                <Button variant="primary" onclick={submitStatusUpdate}>
                    Perbarui
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <div class="flex flex-col gap-6 p-4 sm:p-6 lg:p-8">
    <Link
        href={dashboardUrl}
        class="inline-flex w-fit items-center gap-2 text-sm text-muted-foreground transition-colors hover:text-gold"
    >
        <ArrowLeft class="size-4" />
        Kembali ke Dashboard
    </Link>

    {#if client}
        <div class="rounded-xl border border-border bg-card p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-4">
                    <div
                        class="flex size-12 items-center justify-center rounded-lg border border-gold/40 bg-gold/10 text-gold"
                    >
                        <Building2 class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-foreground">
                            {client.namaEntitas}
                        </h1>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <Badge>{client.jenisKlien}</Badge>
                            {#if client.paket}
                                <Badge variant="secondary">
                                    {client.paket.nama}
                                </Badge>
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="text-sm text-muted-foreground">
                    <p><span class="font-medium text-foreground">Email:</span> {client.email}</p>
                    {#if client.npwp}
                        <p class="mt-1">
                            <span class="font-medium text-foreground">NPWP:</span>&nbsp;
                            {client.npwp}
                        </p>
                    {/if}
                </div>
            </div>
        </div>

        <h2 class="mt-2 font-semibold text-foreground">Riwayat Laporan</h2>
        <div class="space-y-4">
            {#each client.taxReports as report (report.id)}
                <div class="rounded-xl border border-border bg-card p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <FileText class="size-4 text-gold" />
                            <div>
                                <p class="font-medium text-foreground">
                                    {report.jenisLaporanLabel}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    Periode {report.periode} · Deadline&nbsp;
                                    {report.deadline ?? '—'}
                                </p>
                            </div>
                        </div>
                        <StatusBadge
                            status={report.status}
                            label={report.statusLabel}
                        />

                        <div class="flex items-center gap-2 text-xs text-muted-foreground">
                            <Button
                                variant="ghost"
                                size="sm"
                                onclick={() => openStatusUpdate(report.id)}
                            >
                                Ubah Status
                            </Button>
                        </div>
                    </div>

                    {#if report.documents.length > 0}
                        <div class="mt-4 border-t border-border pt-4">
                            <p class="mb-2 flex items-center gap-1.5 text-xs font-medium text-muted-foreground uppercase">
                                <Paperclip class="size-3.5" />
                                Dokumen ({report.documents.length})
                            </p>
                            <ul class="space-y-1.5 text-sm">
                                {#each report.documents as doc (doc.id)}
                                    <li class="flex items-center justify-between gap-2">
                                        <span class="text-foreground">
                                            {doc.namaFile}
                                        </span>
                                        <span class="text-xs text-muted-foreground">
                                            {doc.jenisDokumen}
                                        </span>
                                    </li>
                                {/each}
                            </ul>
                        </div>
                    {:else}
                        <p class="mt-4 border-t border-border pt-4 text-sm text-muted-foreground">
                            Belum ada dokumen untuk laporan ini.
                        </p>
                    {/if}
                </div>
            {:else}
                <p class="rounded-xl border border-border bg-card p-8 text-center text-sm text-muted-foreground">
                    Belum ada laporan untuk klien ini.
                </p>
            {/each}
        </div>
    {/if}
</div>