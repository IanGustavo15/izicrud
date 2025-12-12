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
    { title: 'Trabalhadores', href: '/trabalhador' },
];

const headerTitle = 'Trabalhadores';
const headerDescription = 'Gerencie seus trabalhadores aqui.';

const props = defineProps<{
    item?: { id: number; nome: string; especialidade: number; valorHora: number; status: number; qualidade: number };
    sidebarNavItems: { title: string; href: string }[];
    especialidade: {value: string | number; label: number}[];





}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    nome: props.item?.nome.toString() || '',
    especialidade: props.item?.especialidade.toString() || 0,
    valorHora: props.item?.valorHora.toString() || 0,
    status: props.item?.status.toString() || 0,
    qualidade: props.item?.qualidade.toString() || 0
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
        form.put(`/trabalhador/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('Trabalhador atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o trabalhador: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/trabalhador', {
            onSuccess: () => {
                showAlert('Trabalhador criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o trabalhador: ${errorMessages}`, 'destructive');
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar Trabalhador' : 'Criar Trabalhador'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar Trabalhador' : 'Criar Novo Trabalhador'" description="Gerencie os detalhes do trabalhador" />
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
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar Trabalhador' : 'Criar Novo Trabalhador' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
                    <Label for="nome">Nome</Label>
                    <Input id="nome" v-model="form.nome" type="text" placeholder="Digite Nome" />
                </div>
                <div>
                    <Label for="especialidade">Especialidade</Label>
                                <Select v-model.number="form.especialidade">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Selecione uma Especialidade" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="option in (props.especialidade || [])"
                                            :key="option.value"
                                            :value="option.value.toString()">
                                            {{ option . label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                </div>
                <div>
                    <Label for="valorHora">Valor por Hora</Label>
                    <Input id="valorHora" v-model.number="form.valorHora" type="number" step="0.01" placeholder="Digite Valor por Hora" />
                </div>
                <div>
                    <Label for="status">Status</Label>
                    <Input id="status" v-model.number="form.status" type="number" step="1" placeholder="Digite Status" />
                </div>
                <div>
                    <Label for="qualidade">Qualidade</Label>
                    <Input id="qualidade" v-model.number="form.qualidade" type="number" step="0.01" placeholder="Digite Qualidade" />
                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar Trabalhador' : 'Criar Trabalhador' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
