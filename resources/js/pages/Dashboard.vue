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

/*
 * Exemplo de uso do componente de Formulario:
 *
 * import DonoForm from '@/pages/Dono/components/DonoForm.vue';
 *
 * <DonoForm
 *     :is-editing="false"  // ou true para edição
 *     :processing="processing"
 *     @submit="handleSubmit"
 *     @alert="showAlert"
 * />
 *
 * O componente DonoForm é reutilizável e pode ser usado em:
 * - Modais de cadastro rápido
 * - Páginas de criação/edição
 * - Formulários inline
 */
import DonoForm from '@/pages/Dono/components/DonoForm.vue';

// Componentes para modais e alertas
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';

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
    width?: 'auto' | 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'full' | string;
    align?: 'left' | 'center' | 'right';
    minWidth?: string;
    maxWidth?: string;
    priority?: number;
    truncate?: boolean;
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

    // Dados dos donos para o card de estatísticas
    totalDonos?: number;
}>();

// Estados para modais
const showDeleteDialog = ref(false);
const itemToDelete = ref<number | null>(null);

// Estados para modal de cadastro rápido de dono
const showQuickCreateDono = ref(false);
const processing = ref(false);

// Estados para alertas
const showAlert = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

// Funções para ações das tabelas de ordens
const editarOrdem = (ordem: Record<string, any>) => {
    router.visit(`/ordemservico/${ordem.id}/edit`);
};

const excluirOrdem = (ordem: Record<string, any>) => {
    itemToDelete.value = ordem.id;
    showDeleteDialog.value = true;
};

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

// Funções para o componente DonoForm
function openQuickCreate() {
    showQuickCreateDono.value = true;
}


function handleQuickSubmit(formData: FormData) {
    processing.value = true;

    router.post('/dono', formData, {
        forceFormData: true,
        preserveState: true,
        onSuccess: () => {
            showAlertMessage('Dono criado com sucesso!', 'success');
            showQuickCreateDono.value = false;
            processing.value = false;
            // Recarregar para atualizar contador
            router.reload();
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(', ');
            showAlertMessage(`Erro ao criar o dono: ${errorMessages}`, 'destructive');
            processing.value = false;
        },
    });
}

function showAlertMessage(message: string, variant: 'success' | 'warning' | 'destructive' = 'success') {
    alertMessage.value = message;
    alertVariant.value = variant;
    showAlert.value = true;
    setTimeout(() => showAlert.value = false, 3000);
}
</script>

<template>
    <Head title="Painel" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-hidden p-2">
            <!-- Alerta Global -->
            <Alert v-if="showAlert" class="mb-4" :class="{
                'bg-green-100 border-green-500 text-green-900': alertVariant === 'success',
                'bg-yellow-100 border-yellow-500 text-yellow-900': alertVariant === 'warning',
                'bg-red-100 border-red-500 text-red-900': alertVariant === 'destructive',
            }">
                <AlertTitle>Notificação</AlertTitle>
                <AlertDescription>{{ alertMessage }}</AlertDescription>
            </Alert>

            <!-- Estatísticas com Card de Donos -->
            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
                <!-- Card de Total de Donos usando StatsCard -->
                <div class="relative">
                    <StatsCard
                        title="Total de Donos"
                        :value="props.totalDonos || 0"
                        subtitle="Clientes cadastrados"
                        variant="default"
                    />
                    <!-- Botão de ação sobreposto no canto superior direito -->
                    <Button
                        @click="openQuickCreate"
                        size="sm"
                        variant="default"
                        class="absolute top-3 right-3 h-7 px-2 text-xs"
                    >
                        Novo
                    </Button>
                </div>

                <!-- Outros cards de estatísticas -->
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

            <!-- Gráficos -->
            <div class="grid flex-1 gap-3 md:grid-cols-2 lg:grid-cols-3 min-h-0">
                <SimpleChart
                    v-if="props.dadosGraficoReceita?.length"
                    title="Receita dos Serviços"
                    type="line"
                    :data="props.dadosGraficoReceita"
                    color="#3b82f6"
                    show-legend
                />
                <div v-else class="rounded-lg border bg-card p-6 text-center text-muted-foreground">
                    Sem dados de receita dos serviços
                </div>

                <SimpleChart
                    v-if="props.dadosGraficoUsuarios?.length"
                    title="Atendimentos por Mês"
                    type="bar"
                    :data="props.dadosGraficoUsuarios"
                    color="#10b981"
                    show-legend
                />
                <div v-else class="rounded-lg border bg-card p-6 text-center text-muted-foreground">
                    Sem dados de atendimentos
                </div>

                <SimpleChart
                    v-if="props.dadosCategorias?.length"
                    title="Distribuição por Categorias"
                    type="donut"
                    :data="props.dadosCategorias"
                    color="#8b5cf6"
                    show-legend
                />
                <div v-else class="rounded-lg border bg-card p-6 text-center text-muted-foreground">
                    Sem dados de categorias
                </div>
            </div>

            <div class="grid flex-1 gap-3 lg:grid-cols-2 min-h-0">
                <DashTable
                    v-if="props.dadosMelhoresProfissionais?.data?.length"
                    title="Últimas Ordens Concluídas"
                    :columns="props.dadosMelhoresProfissionais.columns"
                    :data="props.dadosMelhoresProfissionais.data"
                    :show-pagination="true"
                    :items-per-page="3"
                    :actions="false"
                    @edit="editarOrdem"
                    @delete="excluirOrdem"
                />
                <div v-else class="rounded-lg border bg-card p-6 text-center text-muted-foreground">
                    Nenhuma ordem concluída
                </div>

                <DashTable
                    v-if="props.dadosOrdensRecentes?.data?.length"
                    title="Ordens Recentes"
                    :columns="props.dadosOrdensRecentes.columns"
                    :data="props.dadosOrdensRecentes.data"
                    :actions="true"
                    :items-per-page="3"
                    :show-pagination="true"
                    @edit="editarOrdem"
                    @delete="excluirOrdem"
                />
                <div v-else class="rounded-lg border bg-card p-6 text-center text-muted-foreground">
                    Nenhuma ordem recente
                </div>


            </div>
        </div>

        <!-- Modal de Cadastro Rápido - Exemplo prático do DonoForm -->
        <!--
        1. Import: import DonoForm from '@/pages/Dono/components/DonoForm.vue';
        2. Props:
           - is-editing: false para criação, true para edição
           - processing: estado de loading durante submit
        3. Eventos:
           - @submit: recebe FormData com todos os campos
           - @alert: recebe mensagem para mostrar feedback
        -->
        <Dialog v-model:open="showQuickCreateDono">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Cadastro Rápido - Dono</DialogTitle>
                    <DialogDescription>
                        Adicione um novo dono rapidamente usando o componente DonoForm
                    </DialogDescription>
                </DialogHeader>

                <DonoForm
                    :is-editing="false"
                    :processing="processing"
                    @submit="handleQuickSubmit"
                    @alert="showAlertMessage"
                />
            </DialogContent>
        </Dialog>

        <!-- Modal de confirmação de exclusão -->
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
    </AppLayout>
</template>
