<script module lang="ts">
    import { dashboard } from '@/routes';
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
            },
            { title: 'Detail Laporan' },
        ],
    });
</script>

<script lang="ts">
    import { Link, useForm } from '@inertiajs/svelte';
    import StatusBadge from '@/components/admin/StatusBadge.svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import * as Select from '@/components/ui/select';
    import { currentPath } from '@/lib/currentUrl.svelte';
    import { formatDate, toUrl } from '@/lib/utils';
    import documentsRoutes from '@/routes/laporan/documents';
    import type { LaporanDetail } from '@/types';

    let {
        laporan,
    }: {
        laporan: LaporanDetail;
    } = $props();

    const teamSlug = $derived(currentPath().split('/')[1]);

    const JENIS_DOKUMEN = [
        { value: 'bukti_potong', label: 'Bukti Potong' },
        { value: 'invoice', label: 'Invoice' },
        { value: 'npwp', label: 'NPWP' },
        { value: 'laporan_keuangan', label: 'Laporan Keuangan' },
        { value: 'lainnya', label: 'Lainnya' },
    ];

    let selectedJenis = $state('');

    const form = useForm({
        jenis_dokumen: '',
        file: null as File | null,
    });

    function submit(event: SubmitEvent) {
        event.preventDefault();

        if (!selectedJenis) {
            return;
        }

        form.jenis_dokumen = selectedJenis;
        form.post(toUrl(documentsRoutes.store.url({ current_team: teamSlug, taxReport: laporan.id })), {
            forceFormData: true,
            onSuccess: () => form.reset(),
        });
    }

    function onFileChange(event: Event) {
        const input = event.currentTarget as HTMLInputElement;
        form.file = input.files?.[0] ?? null;
    }
</script>

<AppHead title={`Laporan ${laporan.periode}`} />

<div class="mx-auto flex w-full max-w-4xl flex-col gap-6 p-4 md:p-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">
                {laporan.jenisLaporan}
            </h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Periode {laporan.periode}
                {#if laporan.deadline}
                    &middot; deadline {formatDate(laporan.deadline)}
                {/if}
            </p>
        </div>
        <StatusBadge status={laporan.status} label={laporan.statusLabel} />
    </div>

    <Card class="rounded-xl border border-border bg-card">
        <CardHeader>
            <CardTitle>Upload Dokumen</CardTitle>
            <CardDescription>
                Format: PDF, gambar, Excel, atau Word. Maksimal 10 MB.
            </CardDescription>
        </CardHeader>
        <CardContent>
            <form class="grid gap-4 sm:grid-cols-[1fr_1fr_auto]" onsubmit={submit}>
                <div class="grid gap-2">
                    <Label for="jenis">Jenis Dokumen</Label>
                    <Select.Root
                        type="single"
                        bind:value={selectedJenis}
                        required
                    >
                        <Select.Trigger id="jenis" class="w-full">
                            {selectedJenis
                                ? JENIS_DOKUMEN.find((o) => o.value === selectedJenis)?.label
                                : 'Pilih jenis dokumen'}
                        </Select.Trigger>
                        <Select.Content>
                            {#each JENIS_DOKUMEN as option (option.value)}
                                <Select.Item value={option.value}>{option.label}</Select.Item>
                            {/each}
                        </Select.Content>
                    </Select.Root>
                </div>

                <div class="grid gap-2">
                    <Label for="file">File</Label>
                    <Input
                        id="file"
                        type="file"
                        accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx,.doc,.docx"
                        onchange={onFileChange}
                    />
                </div>

                <div class="flex items-end">
                    <Button type="submit" disabled={form.processing}>
                        {form.processing ? 'Mengunggah...' : 'Upload'}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>

    <Card class="rounded-xl border border-border bg-card">
        <CardHeader>
            <CardTitle>Dokumen Terkirim ({laporan.documents.length})</CardTitle>
        </CardHeader>
        <CardContent>
            {#if laporan.documents.length === 0}
                <p class="py-6 text-center text-sm text-muted-foreground">
                    Belum ada dokumen untuk laporan ini.
                </p>
            {:else}
                <ul class="divide-y divide-border">
                    {#each laporan.documents as dokumen (dokumen.id)}
                        <li class="flex flex-col gap-1 py-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-medium">{dokumen.namaFile}</p>
                                <p class="mt-0.5 text-sm text-muted-foreground">
                                    <Badge variant="outline">{dokumen.jenisDokumen}</Badge>
                                    &middot; {dokumen.uploadedAt}
                                    {#if dokumen.uploadedBy}
                                        &middot; oleh {dokumen.uploadedBy}
                                    {/if}
                                </p>
                            </div>
                            {#if dokumen.driveFileUrl}
                                <a
                                    href={dokumen.driveFileUrl}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex h-8 shrink-0 items-center rounded-full border border-input bg-transparent px-4 text-xs font-medium transition-colors hover:bg-accent"
                                >
                                    Lihat di Drive
                                </a>
                            {/if}
                        </li>
                    {/each}
                </ul>
            {/if}
        </CardContent>
    </Card>

    <div>
        <Link
            href={dashboard(teamSlug)}
            class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
        >
            &larr; Kembali ke Dashboard
        </Link>
    </div>
</div>
