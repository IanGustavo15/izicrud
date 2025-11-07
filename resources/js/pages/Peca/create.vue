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
    { title: 'Peças', href: '/peca' },
];

const headerTitle = 'Peças';
const headerDescription = 'Gerencie suas peças aqui.';

const props = defineProps<{
    item?: { id: number; descricao: string; codigo_unico: string; preco_de_custo: number; preco_de_venda: number; quantidade: number; estoque: number };
    sidebarNavItems: { title: string; href: string }[];






}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    descricao: props.item?.descricao.toString() || '',
    codigo_unico: props.item?.codigo_unico.toString() || '',
    preco_de_custo: props.item?.preco_de_custo.toString() || 0,
    preco_de_venda: props.item?.preco_de_venda.toString() || 0,
    quantidade: props.item?.quantidade.toString() || 0,
    estoque: props.item?.estoque.toString() || 0
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
        form.put(`/peca/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('Peça atualizada com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar a peça: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/peca', {
            onSuccess: () => {
                showAlert('Peça criada com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar a peça: ${errorMessages}`, 'destructive');
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar Peça' : 'Criar Peça'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar Peça' : 'Criar Nova Peça'" description="Gerencie os detalhes da peça" />
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
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar Peça' : 'Criar Nova Peça' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
                    <Label for="descricao">Descrição</Label>
                    <Input id="descricao" v-model="form.descricao" type="text" placeholder="Digite Descrição" />
                </div>
                <div>
                    <Label for="codigo_unico">Código Único</Label>
                    <Input id="codigo_unico" v-model="form.codigo_unico" type="text" placeholder="Digite Código Único" maxlength="7"/>
                </div>
                <div>
                    <Label for="preco_de_custo">Preço de Custo</Label>
                    <Input id="preco_de_custo" v-model.number="form.preco_de_custo" type="number" step="0.01" placeholder="Digite Preço de Custo" min="0.01"/>
                </div>
                <div>
                    <Label for="preco_de_venda">Preço de Venda</Label>
                    <Input id="preco_de_venda" v-model.number="form.preco_de_venda" type="number" step="0.01" placeholder="Digite Preço de Venda" min="0.01"/>
                </div>
                <div>
                    <Label for="quantidade">Quantidade</Label>
                    <Input id="quantidade" v-model.number="form.quantidade" type="number" step="1" placeholder="Digite Quantidade" min="1"/>
                </div>
                <div>
                    <Label for="estoque">Estoque</Label>
                    <Input id="estoque" v-model.number="form.estoque" type="number" step="1" placeholder="Digite Estoque" min="0"/>
                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar Peca' : 'Criar Peca' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
