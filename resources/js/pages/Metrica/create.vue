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
    { title: 'Metricas', href: '/metrica' },
];

const headerTitle = 'Metricas';
const headerDescription = 'Gerencie seus metricas aqui.';

const props = defineProps<{
    item?: { id: number; id_simulado: number; media_geral_pontuacao: number; base_vagas: number };
    sidebarNavItems: { title: string; href: string }[];
    id_simuladoOptions: { value: number; label: string }[];
    
    
}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    id_simulado: props.item?.id_simulado.toString() || 0,
    media_geral_pontuacao: props.item?.media_geral_pontuacao.toString() || 0,
    base_vagas: props.item?.base_vagas.toString() || 0
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
        form.put(`/metrica/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('Metrica atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o metrica: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/metrica', {
            onSuccess: () => {
                showAlert('Metrica criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o metrica: ${errorMessages}`, 'destructive');
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar Metrica' : 'Criar Metrica'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar Metrica' : 'Criar Novo Metrica'" description="Gerencie os detalhes do metrica" />
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
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar Metrica' : 'Criar Novo Metrica' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
    <div>
                    <Label for="id_simulado">Simulado</Label>
                    <Select v-model="form.id_simulado">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Selecione um Simulado" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="option in (props.id_simuladoOptions || [])" :key="option.value" :value="option.value.toString()">
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
</div>
                <div>
                    <Label for="media_geral_pontuacao">Media de Pontuação Geral</Label>
                    <Input id="media_geral_pontuacao" v-model.number="form.media_geral_pontuacao" type="number" step="0.01" placeholder="Digite Media de Pontuação Geral" />
                </div>
                <div>
                    <Label for="base_vagas">Numero de Vagas</Label>
                    <Input id="base_vagas" v-model.number="form.base_vagas" type="number" step="1" placeholder="Digite Numero de Vagas" />
                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar Metrica' : 'Criar Metrica' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
