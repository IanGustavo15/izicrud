<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

// Componentes do dashboard
import StatsCard from '@/components/dashboard/StatsCard.vue';
import SimpleChart from '@/components/dashboard/SimpleChart.vue';
import DashTable from '@/components/dashboard/DashTable.vue';

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
    dadosEstatisticas: DadosEstatistica[];
    dadosGraficoReceita: PontoDadosGrafico[];
    dadosGraficoUsuarios: PontoDadosGrafico[];
    dadosCategorias: PontoDadosGrafico[];
    dadosMelhoresProfissionais: {
        columns: Coluna[];
        data: Record<string, any>[];
    };
    dadosOrdensRecentes: {
        columns: Coluna[];
        data: Record<string, any>[];
    };
    dadosServicos: {
        columns: Coluna[];
        data: Record<string, any>[];
    };
    receitaTotal: number;
    valorMedioPedido: number;
    servicosAtivos: number;
}>();

// Funções para ações das tabelas
const visualizarOrdem = (ordem: Record<string, any>) => {
    console.log('Visualizando ordem:', ordem);
    alert(`Visualizando ordem: ${ordem.cliente || ordem.id}`);
};

const editarOrdem = (ordem: Record<string, any>) => {
    console.log('Editando ordem:', ordem);
    alert(`Editando ordem: ${ordem.cliente || ordem.id}`);
};

const excluirOrdem = (ordem: Record<string, any>) => {
    console.log('Excluindo ordem:', ordem);
    alert(`Ordem excluída: ${ordem.cliente || ordem.id}`);
};

const visualizarServico = (servico: Record<string, any>) => {
    console.log('Visualizando serviço:', servico);
    alert(`Visualizando serviço: ${servico.nome || servico.name || servico.id}`);
};

const editarServico = (servico: Record<string, any>) => {
    console.log('Editando serviço:', servico);
    alert(`Editando serviço: ${servico.nome || servico.name || servico.id}`);
};

const excluirServico = (servico: Record<string, any>) => {
    console.log('Excluindo serviço:', servico);
    alert(`Serviço excluído: ${servico.nome || servico.name || servico.id}`);
};
</script>

<template>
    <Head title="Painel" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <StatsCard
                    v-for="(stat, index) in props.dadosEstatisticas"
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
                    :data="props.dadosGraficoReceita"
                    color="#3b82f6"
                    show-legend
                />

                <SimpleChart
                    title="Novos Usuários"
                    type="bar"
                    :data="props.dadosGraficoUsuarios"
                    color="#10b981"
                />

                <SimpleChart
                    title="Distribuição por Categorias"
                    type="donut"
                    :data="props.dadosCategorias"
                    color="#8b5cf6"
                    show-legend
                />
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <DashTable
                    title="Melhores Profissionais"
                    :columns="props.dadosMelhoresProfissionais.columns"
                    :data="props.dadosMelhoresProfissionais.data"
                    :show-pagination="true"
                    :items-per-page="3"
                />

                <DashTable
                    title="Ordens Recentes"
                    :columns="props.dadosOrdensRecentes.columns"
                    :data="props.dadosOrdensRecentes.data"
                    :actions="true"
                    :items-per-page="4"
                    @view="visualizarOrdem"
                    @edit="editarOrdem"
                    @delete="excluirOrdem"
                />
            </div>

            <DashTable
                title="Serviços Populares"
                :columns="props.dadosServicos.columns"
                :data="props.dadosServicos.data"
                :show-pagination="true"
                :actions="true"
                :items-per-page="5"
                @view="visualizarServico"
                @edit="editarServico"
                @delete="excluirServico"
            />

        </div>
    </AppLayout>
</template>
