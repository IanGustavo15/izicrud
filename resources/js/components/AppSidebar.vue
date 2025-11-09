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
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
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
        icon: LayoutGrid,
        nivel: 99,
    },


{
    title: 'Simulados',
    href: '/simulado',
    icon: LayoutGrid,
    nivel: 0,
},

{
    title: 'Questaos',
    href: '/questao',
    icon: LayoutGrid,
    nivel: 0,
},

{
    title: 'Ranks',
    href: '/rank',
    icon: LayoutGrid,
    nivel: 0,
},

{
    title: 'Opcaos',
    href: '/opcao',
    icon: LayoutGrid,
    nivel: 0,
},

{
    title: 'NotaCortes',
    href: '/notacorte',
    icon: LayoutGrid,
    nivel: 0,
},

{
    title: 'Metricas',
    href: '/metrica',
    icon: LayoutGrid,
    nivel: 0,
},

{
    title: 'Inscricaos',
    href: '/inscricao',
    icon: LayoutGrid,
    nivel: 0,
},

{
    title: 'Respostas',
    href: '/resposta',
    icon: LayoutGrid,
    nivel: 0,
},

{
    title: 'Resultados',
    href: '/resultado',
    icon: LayoutGrid,
    nivel: 0,
},

{
    title: 'SimuladoQuestaos',
    href: '/simuladoquestao',
    icon: LayoutGrid,
    nivel: 0,
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
