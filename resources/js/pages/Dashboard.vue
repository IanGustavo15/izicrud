<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref, onMounted } from 'vue'
import { Monitor } from 'lucide-vue-next';
import { VisAxis, VisGroupedBar, VisXYContainer } from '@unovis/vue';
import { ChartContainer, ChartCrosshair, ChartLegendContent, ChartTooltip, ChartTooltipContent, componentToString } from '@/components/ui/chart';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

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

const actionEditOS = (os: Record<string , any>) => {
    console.log(os);
    router.visit(`/ordemdeservico/${os.id}/edit`);
};

const actionEditServico = (serv: Record<string, any>) => {
    console.log(serv);
    router.visit(`/servico/${serv.id}/edit`);
}

// Estados para os modais e funções para os modais
const showDeleteDialog = ref(false);
const itemToDelete = ref<number | null>(null);

function actionDeleteOS(os: any):void{
    itemToDelete.value = os.id;
    showDeleteDialog.value = true;
}

function actionDeleteServico(serv: any):void{
    itemToDelete.value = serv.id;
    showDeleteDialog.value = true;
}

function confirmarExclusaoOS ():void{

    if (itemToDelete.value !== null) {
        router.delete(`/ordemdeservico/${itemToDelete.value}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                itemToDelete.value = null;
            },
            onError: () =>{
                showDeleteDialog.value = false;
            }
        })
    }
}

function confirmarExclusaoServico ():void{

    if (itemToDelete.value !== null) {
        router.delete(`/servico/${itemToDelete.value}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                itemToDelete.value = null;
            },
            onError: () =>{
                showDeleteDialog.value = false;
            }
        })
    }
}



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
                            :data="props.revenueChartData"
                            color="#3b82f6"
                            show-legend
                    />
                    <SimpleChart
                            title="Ordens de Serviço por mês"
                            type="bar"
                            :data="props.usersChartData"
                            color="#10b981"
                    />
                    <SimpleChart
                            title="Ordens de Serviço"
                            type="donut"
                            :data="props.categoriesData"
                            color="#8b56f6"
                            show-legend
                    />
                </div>
                <div class="grid gap-4 lg:grid-cols-2">
                    <DashTable
                        title="Trabalhadores"
                        :columns="performersColumns"
                        :data="props.topPerformersData"
                        show-pagination
                        actions
                    />
                    <DashTable
                        class="[&_td]:whitespace-nowrap [&_th]:whitespace-nowrap [&_td:first-child]:max-w-[100px] [&_td:first-child]:truncate"
                        title="Ordens de Serviço Recentes"
                        :columns="ordersColumns"
                        :data="props.recentOrdersData"
                        :show-pagination="true"
                        :items-per-page="4"
                        :actions="true"
                        @edit="actionEditOS"
                        @delete="actionDeleteOS"
                    />
                </div>
                <DashTable
                        title="Serviços Disponíveis"
                        :columns="servicesColumns"
                        :data="props.servicesData"
                        :show-pagination="true"
                        :actions="true"
                        @edit="actionEditServico"
                        @delete="actionDeleteServico"
                    />
        </div>


        <Dialog v-model:open="showDeleteDialog"><!-- actionDeleteOS -->
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirmar Exclusão</DialogTitle>
                    <DialogDescription>
                        Tem certeza de que deseja excluir esta ordem de serviço? Esta ação não pode ser desfeita.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showDeleteDialog = false">Cancelar</Button>
                    <Button variant="destructive" @click="confirmarExclusaoOS">Excluir</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
        <Dialog v-model:open="showDeleteDialog"><!-- actionDeleteServico -->
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirmar Exclusão</DialogTitle>
                    <DialogDescription>
                        Tem certeza de que deseja excluir este serviço? Esta ação não pode ser desfeita.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showDeleteDialog = false">Cancelar</Button>
                    <Button variant="destructive" @click="confirmarExclusaoServico">Excluir</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>


