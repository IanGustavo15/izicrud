<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ClienteForm from './components/ClienteForm.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Clientes', href: '/cliente' },
];

const headerTitle = 'Clientes';
const headerDescription = 'Gerencie seus clientes aqui.';

const props = defineProps<{
    item?: { id: number; nome: string; email: string; cpf: string; telefone: string; foto: string; [key: string]: any };
    sidebarNavItems: { title: string; href: string }[];
    
}>();

const isEditing = ref(!!props.item);
const processing = ref(false);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

function showAlert(message: string, variant: 'success' | 'warning' | 'destructive' = 'success'): void {
    alertMessage.value = message;
    alertVariant.value = variant;
    showAlertState.value = true;
    setTimeout(() => showAlertState.value = false, 3000);
}

function handleSubmit(formData: FormData) {
    processing.value = true;

    if (isEditing.value && props.item) {
        // Para edição
        router.post(`/cliente/${props.item.id}`, formData, {
            forceFormData: true,
            onSuccess: () => {
                showAlert('Cliente atualizado com sucesso!', 'success');
                processing.value = false;
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors).flat().join(', ');
                showAlert(`Erro ao atualizar o cliente: ${errorMessages}`, 'destructive');
                processing.value = false;
            },
        });
    } else {
        // Para criação
        router.post('/cliente', formData, {
            forceFormData: true,
            onSuccess: () => {
                showAlert('Cliente criado com sucesso!', 'success');
                processing.value = false;
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors).flat().join(', ');
                showAlert(`Erro ao criar o cliente: ${errorMessages}`, 'destructive');
                processing.value = false;
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar Cliente' : 'Criar Cliente'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar Cliente' : 'Criar Novo Cliente'" description="Gerencie os detalhes do cliente" />
        </div>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Alert v-if="showAlertState" class="mb-4" :class="{
                'bg-green-100 border-green-500 text-green-900': alertVariant === 'success',
                'bg-yellow-100 border-yellow-500 text-yellow-900': alertVariant === 'warning',
                'bg-red-100 border-red-500 text-red-900': alertVariant === 'destructive',
            }">
                <AlertTitle>Ação Realizada</AlertTitle>
                <AlertDescription>{{ alertMessage }}</AlertDescription>
            </Alert>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="flex flex-col gap-4 p-4">
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar Cliente' : 'Criar Novo Cliente' }}</h2>

                    <ClienteForm
                        :item="props.item"
                        :is-editing="isEditing"
                        :processing="processing"
                        
                        @submit="handleSubmit"
                        @alert="showAlert"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
