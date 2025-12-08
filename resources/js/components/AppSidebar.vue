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
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Car, UserRound, Cog, LayoutDashboard, Github, Waypoints, ScrollText, Wrench } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

export type NavItem = {
    title: string;
    href: string;
    icon: any;
    nivel: number; // Added the 'nivel' property
};


const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href:  '/dashboard',
        icon: LayoutDashboard ,
        nivel: 99,
    },


{
    title: 'Clientes',
    href: '/cliente',
    icon: UserRound,
    nivel: 1,
},

{
    title: 'Veículos',
    href: '/veiculo',
    icon: Car,
    nivel: 1,
},

{
    title: 'Peças',
    href: '/peca',
    icon: Cog ,
    nivel: 1,
},

{
    title: 'Serviços',
    href: '/servico',
    icon: Wrench,
    nivel: 3,
},

{
    title: 'Pivot PecaServicos',
    href: '/pecaservico',
    icon: Waypoints,
    nivel: 2,
},

{
    title: 'Ordens de Serviço',
    href: '/ordemdeservico',
    icon: ScrollText ,
    nivel: 3,
},

{
    title: 'Pivot ServicoOrdemDeServicos',
    href: '/servicoordemdeservico',
    icon: Waypoints,
    nivel: 2,
},

// Novos Itens do Menu



];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/NicolasKovsky/izicrud',
        icon: Github ?? LayoutGrid,
        nivel: 99
    },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    //     nivel: 99
    // },
    {
        title: 'Permissões',
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
            <NavMain :items="mainNavItems" :UserNivel="0"/>
            <!-- <NavDev :items="devNavItems" :UserNivel="0"/> -->
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
