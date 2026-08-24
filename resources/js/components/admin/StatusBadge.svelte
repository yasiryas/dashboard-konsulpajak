<script lang="ts">
    const styles: Record<string, { cls: string; dot: string }> = {
        draft: {
            cls: 'border-muted bg-muted text-muted-foreground',
            dot: 'bg-muted-foreground',
        },
        menunggu_dokumen: {
            cls: 'border-gold/40 bg-gold/15 text-gold-light',
            dot: 'bg-gold-light',
        },
        diproses: {
            cls: 'border-blue-500/40 bg-blue-500/15 text-blue-400',
            dot: 'bg-blue-400',
        },
        dilaporkan: {
            cls: 'border-violet-500/40 bg-violet-500/15 text-violet-400',
            dot: 'bg-violet-400',
        },
        selesai: {
            cls: 'border-green-500/40 bg-green-500/15 text-green-400',
            dot: 'bg-green-400',
        },
    };

    let {
        status,
        label,
    }: {
        status: string;
        label: string;
    } = $props();

    const style = $derived(styles[status] ?? styles.draft);
</script>

<span
    class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium {style.cls}"
>
    <span class="size-1.5 rounded-full {style.dot}"></span>
    {label}
</span>