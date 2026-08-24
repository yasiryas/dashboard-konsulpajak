<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { CircleCheck } from 'lucide-svelte';
    import type { Snippet } from 'svelte';
    import AppLogoIcon from '@/components/AppLogoIcon.svelte';
    import { home } from '@/routes';

    let {
        title = '',
        description = '',
        children,
    }: {
        title?: string;
        description?: string;
        children?: Snippet;
    } = $props();

    const appName = $derived(page.props.name);
    const highlights = [
        'Konsultasi perpajakan oleh ahli',
        'Perencanaan pajak yang efisien',
        'Kepatuhan SPT selalu terpantau',
    ];
</script>

<div
    class="flex min-h-svh items-center justify-center bg-muted p-4 sm:p-6 lg:p-10"
>
    <div
        class="grid w-full max-w-5xl overflow-hidden rounded-2xl border bg-card text-card-foreground shadow-xl lg:grid-cols-2"
    >
        <!-- Panel gambar di dalam card -->
        <div class="relative hidden min-h-[560px] lg:block">
            <img
                src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2071&auto=format&fit=crop"
                alt="Kalkulator dan dokumen perpajakan"
                class="absolute inset-0 h-full w-full object-cover"
            />
            <div
                class="absolute inset-0 bg-gradient-to-t from-zinc-950/90 via-zinc-950/55 to-zinc-950/20"
            ></div>

            <div
                class="relative z-10 flex h-full flex-col justify-between p-8 xl:p-12"
            >
                <Link
                    href={home()}
                    class="flex w-fit cursor-pointer items-center gap-2"
                >
                    <AppLogoIcon class="size-8 fill-current text-white" />
                    <span
                        class="text-lg font-semibold tracking-wide text-white"
                    >
                        {appName}
                    </span>
                </Link>

                <div class="space-y-8">
                    <div class="space-y-3">
                        <h2
                            class="text-balance text-3xl leading-snug font-bold text-white xl:text-4xl"
                        >
                            Kelola kewajiban pajak Anda dengan lebih mudah
                        </h2>
                        <p
                            class="text-pretty text-sm leading-relaxed text-white/80"
                        >
                            Dashboard konsultasi perpajakan terpadu untuk
                            individu dan perusahaan, dipantau langsung oleh
                            konsultan berlisensi.
                        </p>
                    </div>

                    <ul class="space-y-3">
                        {#each highlights as highlight (highlight)}
                            <li
                                class="flex items-center gap-3 text-sm text-white/90"
                            >
                                <span
                                    class="flex size-6 shrink-0 items-center justify-center rounded-full bg-white/15"
                                >
                                    <CircleCheck class="size-4 text-white" />
                                </span>
                                {highlight}
                            </li>
                        {/each}
                    </ul>
                </div>
            </div>
        </div>

        <!-- Panel form -->
        <div
            class="flex items-center justify-center px-6 py-10 sm:px-10 lg:px-12"
        >
            <div class="w-full max-w-sm">
                <Link
                    href={home()}
                    class="mb-8 flex cursor-pointer items-center justify-center gap-2 lg:hidden"
                >
                    <AppLogoIcon
                        class="size-8 fill-current text-black dark:text-white"
                    />
                    <span class="text-lg font-semibold">{appName}</span>
                </Link>

                <div class="mb-8 space-y-2 text-center">
                    <h1 class="text-2xl font-bold tracking-tight">{title}</h1>
                    <p class="text-balance text-sm text-muted-foreground">
                        {description}
                    </p>
                </div>

                {@render children?.()}
            </div>
        </div>
    </div>
</div>
