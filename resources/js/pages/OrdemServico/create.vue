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
    { title: 'OrdemServicos', href: '/ordemservico' },
];

const headerTitle = 'OrdemServicos';
const headerDescription = 'Gerencie seus ordemservicos aqui.';

const props = defineProps<{
    item?: { id: number; id_servico: number; id_moto: number; data_servico: string; realizado: boolean };
    sidebarNavItems: { title: string; href: string }[];
    id_servicoOptions: { value: number; label: string }[];
    id_motoOptions: { value: number; label: string }[];
    
    
}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    id_servico: props.item?.id_servico.toString() || 0,
    id_moto: props.item?.id_moto.toString() || 0,
    data_servico: props.item?.data_servico.toString() || '',
    realizado: props.item?.realizado.toString() || false
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
        form.put(`/ordemservico/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('OrdemServico atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o ordemservico: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/ordemservico', {
            onSuccess: () => {
                showAlert('OrdemServico criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o ordemservico: ${errorMessages}`, 'destructive');
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar OrdemServico' : 'Criar OrdemServico'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar OrdemServico' : 'Criar Novo OrdemServico'" description="Gerencie os detalhes do ordemservico" />
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
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar OrdemServico' : 'Criar Novo OrdemServico' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
    <div>
                    <Label for="id_servico">Serviço</Label>
                    <Select v-model="form.id_servico">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Selecione um Serviço" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="option in (props.id_servicoOptions || [])" :key="option.value" :value="option.value.toString()">
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
</div>
                <div>
    <div>
                    <Label for="id_moto">Moto</Label>
                    <Select v-model="form.id_moto">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Selecione um Moto" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="option in (props.id_motoOptions || [])" :key="option.value" :value="option.value.toString()">
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
</div>
                <div>
                    <Label for="data_servico">Realizado em</Label>
                    <Input id="data_servico" v-model="form.data_servico" type="date" />
                </div>
                <div class="flex items-center space-x-2">
                    <Checkbox id="realizado" v-model="form.realizado" />
                    <Label for="realizado">Realizado</Label>
                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar OrdemServico' : 'Criar OrdemServico' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
