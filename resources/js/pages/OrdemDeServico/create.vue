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
        watch
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


    const breadcrumbs: BreadcrumbItem[] = [{
        title: 'OrdemDeServicos',
        href: '/ordemdeservico'
    }, ];

    const headerTitle = 'Ordens de Serviço';
    const headerDescription = 'Gerencie suas Ordens de Serviço aqui.';

    interface Servico{
            value: number;label: string; descricao: string;
        };
        interface ServicoOrdemDeServico{
            value: number;label: string; descricao: string;
        };

    const props = defineProps < {
        item ? : {
            id: number;id_cliente: number;id_veiculo: number;data_de_entrada: string;data_de_saida: string;status: number;valor_total: number;observacao: string
        };
        sidebarNavItems: {
            title: string;href: string
        } [];
        id_clienteOptions: {
            value: number;label: string,
            veiculo: number
        } [];
        id_veiculoOptions: {
            value: number;label: string
        } [];
        servicos: Servico [];
        id_servicoEdit: ServicoOrdemDeServico [];





    } > ();

    const addItem = () => {
        form.servicos.push({
            value: 0,
            label: '',
            descricao: '',
        })
    }

    const removeItem = (index:number) => {
        form.servicos.splice(index, 1);
    }

    const isEditing = ref(!!props.item);

    const showAlertState = ref(false);
    const alertMessage = ref('');
    const alertVariant = ref < 'success' | 'warning' | 'destructive' > ('success');

    const form = useForm({
        id_cliente: props.item?.id_cliente.toString() || 0,
        id_veiculo: props.item?.id_veiculo.toString() || 0,
        data_de_entrada: props.item?.data_de_entrada.toString() || '',
        data_de_saida: props.item?.data_de_saida.toString() || '',
        status: props.item?.status.toString() || 0,
        valor_total: props.item?.valor_total.toString() || 0,
        observacao: props.item?.observacao.toString() || '',
        servicos: [] as Array <{
            value: number,
            label: string,
            descricao: string,
        }>,
        id_servicoEdit: props.servicos as Array <{
            value: number,
            label: string,
            descricao: string,
        }>
    });

    const formErrors = ref < Record < string,
        string[] >> ({});
    const descricaoMaxLength = 255;

    function showAlert(message: string, variant: 'success' | 'warning' | 'destructive' = 'success'): void {
        alertMessage.value = message;
        alertVariant.value = variant;
        showAlertState.value = true;
        setTimeout(() => showAlertState.value = false, 3000);
    }

    function submitForm() {
        if (isEditing.value) {
            form.put(`/ordemdeservico/${props.item?.id}`, {
                onSuccess: () => {
                    showAlert('Ordem de Serviço atualizada com sucesso!', 'success');
                    formErrors.value = {};
                },
                onError: (errors) => {
                    formErrors.value = errors as unknown as Record < string, string[] > ;
                    const errorMessages = Object.values(formErrors.value).flat().join(', ');
                    showAlert(`Erro ao atualizar a Ordem de Serviço: ${errorMessages}`, 'destructive');
                },
            });
        } else {
            form.post('/ordemdeservico', {
                onSuccess: () => {
                    showAlert('Ordem de Serviço criada com sucesso!', 'success');
                    formErrors.value = {};
                },
                onError: (errors) => {
                    formErrors.value = errors as unknown as Record < string, string[] > ;
                    const errorMessages = Object.values(formErrors.value).flat().join(', ');
                    showAlert(`Erro ao criar a Ordem de Serviço: ${errorMessages}`, 'destructive');
                },
            });
        }
    }


    interface Veiculo {
        id: number;
        value: number;
        label: string;
    }

    const listaVeiculosDono = ref < Veiculo[] > ([]);
    const isLoadingVeiculo = ref(false);

    async function fetchVeiculos(id_cliente: number | null) {
        if (!id_cliente) {
            listaVeiculosDono.value = [];
            return;
        }
        isLoadingVeiculo.value = true;
        listaVeiculosDono.value = [];

        const response = await axios.get('/ordemdeservico/getVeiculoPorCliente/' + id_cliente);
        listaVeiculosDono.value = response.data;
        isLoadingVeiculo.value = false;
    }

    watch(() => form.id_cliente, (id) => {
        fetchVeiculos(id);
    }, {
        immediate: true
    });
</script>

