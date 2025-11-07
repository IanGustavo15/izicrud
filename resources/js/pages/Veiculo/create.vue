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
    { title: 'Veículos', href: '/veiculo' },
];

const headerTitle = 'Veículos';
const headerDescription = 'Gerencie seus veículos aqui.';

const props = defineProps<{
    item?: { id: number; id_cliente: number; placa: string; modelo: string; ano: number; tipo: number };
    sidebarNavItems: { title: string; href: string }[];
    id_clienteOptions: { value: number; label: string }[];




}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    id_cliente: props.item?.id_cliente.toString() || 0,
    placa: props.item?.placa.toString() || '',
    modelo: props.item?.modelo.toString() || '',
    ano: props.item?.ano.toString() || 0,
    tipo: props.item?.tipo.toString() || 0
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
        form.put(`/veiculo/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('Veículo atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o veículo: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/veiculo', {
            onSuccess: () => {
                showAlert('Veículo criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o veículo: ${errorMessages}`, 'destructive');
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar Veículo' : 'Criar Veículo'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar Veículo' : 'Criar Novo Veículo'" description="Gerencie os detalhes do veículo" />
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
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar Veículo' : 'Criar Novo Veículo' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
    <div>
                    <Label for="id_cliente">Dono</Label>
                    <Select v-model="form.id_cliente">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Selecione um Dono" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="option in (props.id_clienteOptions || [])" :key="option.value" :value="option.value.toString()">
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
</div>
                <div>
                    <Label for="placa">Placa</Label>
                    <Input id="placa" v-model="form.placa" type="text" placeholder="Digite Placa" maxlength="7"/>
                </div>
                <div>
                    <Label for="modelo">Modelo</Label>
                    <Input id="modelo" v-model="form.modelo" type="text" placeholder="Digite Modelo" />
                </div>
                <div>
                    <Label for="ano">Ano</Label>
                    <Input id="ano" v-model.number="form.ano" type="number" step="1" placeholder="Digite Ano" min="1" max="2026"/>
                </div>
                <div>

                    <Label for="tipo">Tipo</Label>
                    <Select v-model="form.tipo">
                    <SelectTrigger class="w-[180px]">
                     <SelectValue placeholder="Escolha" />
                    </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="1">
                                Carro
                            </SelectItem>
                            <SelectItem value="2">
                                Moto
                            </SelectItem>
                        </SelectContent>
                    </Select>

                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar Veiculo' : 'Criar Veiculo' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
