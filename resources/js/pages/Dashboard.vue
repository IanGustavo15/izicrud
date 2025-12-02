<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref, onMounted } from 'vue'
import { Monitor } from 'lucide-vue-next';
import { VisAxis, VisGroupedBar, VisXYContainer } from '@unovis/vue';
import { ChartContainer, ChartCrosshair, ChartLegendContent, ChartTooltip, ChartTooltipContent, componentToString } from '@/components/ui/chart';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

// Dash Components
import StatsCard from '@/components/dashboard/StatsCard.vue';
import SimpleChart from '@/components/dashboard/SimpleChart.vue';
import DashTable from '@/components/dashboard/DashTable.vue';

// Fake Data
import { useDashboardData } from '@/composables/useDashboardData';

const {
    statsData,
    usersChartData,
    topPerformersData,
    recentOrdersData,
} = useDashboardData();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// interface Column {
//   key: string;
//   label: string;
// }

const performersColumns = [
    { key: 'avatar', label: 'Profissional' },
    { key: 'especialidade', label: 'Especialidade' },
    { key: 'trabalhos', label: 'Trabalhos feitos' },
    { key: 'rating', label: 'Avaliação' },
    { key: 'trend', label: 'Tendência' }
];

const ordersColumns = [
    { key: 'numero', label: 'Ordem' },
    { key: 'pet', label: 'Dono/Veículo' },
    { key: 'servico', label: 'Serviços' },
    { key: 'status', label: 'Status' },
    { key: 'total', label: 'Total' }
];

const servicesColumns = [
    { key: 'nome', label: 'Serviço' },
    { key: 'categoria', label: 'Peças' },
    { key: 'preco', label: 'Preço' },
    { key: 'agendamentos', label: 'Quantidade' },
    { key: 'status', label: 'Status' }
];


const revenueChartData = ref([
    { label: 'Jun', value: 4200 },
    { label: 'Jul', value: 3800 },
    { label: 'Ago', value: 5200 },
    { label: 'Set', value: 4800 },
    { label: 'Out', value: 6100 },
    { label: 'Nov', value: 5500 },
    { label: 'Dez', value: 7200 }
]);


const props = defineProps<{
    stats?: Array<{
        title: string;
        value: string | number;
        change?: string;
        subtitle?: string;
        variant?: 'default' | 'success' | 'warning' | 'danger';
    }>;

    revenueChartData: Array<{
        label: string;
        value: number;
    }>;
    usersChartData: Array<{
        label: string;
        value: number;
    }>;
    categoriesData: Array<{
        label: string;
        value: number;
    }>;

    topPerformersData: Record<string, any>[];
    recentOrdersData: Record<string, any>[];
    servicesData: Record<string, any>[];

}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <StatsCard
                        v-for="(stat, index) in props.stats"
                        :key="index"
                        :title="stat.title"
                        :value="stat.value"
                        :change="stat.change"
                        :subtitle="stat.subtitle"
                        :variant="stat.variant"
                    />
                </div>
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
                            title="Ordens de Serviço"
                            type="donut"
                            :data="props.categoriesData"
                            color="#8b56f6"
                    />
                </div>
                <div class="grid gap-4 lg:grid-cols-2">
                    <DashTable
                        title="Trabalhadores"
                        :columns="performersColumns"
                        :data="props.topPerformersData"
                        show-pagination
                    />
                    <DashTable
                        title="Ordens de Serviço Recentes"
                        :columns="ordersColumns"
                        :data="props.recentOrdersData"
                        show-pagination
                    />

                </div>
                <DashTable
                        title="Serviços Disponíveis"
                        :columns="servicesColumns"
                        :data="props.servicesData"
                        show-pagination
                    />
        </div>
    </AppLayout>
</template>


