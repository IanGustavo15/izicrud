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
// import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

export type NavItem = {
    title: string;
    href: string;
    icon: any;
    nivel: number; // Added the 'nivel' property
};

const page = usePage();

const userNivel = computed(() => {
    return (page.props.auth?.user as any)?.nivel ?? 99;
});


const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href:  '/dashboard',
        icon: LayoutGrid,
        nivel: 99,
    },


// Novos Itens do Menu



];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/NicolasKovsky/izicrud',
        icon: Folder,
        nivel: 99
    },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    //     nivel: 99
    // },
    {
        title: 'Permissoes',
        href: '/permissoes',
        icon: LayoutGrid,
        nivel: 0,
    },

];
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
            <NavMain :items="mainNavItems" :UserNivel="userNivel"/>
            <!-- <NavDev :items="devNavItems" :UserNivel="0"/> -->
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" :UserNivel="userNivel" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
