<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { ref } from 'vue';
import { FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem, SelectGroup, SelectLabel } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Simulados', href: '/simulado' },
];

const headerTitle = 'Simulados';
const headerDescription = 'Gerencie seus simulados aqui.';

const props = defineProps<{
    item?: { id: number; titulo: string; descricao: string; data_inicio: string; data_fim: string; duracao_minutos: number; numero_vagas: number };
    sidebarNavItems: { title: string; href: string }[];
    
    
    
    
    
    
}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    titulo: props.item?.titulo.toString() || '',
    descricao: props.item?.descricao.toString() || '',
    data_inicio: props.item?.data_inicio.toString() || '',
    data_fim: props.item?.data_fim.toString() || '',
    duracao_minutos: props.item?.duracao_minutos.toString() || 0,
    numero_vagas: props.item?.numero_vagas.toString() || 0
});

const formErrors = ref<Record<string, string[]>>({});
const descricaoMaxLength = 255;

function showAlert(message: string, variant: 'success' | 'warning' | 'destructive' = 'success'): void {
    alertMessage.value = message;
    alertVariant.value = variant;
    showAlertState.value = true;
    setTimeout(() => showAlertState.value = false, 3000);
}

function submitForm() {
    if (isEditing.value) {
        form.put(`/simulado/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('Simulado atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o simulado: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/simulado', {
            onSuccess: () => {
                showAlert('Simulado criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o simulado: ${errorMessages}`, 'destructive');
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar Simulado' : 'Criar Simulado'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar Simulado' : 'Criar Novo Simulado'" description="Gerencie os detalhes do simulado" />
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
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar Simulado' : 'Criar Novo Simulado' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
                    <Label for="titulo">Titulo</Label>
                    <Input id="titulo" v-model="form.titulo" type="text" placeholder="Digite Titulo" />
                </div>
                <div>
                    <Label for="descricao">Descrição</Label>
                    <Textarea id="descricao" v-model="form.descricao" placeholder="Digite Descrição" rows="4" />
                </div>
                <div>
                    <Label for="data_inicio">Data Inicio</Label>
                    <Input id="data_inicio" v-model="form.data_inicio" type="datetime-local" />
                </div>
                <div>
                    <Label for="data_fim">Data Fim</Label>
                    <Input id="data_fim" v-model="form.data_fim" type="datetime-local" />
                </div>
                <div>
                    <Label for="duracao_minutos">Duração em Minutos</Label>
                    <Input id="duracao_minutos" v-model.number="form.duracao_minutos" type="number" step="1" placeholder="Digite Duração em Minutos" />
                </div>
                <div>
                    <Label for="numero_vagas">Numero de Vagas</Label>
                    <Input id="numero_vagas" v-model.number="form.numero_vagas" type="number" step="1" placeholder="Digite Numero de Vagas" />
                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar Simulado' : 'Criar Simulado' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
