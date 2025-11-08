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
    { title: 'Servicos', href: '/servico' },
];

const headerTitle = 'Serviços';
const headerDescription = 'Gerencie seus serviços aqui.';

const props = defineProps<{
    item?: { id: number; nome: string; descricao: string; preco_mao_de_obra: number; tempo_estimado: number };
    sidebarNavItems: { title: string; href: string }[];




}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    nome: props.item?.nome.toString() || '',
    descricao: props.item?.descricao.toString() || '',
    preco_mao_de_obra: props.item?.preco_mao_de_obra.toString() || 0,
    tempo_estimado: props.item?.tempo_estimado.toString() || 0
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
        form.put(`/servico/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('Serviço atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o serviço: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/servico', {
            onSuccess: () => {
                showAlert('Serviço criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o serviço: ${errorMessages}`, 'destructive');
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar Serviço' : 'Criar Serviço'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar Serviço' : 'Criar Novo Serviço'" description="Gerencie os detalhes do serviço" />
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
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar Serviço' : 'Criar Novo Serviço' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
                    <Label for="nome">Nome</Label>
                    <Input id="nome" v-model="form.nome" type="text" placeholder="Digite Nome" />
                </div>
                <div>
                    <Label for="descricao">Descrição</Label>
                    <Input id="descricao" v-model="form.descricao" type="text" placeholder="Digite Descrição" />
                </div>
                <div>
                    <Label for="preco_mao_de_obra">Preço da Mão de Obra (R$)</Label>
                    <Input id="preco_mao_de_obra" v-model.number="form.preco_mao_de_obra" type="number" step="0.01" placeholder="Digite Preço da Mão de Obra" />
                </div>
                <div>
                    <Label for="tempo_estimado">Tempo Estimado (Minutos)</Label>
                    <Input id="tempo_estimado" v-model.number="form.tempo_estimado" type="number" step="1" placeholder="Digite Tempo Estimado" min="1"/>
                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar Serviço' : 'Criar Serviço' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
