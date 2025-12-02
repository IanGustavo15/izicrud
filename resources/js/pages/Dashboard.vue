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

// Funções para ações das tabelas de ordens
const editarOrdem = (ordem: Record<string, any>) => {
    // Redirecionar para edição usando o mesmo padrão do módulo original
    router.visit(`/ordemservico/${ordem.id}/edit`);
};

const excluirOrdem = (ordem: Record<string, any>) => {
    // Confirmar exclusão usando modal como no módulo original
    confirmarExclusaoOrdem(ordem.id);
};

// Funções para ações das tabelas de serviços
const editarServico = (servico: Record<string, any>) => {
    // Redirecionar para edição do serviço
    router.visit(`/servico/${servico.id}/edit`);
};

const excluirServico = (servico: Record<string, any>) => {
    // Confirmar exclusão de serviço
    confirmarExclusaoServico(servico.id);
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
                    title="Últimas Ordens Concluídas"
                    :columns="props.dadosMelhoresProfissionais.columns"
                    :data="props.dadosMelhoresProfissionais.data"
                    :show-pagination="true"
                    :items-per-page="4"
                    :actions="true"
                    @edit="editarOrdem"
                    @delete="excluirOrdem"
                />

                <DashTable
                    title="Ordens Recentes"
                    :columns="props.dadosOrdensRecentes.columns"
                    :data="props.dadosOrdensRecentes.data"
                    :actions="true"
                    :items-per-page="4"
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
                @edit="editarServico"
                @delete="excluirServico"
            />

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