<template>

    <Head :title="isEditing ? 'Editar Ordem de Serviço' : 'Criar Ordem de Serviço'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription"
        :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar Ordem de Serviço' : 'Criar Nova Ordem de Serviço'"
                description="Gerencie os detalhes da Ordem de Serviço" />
        </div>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Alert v-if="showAlertState" class="mb-4"
                :class="{
                    'bg-green-100 border-green-500 text-green-900': alertVariant === 'success',
                    'bg-yellow-100 border-yellow-500 text-yellow-900': alertVariant === 'warning',
                    'bg-red-100 border-red-500 text-red-900': alertVariant === 'destructive',
                }">
                <AlertTitle>Ação Realizada</AlertTitle>
                <AlertDescription>{{ alertMessage }}</AlertDescription>
            </Alert>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="flex flex-col gap-4 p-4">
                    <h2 class="text-lg font-semibold">
                        {{ isEditing ? 'Editar Ordem de Serviço' : 'Criar Nova Ordem de Serviço' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
                            <div>
                                <Label for="id_cliente">Cliente</Label>
                                <Select v-model="form.id_cliente" @change="getVeiculoPorCliente">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Selecione um Cliente" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="option in (props.id_clienteOptions || [])"
                                            :key="option.value" :value="option.value.toString()">
                                            {{ option . label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                        </div>

                        <div>
                            <div>
                                <Label for="id_veiculo">Veículo</Label>
                                <Select v-model="form.id_veiculo"
                                    :disable="!form.id_cliente || isLoadingVeiculo || listaVeiculosDono.length === 0">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Selecione um Veículo"
                                            v-if="listaVeiculosDono.length > 0" />
                                        <SelectValue placeholder="Nenhum Veículo encontrado"
                                            v-else-if="listaVeiculosDono.length === 0 && form.id_cliente" />
                                        <SelectValue placeholder="Selecione um Veículo" v-else />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="option in (listaVeiculosDono || [])" :key="option.value"
                                            :value="option.value.toString()">
                                            {{ option . label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>


                        <Card>
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <CardDescription>
                                            Adicione os serviços
                                        </CardDescription>
                                    </div>
                                    <Button type="button" @click="addItem">
                                        <Plus class="my-2 h-4 w-4" />
                                        Adicionar Serviço
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div v-if="form.servicos.length === 0"
                                     class="text-center py-12 text-muted-foreground">
                                    <Wrench class="mx-auto h-12 w-12 mb-3 opacity-50" />
                                    <p>Nenhum serviço adicionado</p>
                                    <p class="text-sm">Clique em "Adicionar Serviço" para começar</p>
                                </div>

                                <div v-else class="space-y-4">
                                    <div v-for="(item, index) in form.servicos"
                                         :key="index"
                                         class="p-4 border rounded-lg space-y-4">

                                        <div class="flex items-center justify-between">
                                            <h4 class="font-medium">Serviço {{ index + 1 }}</h4>
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
                                                <Label class="text-xs py-1">Serviço</Label>
                                                <Select v-if="!isEditing" v-model="item.value" @update:model-value="(value) => {
                                                    const servico = props.servicos?.find(p => p.value.toString() === value);
                                                    if (servico) {
                                                        item.label = servico.label;
                                                        item.descricao = servico.descricao;
                                                    }
                                                } ">
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Selecione..." />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem
                                                            v-for="servico in props.servicos"
                                                            :key="servico.value"
                                                            :value="servico.value.toString()">
                                                            {{ servico.label }} - {{ servico.descricao }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>



                                                <Select v-else v-model="item.value" @update:model-value="(value) => {
                                                    const servico = props.id_servicoEdit?.find(p => p.value.toString() === value);
                                                    if (servico) {
                                                        item.label = servico.label;
                                                        item.descricao = servico.descricao;
                                                    }
                                                }">
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Selecione..." />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem
                                                            v-for="servico in props.id_servicoEdit"
                                                            :key="servico.value"
                                                            :value="servico.value.toString()">
                                                            {{ servico.label }} - {{ servico.descricao }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                        <div>
                            <Label for="data_de_entrada">Data de Entrada</Label>
                            <Input id="data_de_entrada" v-model="form.data_de_entrada" type="datetime-local" />
                        </div>
                        <div>
                            <Label for="data_de_saida">Data de Saída</Label>
                            <Input id="data_de_saida" v-model="form.data_de_saida" type="datetime-local" />
                        </div>
                        <div>
                            <Label for="status">Status</Label>
                            <RadioGroup default-value="option-one" v-model="form.status" :required="true">
                                <div class="flex items-center space-x-2">
                                    <RadioGroupItem id="option-one" value="1" />
                                    <Label for="option-one">Em Aberto</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <RadioGroupItem id="option-two" value="2" />
                                    <Label for="option-two">Em Andamento</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <RadioGroupItem id="option-three" value="3" />
                                    <Label for="option-three">Finalizado</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <RadioGroupItem id="option-four" value="4" />
                                    <Label for="option-four">Cancelado</Label>
                                </div>
                            </RadioGroup>
                        </div>
                        <div>
                            <Label for="valor_total">Valor Total</Label>
                            <Input id="valor_total" v-model.number="form.valor_total" type="number" step="0.01"
                                placeholder="Digite Valor Total" />
                        </div>
                        <div>
                            <Label for="observacao">Observação</Label>
                            <Textarea id="observacao" v-model="form.observacao" placeholder="Digite Observação"
                                rows="4" />
                        </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar Ordem de Serviço' : 'Criar Ordem de Serviço' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
