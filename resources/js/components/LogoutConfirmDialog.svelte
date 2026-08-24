<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import LogOut from 'lucide-svelte/icons/log-out';
    import { Button } from '@/components/ui/button';
    import {
        Dialog,
        DialogContent,
        DialogDescription,
        DialogFooter,
        DialogTitle,
    } from '@/components/ui/dialog';
    import { toUrl } from '@/lib/utils';
    import { logout } from '@/routes';

    let {
        open = $bindable(false),
    }: {
        open?: boolean;
    } = $props();

    function confirmLogout() {
        open = false;
        router.flushAll();
        router.visit(toUrl(logout()), { method: 'post' });
    }
</script>

<Dialog bind:open>
    <DialogContent class="max-w-sm">
        <DialogTitle>Keluar dari portal?</DialogTitle>
        <DialogDescription>
            Sesi Anda akan diakhiri. Anda perlu login kembali untuk mengakses
            dashboard.
        </DialogDescription>
        <DialogFooter class="gap-2">
            <Button variant="outline" onclick={() => (open = false)}>
                Batal
            </Button>
            <Button variant="destructive" onclick={confirmLogout}>
                <LogOut class="size-4" />
                Ya, keluar
            </Button>
        </DialogFooter>
    </DialogContent>
</Dialog>
