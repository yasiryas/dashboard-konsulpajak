<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import BellRing from 'lucide-svelte/icons/bell-ring';
    import CalendarDays from 'lucide-svelte/icons/calendar-days';
    import CalendarRange from 'lucide-svelte/icons/calendar-range';
    import History from 'lucide-svelte/icons/history';
    import IdCard from 'lucide-svelte/icons/id-card';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import Package from 'lucide-svelte/icons/package';
    import ReceiptText from 'lucide-svelte/icons/receipt-text';
    import Users from 'lucide-svelte/icons/users';
    import type { Snippet } from 'svelte';
    import AppLogo from '@/components/AppLogo.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarHeader,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import { dashboard } from '@/routes';
    import { dashboard as adminDashboard } from '@/routes/admin';
    import invoicesRoutes from '@/routes/admin/invoices';
    import clientsRoutes from '@/routes/admin/clients';
    import notificationsRoutes from '@/routes/admin/notifications';
    import packagesRoutes from '@/routes/admin/packages';
    import rekap from '@/routes/admin/rekap';
    import profilRoutes from '@/routes/profil';
    import riwayatRoutes from '@/routes/riwayat';
    import type { NavItem, Team } from '@/types';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    const currentTeam = $derived(page.props.currentTeam as Team | null);
    const isAdmin = $derived(page.props.auth.user?.role === 'admin');
    const dashboardUrl = $derived(
        !currentTeam
            ? '/'
            : isAdmin
              ? adminDashboard(currentTeam.slug)
              : dashboard(currentTeam.slug),
    );

    const mainNavItems = $derived<NavItem[]>(
        isAdmin && currentTeam
            ? [
                  {
                      title: 'Dashboard Admin',
                      href: adminDashboard(currentTeam.slug),
                      icon: LayoutGrid,
                  },
                  {
                      title: 'Klien',
                      href: clientsRoutes.index.url(currentTeam.slug),
                      icon: Users,
                  },
                  {
                      title: 'Invoice',
                      href: invoicesRoutes.index.url(currentTeam.slug),
                      icon: ReceiptText,
                  },
                  {
                      title: 'Paket Layanan',
                      href: packagesRoutes.index.url(currentTeam.slug),
                      icon: Package,
                  },
                  {
                      title: 'Log Notifikasi',
                      href: notificationsRoutes.index.url(currentTeam.slug),
                      icon: BellRing,
                  },
                  {
                      title: 'Rekap Bulanan',
                      href: rekap.bulanan.url(currentTeam.slug),
                      icon: CalendarRange,
                  },
                  {
                      title: 'Rekap Tahunan',
                      href: rekap.tahunan.url(currentTeam.slug),
                      icon: CalendarDays,
                  },
              ]
            : [
                  {
                      title: 'Dashboard',
                      href: dashboardUrl,
                      icon: LayoutGrid,
                  },
                  {
                      title: 'Riwayat Laporan',
                      href: riwayatRoutes.index.url(currentTeam?.slug ?? ''),
                      icon: History,
                  },
                  {
                      title: 'Profil Entitas',
                      href: profilRoutes.show.url(currentTeam?.slug ?? ''),
                      icon: IdCard,
                  },
              ],
    );
</script>

<Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton size="lg" asChild>
                    {#snippet children(props)}
                        <Link
                            {...props}
                            href={dashboardUrl}
                            class={props.class}
                        >
                            <AppLogo />
                        </Link>
                    {/snippet}
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
        <NavMain items={mainNavItems} />
    </SidebarContent>

    <SidebarFooter>
        <NavUser />
    </SidebarFooter>
</Sidebar>
{@render children?.()}
