<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
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
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, FileUser} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();
const auth = computed(() => page.props.auth);

const allNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Clients files',
        href: '/tax-returns',
        icon: FileUser,
    },
    {
        title: 'Bookings',
        href: '/bookings',
        icon: BookOpen,
        roles: ['admin'],
    },
    {
        title: 'Availability Slots',
        href: '/availability',
        icon: Folder,
        roles: ['admin'],
    },
    {
        title: 'Manage Blogs',
        href: '/blogs',
        icon: BookOpen,
        roles: ['admin'],
    },
];

// Filter navigation items based on user role
const userRole = computed(() => auth.value?.user?.role);
const mainNavItems = computed(() => {
    return allNavItems.filter(item => {
        if (!item.roles || item.roles.length === 0) {
            return true; // No role restriction, show to everyone
        }
        return item.roles.includes(userRole.value || '');
    });
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
