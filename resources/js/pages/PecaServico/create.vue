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
    { title: 'PecaServicos', href: '/pecaservico' },
];

const headerTitle = 'PecaServicos';
const headerDescription = 'Gerencie seus pecaservicos aqui.';

const props = defineProps<{
    item?: { id: number; id_servico: number; id_peca: number; quantidade_peca: number };
    sidebarNavItems: { title: string; href: string }[];
    id_servicoOptions: { value: number; label: string }[];
    id_pecaOptions: { value: number; label: string }[];

}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    id_servico: props.item?.id_servico.toString() || 0,
    id_peca: props.item?.id_peca.toString() || 0,
    quantidade_peca: props.item?.quantidade_peca.toString() || 0
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
        form.put(`/pecaservico/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('PecaServico atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o pecaservico: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/pecaservico', {
            onSuccess: () => {
                showAlert('PecaServico criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o pecaservico: ${errorMessages}`, 'destructive');
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar PecaServico' : 'Criar PecaServico'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar PecaServico' : 'Criar Novo PecaServico'" description="Gerencie os detalhes do pecaservico" />
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
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar PecaServico' : 'Criar Novo PecaServico' }}</h2>
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
                    <Label for="id_peca">Peça</Label>
                    <Select v-model="form.id_peca">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Selecione um Peça" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="option in (props.id_pecaOptions || [])" :key="option.value" :value="option.value.toString()">
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
</div>
                <div>
                    <Label for="quantidade_peca">Quantidade</Label>
                    <Input id="quantidade_peca" v-model.number="form.quantidade_peca" type="number" step="1" placeholder="Digite Quantidade" />
                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar PecaServico' : 'Criar PecaServico' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
