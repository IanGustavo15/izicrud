<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Componentes do dashboard
import StatsCard from '@/components/dashboard/StatsCard.vue';
import SimpleChart from '@/components/dashboard/SimpleChart.vue';
import DashTable from '@/components/dashboard/DashTable.vue';

// Componentes para modais
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Painel',
        href: dashboard().url,
    },
];

// Interface para props vindas do controller
interface PontoDadosGrafico {
    label: string;
    value: number;
}

interface Coluna {
    key: string;
    label: string;
}

interface DadosEstatistica {
    title: string;
    value: string | number;
    change?: string;
    subtitle?: string;
    variant?: 'default' | 'success' | 'warning' | 'danger';
}

const props = defineProps<{
    dadosEstatisticas?: DadosEstatistica[];
    dadosGraficoReceita?: PontoDadosGrafico[];
    dadosGraficoUsuarios?: PontoDadosGrafico[];
    dadosCategorias?: PontoDadosGrafico[];
    dadosMelhoresProfissionais?: {
        columns: Coluna[];
        data: Record<string, any>[];
    };
    dadosOrdensRecentes?: {
        columns: Coluna[];
        data: Record<string, any>[];
    };

    receitaTotal?: number;
    valorMedioPedido?: number;
    servicosAtivos?: number;
}>();

// Dados padrão/mock para quando não há dados do backend
const dadosEstatisticasPadrao: DadosEstatistica[] = [
    {
        title: 'Total de Ordens',
        value: '0',
        change: '0%',
        subtitle: 'todas as ordens',
        variant: 'default'
    },
    {
        title: 'Ordens (30 dias)',
        value: 0,
        change: '0%',
        subtitle: 'últimos 30 dias',
        variant: 'default'
    },
    {
        title: 'Ordens Ativas',
        value: 0,
        change: '0%',
        subtitle: 'pendentes + em andamento',
        variant: 'default'
    },
    {
        title: 'Processando',
        value: 0,
        change: '0%',
        subtitle: 'em andamento',
        variant: 'default'
    }
];

const dadosGraficoReceitaPadrao: PontoDadosGrafico[] = [
    { label: 'Jun', value: 0 },
    { label: 'Jul', value: 0 },
    { label: 'Ago', value: 0 },
    { label: 'Set', value: 0 },
    { label: 'Out', value: 0 },
    { label: 'Nov', value: 0 },
    { label: 'Dez', value: 0 }
];

const dadosGraficoUsuariosPadrao: PontoDadosGrafico[] = [
    { label: 'Dom', value: 0 },
    { label: 'Seg', value: 0 },
    { label: 'Ter', value: 0 },
    { label: 'Qua', value: 0 },
    { label: 'Qui', value: 0 },
    { label: 'Sex', value: 0 },
    { label: 'Sab', value: 0 }
];

const dadosCategoriasPadrao: PontoDadosGrafico[] = [
    { label: 'Cães', value: 0 },
    { label: 'Gatos', value: 0 },
    { label: 'Exóticos', value: 0 }
];

const dadosMelhoresProfissionaisPadrao = {
    columns: [
        { key: 'pet', label: 'Pet Atendido' },
        { key: 'valor', label: 'Valor' },
        { key: 'status', label: 'Status' },
        { key: 'data', label: 'Data' }
    ],
    data: []
};

const dadosOrdensRecentesPadrao = {
    columns: [
        { key: 'numero', label: 'Ordem' },
        { key: 'pet', label: 'Pet' },
        { key: 'servico', label: 'Serviço' },
        { key: 'status', label: 'Status' },
        { key: 'total', label: 'Total' }
    ],
    data: []
};

// Funções para ações das tabelas de ordens
const editarOrdem = (ordem: Record<string, any>) => {
    router.visit(`/ordemservico/${ordem.id}/edit`);
};

const excluirOrdem = (ordem: Record<string, any>) => {
    confirmarExclusaoOrdem(ordem.id);
};

