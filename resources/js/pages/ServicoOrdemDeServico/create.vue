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
    { title: 'ServicoOrdemDeServicos', href: '/servicoordemdeservico' },
];

const headerTitle = 'ServicoOrdemDeServicos';
const headerDescription = 'Gerencie seus servicoordemdeservicos aqui.';

const props = defineProps<{
    item?: { id: number; id_ordemdeservico: number; id_servico: number; quantidade: number; preco_unitario: number };
    sidebarNavItems: { title: string; href: string }[];
    id_ordemdeservicoOptions: { value: number; label: string }[];
    id_servicoOptions: { value: number; label: string }[];


}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    id_ordemdeservico: props.item?.id_ordemdeservico.toString() || 0,
    id_servico: props.item?.id_servico.toString() || 0,
    quantidade: props.item?.quantidade.toString() || 0,
    preco_unitario: props.item?.preco_unitario.toString() || 0
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
        form.put(`/servicoordemdeservico/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('ServicoOrdemDeServico atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o servicoordemdeservico: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/servicoordemdeservico', {
            onSuccess: () => {
                showAlert('ServicoOrdemDeServico criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o servicoordemdeservico: ${errorMessages}`, 'destructive');
            },
        });
    }
}


</script>

<template>
    <Head :title="isEditing ? 'Editar ServicoOrdemDeServico' : 'Criar ServicoOrdemDeServico'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar ServicoOrdemDeServico' : 'Criar Novo ServicoOrdemDeServico'" description="Gerencie os detalhes do servicoordemdeservico" />
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
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar ServicoOrdemDeServico' : 'Criar Novo ServicoOrdemDeServico' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
    <div>
                    <Label for="id_ordemdeservico">Ordem de Serviço</Label>
                    <Select v-model="form.id_ordemdeservico">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Selecione um Ordem de Serviço" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="option in (props.id_ordemdeservicoOptions || [])" :key="option.value" :value="option.value.toString()">
                                {{ option.label }} - {{ option.veiculo }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
</div>
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
                    <Label for="quantidade">Quantidade</Label>
                    <Input id="quantidade" v-model.number="form.quantidade" type="number" step="1" placeholder="Digite Quantidade" min="0"/>
                </div>
                <div>
                    <Label for="preco_unitario">Preço Unitário</Label>
                    <Input id="preco_unitario" v-model.number="form.preco_unitario" type="number" step="0.01" placeholder="Digite Preço Unitário" min="0"/>
                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar ServicoOrdemDeServico' : 'Criar ServicoOrdemDeServico' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
