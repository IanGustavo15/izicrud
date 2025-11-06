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
    { title: 'Clientes', href: '/cliente' },
];

const headerTitle = 'Clientes';
const headerDescription = 'Gerencie seus clientes aqui.';

const props = defineProps<{
    item?: { id: number; nome: string; cpf: string; telefone: string; email: string };
    sidebarNavItems: { title: string; href: string }[];




}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    nome: props.item?.nome.toString() || '',
    cpf: props.item?.cpf.toString() || '',
    telefone: props.item?.telefone.toString() || '',
    email: props.item?.email.toString() || ''
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
        form.put(`/cliente/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('Cliente atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o cliente: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/cliente', {
            onSuccess: () => {
                showAlert('Cliente criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o cliente: ${errorMessages}`, 'destructive');
            },
        });
    }
}

function cpf_mask(event:Event): void
{
    const input = event.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    form.cpf = value;
}

function telefone_mask(event: Event): void {
    const input = event.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    value = value.replace(/(\d{2})(\d)/, '($1) $2');
    value = value.replace(/(\d{5})(\d)/, '$1-$2');
    form.telefone = value;
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
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
                    <Label for="nome">Nome</Label>
                    <Input id="nome" v-model="form.nome" type="text" placeholder="Digite Nome" />
                </div>
                <div>
                    <Label for="cpf">CPF</Label>
                    <Input id="cpf" v-model="form.cpf" @input="cpf_mask" type="text" placeholder="Digite CPF" maxlength="14"/>
                </div>
                <div>
                    <Label for="telefone">Telefone</Label>
                    <Input id="telefone" v-model="form.telefone" @input="telefone_mask" type="text" placeholder="Digite Telefone" maxlength="20"/>
                </div>
                <div>
                    <Label for="email">E-mail</Label>
                    <Input id="email" v-model="form.email"  type="email" placeholder="Digite E-mail" />
                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar Cliente' : 'Criar Cliente' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
