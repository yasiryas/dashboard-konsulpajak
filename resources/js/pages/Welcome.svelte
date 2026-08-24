<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import ArrowRight from 'lucide-svelte/icons/arrow-right';
    import BellRing from 'lucide-svelte/icons/bell-ring';
    import ExternalLink from 'lucide-svelte/icons/external-link';
    import FileCheck2 from 'lucide-svelte/icons/file-check-2';
    import LayoutDashboard from 'lucide-svelte/icons/layout-dashboard';
    import ShieldCheck from 'lucide-svelte/icons/shield-check';
    import AppHead from '@/components/AppHead.svelte';
    import AppLogo from '@/components/AppLogo.svelte';
    import { Button } from '@/components/ui/button';
    import { toUrl } from '@/lib/utils';
    import { dashboard, login } from '@/routes';
    import { dashboard as adminDashboard } from '@/routes/admin';
    import type { Team } from '@/types';

    const auth = $derived(page.props.auth);
    const currentTeam = $derived(page.props.currentTeam as Team | null);
    const isAdmin = $derived(auth.user?.role === 'admin');
    const dashboardUrl = $derived(
        !currentTeam
            ? '/'
            : isAdmin
              ? adminDashboard(currentTeam.slug)
              : dashboard(currentTeam.slug),
    );

    const features = [
        {
            icon: ShieldCheck,
            title: 'Status Laporan Transparan',
            description:
                'Pantau progres SPT dan laporan pajak Anda kapan saja — tanpa harus bertanya via WhatsApp.',
        },
        {
            icon: BellRing,
            title: 'Reminder Deadline',
            description:
                'Pengingat otomatis H-7 dan H-1 sebelum jatuh tempo agar tidak telat lapor dan terhindar dari denda.',
        },
        {
            icon: FileCheck2,
            title: 'Dokumen Terpusat',
            description:
                'Upload dan unduh dokumen pajak dalam satu tempat yang rapi, aman, dan mudah dilacak.',
        },
    ];

    const steps = [
        {
            title: 'Masuk ke Portal',
            description: 'Login dengan akun yang diberikan konsultan Anda.',
        },
        {
            title: 'Pantau Progress',
            description:
                'Lihat status setiap laporan: diterima, dikerjakan, atau sudah dilaporkan.',
        },
        {
            title: 'Terima Hasil',
            description:
                'Unduh bukti lapor dan dokumen final langsung dari dashboard.',
        },
    ];
</script>

<AppHead title="Portal Klien KonsulPajak" />

