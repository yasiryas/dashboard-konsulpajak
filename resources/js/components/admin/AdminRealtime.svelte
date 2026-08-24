<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { onDestroy, onMount } from 'svelte';

    let { url }: { url: string } = $props();

    let es: EventSource | null = null;

    function refresh() {
        router.reload({ only: ['stats', 'clients', 'deadlines'] });
    }

    onMount(() => {
        es = new EventSource(url);
        es.addEventListener('refresh', refresh);
    });

    onDestroy(() => {
        es?.removeEventListener('refresh', refresh);
        es?.close();
        es = null;
    });
</script>