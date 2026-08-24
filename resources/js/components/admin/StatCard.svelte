<script lang="ts">
    import type { Snippet } from 'svelte';
    import { cn } from '@/lib/utils';

    let {
        title,
        value,
        icon,
        class: className = '',
    }: {
        title: string;
        value: number | string;
        icon: Snippet;
        class?: string;
    } = $props();
</script>

<div
    class={cn(
        'rounded-xl border border-border bg-card p-5 transition-colors hover:border-gold/40',
        className,
    )}
>
    <div
        class="flex size-10 items-center justify-center rounded-lg border border-gold/40 bg-gold/10 text-gold"
    >
        {@render icon()}
    </div>
    <p class="mt-4 text-2xl font-bold text-foreground">{value}</p>
    <p class="mt-0.5 text-sm text-muted-foreground">{title}</p>
</div>