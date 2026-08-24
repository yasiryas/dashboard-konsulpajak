<script lang="ts">
    import UserPlus from 'lucide-svelte/icons/user-plus';
    import { Button } from '@/components/ui/button';
    import {
        Dialog,
        DialogContent,
        DialogDescription,
        DialogFooter,
        DialogTitle,
    } from '@/components/ui/dialog';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import admin from '@/routes/admin';
    import type { PackageOption } from '@/types';

    const jenisKlienOptions = [
        { value: 'dokter', label: 'Dokter' },
        { value: 'pengacara', label: 'Pengacara' },
        { value: 'notaris', label: 'Notaris' },
        { value: 'umkm', label: 'UMKM' },
        { value: 'pt', label: 'PT' },
        { value: 'cv', label: 'CV' },
        { value: 'yayasan', label: 'Yayasan' },
    ];

    let {
        teamSlug,
        packages = [],
    }: {
        teamSlug: string;
        packages?: PackageOption[];
    } = $props();

    let open = $state(false);
    let submitting = $state(false);
    let errors: Record<string, string> = $state({});

    let form = $state({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        nama_entitas: '',
        jenis_klien: 'dokter',
        npwp: '',
        package_id: '',
    });

    $effect(() => {
        if (!open) {
            return;
        }

        form = {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            nama_entitas: '',
            jenis_klien: 'dokter',
            npwp: '',
            package_id: '',
        };
        errors = {};
    });

    const availablePackages = $derived(
        packages.filter((pkg) => pkg.jenis_klien === form.jenis_klien),
    );

    const getCookie = (name: string): string | undefined => {
        if (typeof document === 'undefined') {
            return undefined;
        }

        const match = document.cookie.match(new RegExp(`(?:^|; )${name}=([^;]*)`));

        return match ? decodeURIComponent(match[1]) : undefined;
    };

    async function submit() {
        submitting = true;
        errors = {};

        try {
            const response = await fetch(admin.clients.store.url(teamSlug), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') ?? '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(form),
            });

            if (response.ok) {
                open = false;
                window.location.reload();

                return;
            }

            const payload = await response.json();

            if (payload.errors) {
                errors = payload.errors;
            }
        } finally {
            submitting = false;
        }
    }
</script>

<Button size="sm" onclick={() => (open = true)}>
    <UserPlus class="size-4" />
    Tambah Klien
</Button>

<Dialog bind:open>
    <DialogContent>
        <header>
            <DialogTitle>Tambah Klien Baru</DialogTitle>
            <DialogDescription>
                Buat akun klien beserta profilnya. Klien otomatis mendapat akses ke
                portal.
            </DialogDescription>
        </header>

        <form
            class="grid gap-4 py-2"
            onsubmit={(event) => {
                event.preventDefault();
                submit();
            }}
        >
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="grid gap-1.5">
                    <Label>Nama</Label>
                    <Input bind:value={form.name} placeholder="Nama klien" />
                    {#if errors.name}
                        <p class="text-xs text-destructive">{errors.name}</p>
                    {/if}
                </div>
                <div class="grid gap-1.5">
                    <Label>Nama Entitas</Label>
                    <Input bind:value={form.nama_entitas} placeholder="CV Sejahtera" />
                    {#if errors.nama_entitas}
                        <p class="text-xs text-destructive">{errors.nama_entitas}</p>
                    {/if}
                </div>
            </div>

            <div class="grid gap-1.5">
                <Label>Email</Label>
                <Input bind:value={form.email} type="email" placeholder="klien@email.com" />
                {#if errors.email}
                    <p class="text-xs text-destructive">{errors.email}</p>
                {/if}
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="grid gap-1.5">
                    <Label>Password</Label>
                    <Input bind:value={form.password} type="password" placeholder="Min. 8 karakter" />
                    {#if errors.password}
                        <p class="text-xs text-destructive">{errors.password}</p>
                    {/if}
                </div>
                <div class="grid gap-1.5">
                    <Label>Konfirmasi Password</Label>
                    <Input bind:value={form.password_confirmation} type="password" placeholder="Ulangi password" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="grid gap-1.5">
                    <Label>Jenis Klien</Label>
                    <select
                        bind:value={form.jenis_klien}
                        class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background"
                    >
                        {#each jenisKlienOptions as opt (opt.value)}
                            <option value={opt.value}>{opt.label}</option>
                        {/each}
                    </select>
                    {#if errors.jenis_klien}
                        <p class="text-xs text-destructive">{errors.jenis_klien}</p>
                    {/if}
                </div>
                <div class="grid gap-1.5">
                    <Label>Paket Layanan</Label>
                    <select
                        bind:value={form.package_id}
                        class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background"
                    >
                        <option value="">Tanpa paket</option>
                        {#each availablePackages as pkg (pkg.id)}
                            <option value={pkg.id}>{pkg.nama_paket}</option>
                        {/each}
                    </select>
                    {#if errors.package_id}
                        <p class="text-xs text-destructive">{errors.package_id}</p>
                    {/if}
                </div>
            </div>

            <div class="grid gap-1.5">
                <Label>NPWP</Label>
                <Input bind:value={form.npwp} placeholder="00.000.000.0-000.000" />
                {#if errors.npwp}
                    <p class="text-xs text-destructive">{errors.npwp}</p>
                {/if}
            </div>

            <DialogFooter class="mt-2">
                <Button
                    type="button"
                    variant="outline"
                    onclick={() => (open = false)}
                >
                    Batal
                </Button>
                <Button type="submit" disabled={submitting}>
                    {submitting ? 'Menyimpan...' : 'Simpan Klien'}
                </Button>
            </DialogFooter>
        </form>
    </DialogContent>
</Dialog>