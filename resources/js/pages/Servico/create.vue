<script setup lang="ts">
    import AppLayout from '@/layouts/AppLayout.vue';
    import {
        type BreadcrumbItem
    } from '@/types';
    import {
        Head
    } from '@inertiajs/vue3';
    import {
        Alert,
        AlertDescription,
        AlertTitle
    } from '@/components/ui/alert';
    import {
        ref,
        watch,
        onMounted,
    } from 'vue';
    import {
        FormField,
        FormItem,
        FormLabel,
        FormMessage
    } from '@/components/ui/form';
    import {
        Input
    } from '@/components/ui/input';
    import {
        Textarea
    } from '@/components/ui/textarea';
    import {
        Button
    } from '@/components/ui/button';
    import {
        useForm
    } from '@inertiajs/vue3';
    import {
        Select,
        SelectTrigger,
        SelectValue,
        SelectContent,
        SelectItem,
        SelectGroup,
        SelectLabel
    } from '@/components/ui/select';
    import {
        Label
    } from '@/components/ui/label';
    import {
        Checkbox
    } from '@/components/ui/checkbox';
    import {
        disable
    } from '@/routes/two-factor';
    import axios from 'axios';
    import {
        RadioGroup,
        RadioGroupItem
    } from '@/components/ui/radio-group';
    import {
        Footprints,
        Wrench
    } from 'lucide-vue-next';
    import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { index } from '@/routes/cliente';
import {
    ArrowLeft,
    Save,
    ShoppingCart,
    Plus,
    Minus,
    Calculator,
    Truck,
    Calendar,
    FileText,
    DollarSign,
    Package,
    Trash2,
    Search
} from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Servicos', href: '/servico' },
];

const headerTitle = 'Serviços';
const headerDescription = 'Gerencie seus serviços aqui.';

interface Peca{
    value: number;
    label: string;
    preco_de_venda: number;
    quantidade: number;
    // quantidade_peca: number;

};

interface PecaServico{
    value: number;
    label: string;
    preco_de_venda: number;
    quantidade: number;
    // quantidade_peca: number;
};

const props = defineProps<{
    item?: { id: number; nome: string; descricao: string; preco_mao_de_obra: number; tempo_estimado: number; quantidade_peca: number};
    sidebarNavItems: { title: string; href: string }[];
    pecas: Peca [];
    pecasEdit: PecaServico [];




}>();

const addItem = () => {
        form.pecas.push({
            value: 0,
            label: '',
            preco_de_venda: 0,
            quantidade: 0,
        })
    };

    const removeItem = (index:number) => {
        form.pecas.splice(index, 1);
    };

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    nome: props.item?.nome.toString() || '',
    descricao: props.item?.descricao.toString() || '',
    preco_mao_de_obra: props.item?.preco_mao_de_obra.toString() || 0,
    tempo_estimado: props.item?.tempo_estimado.toString() || 0,
    quantidade_peca: props.item?.quantidade_peca.toString() || 0,
    pecas: [] as Array <{
            value: number,
            label: string,
            preco_de_venda: number,
            quantidade: number,
        }>,
    pecasEdit: [] as Array<{
        value: number,
        label: string,
        preco_de_venda: number,
        quantidade: number,
    }>,
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

onMounted(
        () => {
            if (isEditing.value && props.pecasEdit) {
                form.pecas = props.pecasEdit
            }
        }
    );
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
                <Card>
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <CardDescription>
                                            Adicione as peças
                                        </CardDescription>
                                    </div>
                                    <Button type="button" @click="addItem">
                                        <Plus class="my-2 h-4 w-4" />
                                        Adicionar Peça
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div v-if="form.pecas.length === 0"
                                     class="text-center py-12 text-muted-foreground">
                                    <Wrench class="mx-auto h-12 w-12 mb-3 opacity-50" />
                                    <p>Nenhuma peça adicionada</p>
                                    <p class="text-sm">Clique em "Adicionar Peças" se o Serviço precisar de Peças</p>
                                </div>

                                <div v-else class="space-y-4">
                                    <div v-for="(item, index) in form.pecas"
                                         :key="index"
                                         class="p-4 border rounded-lg space-y-4">

                                        <div class="flex items-center justify-between">
                                            <h4 class="font-medium">Peça {{ index + 1 }}</h4>
                                            <Button
                                                type="button"
                                                @click="removeItem(index)"
                                                variant="outline"
                                                size="sm"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>

                                        <div class="grid gap-3 sm:grid-cols-6">
                                            <div class="sm:col-span-2">
                                                <Label class="text-xs py-1">Peça</Label>
                                                <Select v-if="!isEditing" v-model="item.value" @update:model-value="(value) => {
                                                    const peca = props.pecas?.find(p => p.value.toString() === value);
                                                    if (peca) {
                                                        item.label = peca.label;
                                                        item.quantidade = peca.quantidade;
                                                    }
                                                } ">
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Selecione..." />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem
                                                            v-for="peca in props.pecas"
                                                            :key="peca.value"
                                                            :value="peca.value.toString()">
                                                            {{ peca.label }} // Quantidade: {{ peca.quantidade }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                <Label v-if="!isEditing" class="text-xs py-1" for="quantidade_peca">Quantia por Serviço</Label>
                                                <Input v-if="!isEditing" id="quantidade_peca" type="number" min="1" v-model.number="form.quantidade_peca" />


                                                <Select v-else v-model="item.value" @update:model-value="(value) => {
                                                    const peca = props.pecas?.find(s => s.value.toString() === value);
                                                    if (peca) {
                                                        item.label = peca.label;
                                                        item.quantidade = peca.quantidade;
                                                    }
                                                }">
                                                    <SelectTrigger>
                                                        <SelectValue v-if="item.value" :placeholder="item.label + ' // Quantidade: ' + item.quantidade"  />
                                                        <SelectValue v-else placeholder="Selecione..." />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem
                                                            v-for="peca in props.pecas"
                                                            :key="peca.value"
                                                            :value="peca.value.toString()">
                                                            {{ peca.label }} // Quantidade: {{ peca.quantidade }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                <Label v-if="isEditing" class="text-xs py-1" for="quantidade_peca">Quantia por Serviço</Label>
                                                <Input v-if="isEditing" id="quantidade_peca" type="number" min="1" v-model.number="form.quantidade_peca" />
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
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
