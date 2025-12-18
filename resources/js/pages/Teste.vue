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
// import DonoForm from '@/pages/Dono/components/DonoForm.vue';

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
        
    </AppLayout>
</template>