<div class="min-h-screen bg-background text-foreground">
    <header
        class="sticky top-0 z-40 border-b border-border bg-background/90 backdrop-blur"
    >
        <div
            class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6"
        >
            <Link href="/" class="flex items-center gap-2">
                <AppLogo />
            </Link>
            <div class="flex items-center gap-3">
                {#if auth.user}
                    <Button size="default" class="px-5" asChild>
                        {#snippet children(props)}
                            <Link
                                href={toUrl(dashboardUrl)}
                                class={props.class}
                            >
                                <LayoutDashboard class="size-4" />
                                Dashboard
                            </Link>
                        {/snippet}
                    </Button>
                {:else}
                    <Button size="default" class="px-5" asChild>
                        {#snippet children(props)}
                            <Link href={toUrl(login())} class={props.class}>
                                Masuk Portal
                            </Link>
                        {/snippet}
                    </Button>
                {/if}
            </div>
        </div>
    </header>

    <section class="relative overflow-hidden">
        <div
            class="pointer-events-none absolute -top-40 left-1/2 h-96 w-[42rem] -translate-x-1/2 rounded-full bg-gold/20 blur-[120px]"
        ></div>
        <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 sm:py-28">
            <div class="mx-auto max-w-3xl text-center">
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-gold/40 bg-gold/10 px-4 py-1.5 text-xs font-medium tracking-wide text-gold-light"
                >
                    <ShieldCheck class="size-3.5" />
                    Portal Klien resmi KonsulPajak
                </span>
                <h1
                    class="mt-6 text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl"
                >
                    Satu Dashboard untuk Semua
                    <span class="text-gold">Laporan Pajak Anda</span>
                </h1>
                <p
                    class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-muted-foreground sm:text-lg"
                >
                    Website ini bukan landing page kantor — di sinilah Anda
                    memantau status pelaporan, mengelola dokumen, dan menerima
                    pengingat deadline. Kunjungi
                    <a
                        href="https://ahmadsandi.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="font-medium text-gold underline-offset-4 hover:underline"
                        >ahmadsandi.com</a
                    > untuk informasi layanan & profil kantor.
                </p>
                <div
                    class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row"
                >
                    <Button size="lg" class="w-full sm:w-auto" asChild>
                        {#snippet children(props)}
                            <Link href={toUrl(login())} class={props.class}>
                                Masuk Portal Klien
                                <ArrowRight class="size-4" />
                            </Link>
                        {/snippet}
                    </Button>
                    <a
                        href="https://ahmadsandi.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-border bg-secondary px-5 py-2.5 text-sm font-medium text-secondary-foreground transition-colors hover:bg-secondary/70 sm:w-auto"
                    >
                        Info Layanan & Harga
                        <ExternalLink class="size-4" />
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="fitur" class="border-y border-border bg-card/50 py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <span
                    class="text-xs font-semibold tracking-widest text-gold uppercase"
                >
                    Fitur Portal
                </span>
                <h2 class="mt-2 text-3xl font-bold sm:text-4xl">
                    Apa yang Bisa Anda Lakukan di Dashboard?
                </h2>
            </div>
            <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                {#each features as item (item.title)}
                    <div
                        class="rounded-xl border border-border bg-background p-6 text-center"
                    >
                        <div
                            class="mx-auto flex size-12 items-center justify-center rounded-lg border border-gold/40 bg-gold/10 text-gold"
                        >
                            <item.icon class="size-6" />
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">{item.title}</h3>
                        <p
                            class="mt-2 text-sm leading-relaxed text-muted-foreground"
                        >
                            {item.description}
                        </p>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <section id="cara-kerja" class="py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <span
                    class="text-xs font-semibold tracking-widest text-gold uppercase"
                >
                    Cara Kerja
                </span>
                <h2 class="mt-2 text-3xl font-bold sm:text-4xl">
                    Tiga Langkah Saja
                </h2>
            </div>
            <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                {#each steps as step, index (step.title)}
                    <div
                        class="rounded-xl border border-border bg-card p-6 text-center"
                    >
                        <span
                            class="mx-auto flex size-10 items-center justify-center rounded-full border border-gold/40 bg-gold/10 text-lg font-bold text-gold"
                        >
                            {index + 1}
                        </span>
                        <h3 class="mt-4 text-lg font-semibold">{step.title}</h3>
                        <p
                            class="mt-2 text-sm leading-relaxed text-muted-foreground"
                        >
                            {step.description}
                        </p>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <section class="pb-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div
                class="rounded-2xl border border-gold/40 bg-gradient-to-br from-gold/15 to-transparent p-10 text-center sm:p-14"
            >
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Sudah menjadi klien? Cek laporan Anda sekarang.
                </h2>
                <p class="mx-auto mt-3 max-w-xl text-muted-foreground">
                    Belum punya akses? Hubungi konsultan Anda untuk mendapatkan
                    akun portal.
                </p>
                <div class="mt-6 flex justify-center">
                    <Button size="lg" asChild>
                        {#snippet children(props)}
                            <Link href={toUrl(login())} class={props.class}>
                                Masuk Portal
                            </Link>
                        {/snippet}
                    </Button>
                </div>
            </div>
        </div>
    </section>

    <footer class="border-t border-border">
        <div
            class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-4 py-8 text-sm text-muted-foreground sm:flex-row sm:px-6"
        >
            <div class="flex items-center gap-2">
                <AppLogo />
            </div>
            <p>
                © {new Date().getFullYear()} KonsulPajak — Kantor Konsultan Pajak
            </p>
            <div class="flex items-center gap-4">
                <a
                    href="https://ahmadsandi.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="transition-colors hover:text-gold"
                >
                    ahmadsandi.com
                </a>
            </div>
        </div>
    </footer>
</div>
