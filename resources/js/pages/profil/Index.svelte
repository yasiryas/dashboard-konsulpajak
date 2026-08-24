<script module lang="ts">
    import { dashboard } from '@/routes';
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
            },
            { title: 'Profil' },
        ],
    });
</script>

<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Separator } from '@/components/ui/separator';
    import { currentPath } from '@/lib/currentUrl.svelte';
    import { toUrl } from '@/lib/utils';
    import profilRoutes from '@/routes/profil';
    import type { ProfilKlien } from '@/types';

    let {
        profil,
    }: {
        profil: ProfilKlien;
    } = $props();

    const teamSlug = $derived(currentPath().split('/')[1]);

    const form = useForm({
        nama_entitas: profil.namaEntitas ?? '',
    });

    function submit(event: SubmitEvent) {
        event.preventDefault();
        form.put(toUrl(profilRoutes.update(teamSlug)), { preserveScroll: true });
    }
</script>

<AppHead title="Profil" />

<div class="mx-auto flex w-full max-w-2xl flex-col gap-6 p-4 md:p-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Profil Entitas</h1>
        <p class="mt-1 text-sm text-muted-foreground">
            Data entitas yang digunakan untuk pelaporan pajak Anda.
        </p>
    </div>

    <Card class="rounded-xl border border-border bg-card">
        <CardHeader>
            <CardTitle>Informasi Paket</CardTitle>
        </CardHeader>
        <CardContent class="grid gap-3 sm:grid-cols-3">
            <div>
                <p class="text-sm text-muted-foreground">Paket Layanan</p>
                <p class="font-medium">{profil.paket?.nama ?? '-'}</p>
            </div>
            <div>
                <p class="text-sm text-muted-foreground">Harga</p>
                <p class="font-medium">{profil.paket ? `Rp ${profil.paket.harga}` : '-'}</p>
            </div>
            <div>
                <p class="text-sm text-muted-foreground">Jenis Klien</p>
                <p class="font-medium">{profil.jenisKlien ?? '-'}</p>
            </div>
        </CardContent>
    </Card>

    <Card class="rounded-xl border border-border bg-card">
        <CardHeader>
            <CardTitle>Data Entitas</CardTitle>
            <CardDescription>
                Hanya nama entitas yang bisa diubah sendiri. Untuk NPWP dan jenis klien, hubungi konsultan Anda.
            </CardDescription>
        </CardHeader>
        <CardContent>
            <form class="grid gap-4" onsubmit={submit}>
                <div class="grid gap-2">
                    <Label for="nama_entitas">Nama Entitas</Label>
                    <Input id="nama_entitas" type="text" bind:value={form.nama_entitas} required />
                    {#if form.errors.nama_entitas}
                        <p class="text-sm text-destructive">{form.errors.nama_entitas}</p>
                    {/if}
                </div>

                <div class="grid gap-2">
                    <Label>NPWP</Label>
                    <Input value={profil.npwp ?? '-'} disabled />
                </div>

                <Separator />

                <div class="flex justify-end">
                    <Button type="submit" disabled={form.processing}>
                        {form.processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</div>
