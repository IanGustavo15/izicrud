<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

// dash components
import StatsCard from '@/components/dashboard/StatsCard.vue';
import SimpleChart from '@/components/dashboard/SimpleChart.vue';
import DashTable from '@/components/dashboard/DashTable.vue';
import WelcomeCard from '@/components/dashboard/WelcomeCard.vue';
import FeatureCard from '@/components/dashboard/FeatureCard.vue';
import ExampleSystem from '@/components/dashboard/ExampleSystem.vue';

// mock data
import { useDashboardData } from '@/composables/useDashboardData';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// load mock data
const {
    statsData,
    revenueChartData,
    usersChartData,
    categoriesData,
    topPerformersData,
    recentOrdersData,
    servicesData,
    performersColumns,
    ordersColumns,
    servicesColumns,
    totalRevenue,
    averageOrderValue,
    activeServices
} = useDashboardData();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <!-- <WelcomeCard user-name="Desenvolvedor" /> -->

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <StatsCard
                    v-for="(stat, index) in statsData"
                    :key="index"
                    :title="stat.title"
                    :value="stat.value"
                    :change="stat.change"
                    :subtitle="stat.subtitle"
                    :variant="stat.variant"
                />
            </div>

            <!-- Charts Row -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <SimpleChart
                    title="Receita Mensal"
                    type="line"
                    :data="revenueChartData"
                    color="#3b82f6"
                    show-legend
                />

                <SimpleChart
                    title="Novos Usuários"
                    type="bar"
                    :data="usersChartData"
                    color="#10b981"
                />

                <SimpleChart
                    title="Meta do Mês"
                    type="donut"
                    :data="categoriesData"
                    :percentage="73"
                    color="#8b5cf6"
                />
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <DashTable
                    title="Melhores Profissionais"
                    :columns="performersColumns"
                    :data="topPerformersData"
                    show-pagination
                />

                <DashTable
                    title="Ordens Recentes"
                    :columns="ordersColumns"
                    :data="recentOrdersData"
                    actions
                />
            </div>

            <DashTable
                title="Serviços Populares"
                :columns="servicesColumns"
                :data="servicesData"
                show-pagination
                actions
            />

            <!-- Example System -->
            <!-- <ExampleSystem /> -->

            <!-- Additional Stats & Features -->
            <!-- Stats Cards -->
            <!-- <div class="grid gap-4 md:grid-cols-4"> -->
                <!-- <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-sidebar-accent">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">R$ {{ totalRevenue.toLocaleString('pt-BR') }}</p>
                        <p class="text-sm text-muted-foreground">Receita Total</p>
                    </div>
                </div>
                <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-sidebar-accent">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">R$ {{ averageOrderValue.toFixed(2) }}</p>
                        <p class="text-sm text-muted-foreground">Ticket Médio</p>
                    </div>
                </div>
                <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-sidebar-accent">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-purple-600">{{ activeServices }}</p>
                        <p class="text-sm text-muted-foreground">Serviços Ativos</p>
                    </div>
                </div> -->

                <!-- Feature Card -->
                <!-- <FeatureCard
                    title="CRUD Generator"
                    description="Crie CRUDs completos em segundos"
                    :features="['Relacionamentos 1:N', 'Interface Rica', 'Soft Delete']"
                    button-text="Começar Agora"
                    @action="console.log('Iniciar CRUD Generator')"
                /> -->
            <!-- </div> -->
        </div>
    </AppLayout>
</template>