// Estados para modais
const showDeleteDialog = ref(false);
const showDeleteServicoDialog = ref(false);
const itemToDelete = ref<number | null>(null);
const servicoToDelete = ref<number | null>(null);

// Funções para modais e alertas
function confirmarExclusaoOrdem(itemId: number): void {
    itemToDelete.value = itemId;
    showDeleteDialog.value = true;
}

function confirmarExclusaoServico(servicoId: number): void {
    servicoToDelete.value = servicoId;
    showDeleteServicoDialog.value = true;
}

function excluirOrdemConfirmada(): void {
    if (itemToDelete.value !== null) {
        router.delete(`/ordemservico/${itemToDelete.value}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                itemToDelete.value = null;
            },
            onError: () => {
                showDeleteDialog.value = false;
            },
        });
    }
}

function excluirServicoConfirmado(): void {
    if (servicoToDelete.value !== null) {
        router.delete(`/servico/${servicoToDelete.value}`, {
            onSuccess: () => {
                showDeleteServicoDialog.value = false;
                servicoToDelete.value = null;
            },
            onError: () => {
                showDeleteServicoDialog.value = false;
            },
        });
    }
}
</script>

<template>
    <Head title="Painel" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-hidden p-2">
            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
                <StatsCard
                    v-for="(stat, index) in (props.dadosEstatisticas || dadosEstatisticasPadrao)"
                    :key="index"
                    :title="stat.title"
                    :value="stat.value"
                    :change="stat.change"
                    :subtitle="stat.subtitle"
                    :variant="stat.variant"
                />
            </div>

            <div class="grid flex-1 gap-3 md:grid-cols-2 lg:grid-cols-3 min-h-0">
                <SimpleChart
                    title="Receita Mensal"
                    type="line"
                    :data="props.dadosGraficoReceita || dadosGraficoReceitaPadrao"
                    color="#3b82f6"
                    show-legend
                />

                <SimpleChart
                    title="Novos Usuários"
                    type="bar"
                    :data="props.dadosGraficoUsuarios || dadosGraficoUsuariosPadrao"
                    color="#10b981"
                />

                <SimpleChart
                    title="Distribuição por Categorias"
                    type="donut"
                    :data="props.dadosCategorias || dadosCategoriasPadrao"
                    color="#8b5cf6"
                    show-legend
                />
            </div>

            <div class="grid flex-1 gap-3 lg:grid-cols-2 min-h-0">
                <DashTable
                    title="Últimas Ordens Concluídas"
                    :columns="(props.dadosMelhoresProfissionais || dadosMelhoresProfissionaisPadrao).columns"
                    :data="(props.dadosMelhoresProfissionais || dadosMelhoresProfissionaisPadrao).data"
                    :show-pagination="false"
                    :items-per-page="3"
                    :actions="true"
                    @edit="editarOrdem"
                    @delete="excluirOrdem"
                />

                <DashTable
                    title="Ordens Recentes"
                    :columns="(props.dadosOrdensRecentes || dadosOrdensRecentesPadrao).columns"
                    :data="(props.dadosOrdensRecentes || dadosOrdensRecentesPadrao).data"
                    :actions="true"
                    :items-per-page="3"
                    :show-pagination="false"
                    @edit="editarOrdem"
                    @delete="excluirOrdem"
                />
            </div>

        </div>

        <!-- Modal de confirmação de exclusão de Ordem -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirmar Exclusão</DialogTitle>
                    <DialogDescription>
                        Tem certeza de que deseja excluir esta ordem de serviço? Esta ação não pode ser desfeita.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showDeleteDialog = false">Cancelar</Button>
                    <Button variant="destructive" @click="excluirOrdemConfirmada">Excluir</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Modal de confirmação de exclusão de Serviço -->
        <Dialog v-model:open="showDeleteServicoDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirmar Exclusão</DialogTitle>
                    <DialogDescription>
                        Tem certeza de que deseja excluir este serviço? Esta ação não pode ser desfeita.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showDeleteServicoDialog = false">Cancelar</Button>
                    <Button variant="destructive" @click="excluirServicoConfirmado">Excluir</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
