# LaraVue IzCRUD

Bem-vindo ao **LaraVue IzCRUD**! Este kit é baseado no Starter Kit oficial do Laravel 12 com Vue, mas foi aprimorado com um poderoso gerador de CRUDs que suporta relacionamentos complexos, desenvolvido para acelerar significativamente o desenvolvimento de aplicações web modernas.

Com suporte nativo a **relacionamentos 1:1** e **1:N**, interface rica com componentes Vue dinâmicos e TODOs orientativos, este projeto reflete minha paixão por criar ferramentas que simplificam e aceleram o dia a dia de desenvolvedores.

Desenvolvido por **Nicolas Slujalkovsky** 🚀

---

## ✨ Recursos Principais

- **CRUD Generator Avançado**: Crie modelos, controladores, views Vue, migrações, rotas e itens de menu com um único comando.
- **Relacionamentos Inteligentes**: Suporte automático para relacionamentos 1:1 (belongsTo) e 1:N (belongsToMany) com interface rica.
- **Componentes Vue Dinâmicos**: Interface interativa para relacionamentos com modal de criação rápida.
- **Stack Moderna**: Laravel 12 backend + Vue.js frontend com TypeScript.
- **Soft Delete Integrado**: Todos os CRUDs incluem soft delete com filtros automáticos.
- **Geração Condicional**: Imports e códigos gerados apenas quando necessários para performance otimizada.
- **Integração Sidebar**: Adiciona automaticamente itens de menu ao sidebar.
- **TODOs Orientativos**: Guias claros sobre o que ajustar após a geração.
- **Customizável**: Adapte facilmente às suas necessidades.

---

## 🚀 Como Usar

### Pré-requisitos
- PHP 8.2+
- Composer
- Node.js & npm
- Laravel 12

### Instalação
1. **Clone o repositório**:
   ```bash
   git clone https://github.com/NicolasKovsky/izicrud.git
   cd starter-kit-laravel-vue
   ```
2. **Instale as dependências do PHP**:
   ```bash
   composer install
   ```
3. **Instale as dependências do frontend**:
   ```bash
   npm install
   ```
4. **Configure o arquivo `.env`**:
   - Copie `.env.example` para `.env` e ajuste as configurações (banco de dados, etc.).
5. **Gere a chave da aplicação**:
   ```bash
   php artisan key:generate
   ```
6. **Execute as migrações**:
   ```bash
   php artisan migrate
   ```
7. **Inicie o servidor**:
   ```bash
   php artisan serve
   npm run dev
   ```

---

## 🛠️ Gerando CRUDs

Use o comando `make:crud` para criar um CRUD completo em segundos:

```bash
php artisan make:crud Post title:"Título":string content:"Conteúdo":text
```

### 🚀 **Novidade: Relacionamentos 1:N com Pivot**

Agora você pode criar relacionamentos **1 para muitos** usando a sintaxe `pModelo`:

```bash
# Exemplo: Uma Ordem de Serviço que tem vários Serviços
php artisan make:crud OrdemServico numero:"Número":string id_pet:"Pet":integer servicos:"Serviços":pServico
```

Isso gerará:
- **Relacionamento pivot** no modelo (`belongsToMany`)
- **Componente Vue interativo** com lista dinâmica, modal para criação rápida
- **Controller com sync automático** dos relacionamentos
- **TODOs orientativos** para configuração manual

### Tipos de Campos e Relacionamentos Suportados

| **Tipo**         | **Descrição**                                       | **Exemplo** |
|-------------------|-----------------------------------------------------|-------------|
| `string`          | Texto curto (ex.: nome, título).                   | `title:"Título":string` |
| `text`            | Texto longo (ex.: descrição).                      | `content:"Conteúdo":text` |
| `integer`         | Número inteiro.                                     | `idade:"Idade":integer` |
| `bigInteger`      | Número inteiro grande.                              | `id_externo:"ID Externo":bigInteger` |
| `float`           | Número de ponto flutuante.                          | `preco:"Preço":float` |
| `double`          | Número de precisão dupla.                           | `latitude:"Latitude":double` |
| `decimal`         | Número decimal exato.                               | `valor:"Valor":decimal` |
| `boolean`         | Valor verdadeiro ou falso.                          | `ativo:"Ativo":boolean` |
| `date`            | Data (YYYY-MM-DD).                                  | `nascimento:"Data de Nascimento":date` |
| `datetime`        | Data e hora (YYYY-MM-DD HH:MM:SS).                  | `data_servico:"Data do Serviço":datetime` |
| `timestamp`       | Timestamp com data e hora.                          | `ultimo_acesso:"Último Acesso":timestamp` |
| `json`            | Dados no formato JSON.                              | `configuracoes:"Configurações":json` |
| `email`           | Endereço de e-mail.                                 | `email:"E-mail":email` |
| `moeda`           | Valor monetário (float com formatação).             | `preco:"Preço":moeda` |
| `file`            | Upload de arquivo único.                            | `documento:"Documento":file` |
| `files`           | Upload de múltiplos arquivos.                       | `anexos:"Anexos":files` |

### 🔗 **Relacionamentos Especiais**

| **Padrão**        | **Tipo de Relacionamento** | **Descrição**                           | **Exemplo** |
|-------------------|-----------------------------|-----------------------------------------|-------------|
| `id_campo`        | **belongsTo (1:1)**         | Chave estrangeira simples              | `id_usuario:"Usuário":integer` |
| `pModelo`         | **belongsToMany (1:N)**     | Relacionamento pivot/muitos-para-muitos | `servicos:"Serviços":pServico` |

### O que será gerado?

#### **CRUD Básico:**
- **Modelo**: `app/Models/Post.php`
- **Controlador**: `app/Http/Controllers/PostController.php`
- **Views Vue**: `resource/js/pages/Post/index.vue` e `create.vue`
- **Migração**: `database/migrations/[timestamp]_create_posts_table.php`
- **Rotas**: Adicionadas em `routes/web.php`
- **Menu**: Item adicionado em `resource/js/components/AppSidebar.vue`

#### **CRUD com Relacionamentos Pivot:**
Quando usar campos `pModelo`, também será gerado:
- **Componente Vue**: `resource/js/components/RelationshipManyField.vue`
- **Relacionamentos no Model**: `belongsToMany` com TODOs
- **Sync no Controller**: Métodos para sincronizar relacionamentos
- **Interface Rica**: Lista dinâmica, modal para criação rápida, badges

### 💡 **Exemplos Práticos**

#### Exemplo 1: Blog Simples
```bash
php artisan make:crud Post titulo:"Título":string conteudo:"Conteúdo":text id_autor:"Autor":integer publicado:"Publicado":boolean
```

#### Exemplo 2: E-commerce com Relacionamentos
```bash
php artisan make:crud Produto nome:"Nome":string preco:"Preço":moeda descricao:"Descrição":text categorias:"Categorias":pCategoria tags:"Tags":pTag
```

#### Exemplo 3: Sistema de Ordem de Serviço
```bash
php artisan make:crud OrdemServico numero:"Número":string id_pet:"Pet":integer data_servico:"Data do Serviço":datetime servicos:"Serviços":pServico observacoes:"Observações":text
```

#### 🏥 **Exemplo Completo: Clínica Veterinária**

Sistema completo de clínica de pets com donos, pets, ordens de serviço e serviços:

```bash
# 1. Limpar o banco de dados
php artisan migrate:fresh

# 2. Criar os modelos base
php artisan make:crud Dono nome:"Nome":string email:"E-mail":email telefone:"Telefone":string endereco:"Endereço":text

php artisan make:crud Pet nome:"Nome":string especie:"Espécie":string raca:"Raça":string idade:"Idade":integer id_dono:"Dono":integer peso:"Peso":float observacoes:"Observações":text

php artisan make:crud Servico nome:"Nome":string descricao:"Descrição":text preco:"Preço":moeda duracao_minutos:"Duração (min)":integer ativo:"Ativo":boolean

# 3. Criar a tabela pivot como CRUD (para gerenciar itens da ordem detalhadamente)
php artisan make:crud OrdemServicoServico id_ordemservico:"Ordem de Serviço":integer id_servico:"Serviço":integer

# 4. Criar ordem de serviço com relacionamentos
php artisan make:crud OrdemServico numero:"Número":string id_pet:"Pet":integer data_servico:"Data do Serviço":datetime servicos:"Serviços":pServico valor_total:"Valor Total":moeda status:"Status":string observacoes:"Observações":text

# 5. Executar migrações
php artisan migrate
```

**O que isso criará:**

- **Donos** → podem ter vários pets
- **Pets** → pertencem a um dono, podem ter várias ordens
- **Serviços** → catálogo de serviços da clínica
- **OrdemServicoServico** → tabela pivot com campos extras (quantidade, preço, subtotal)
- **Ordens de Serviço** → para um pet específico, com múltiplos serviços

**Relacionamentos gerados automaticamente:**
```php
// Dono.php - TODO: Adicionar relacionamento
public function pets() {
    return $this->hasMany(App\Models\Pet::class, 'id_dono');
}

// Pet.php - Relacionamentos já configurados
public function dono() {
    return $this->belongsTo(App\Models\Dono::class, 'id_dono');
}

// OrdemServico.php - Relacionamentos configurados automaticamente
public function pet() {
    return $this->belongsTo(App\Models\Pet::class, 'id_pet');
}
public function servicos() {
    return $this->belongsToMany(App\Models\Servico::class, 'ordemservicoservicos', 'id_ordemservico', 'id_servico')
        ->withPivot('quantidade', 'preco_unitario', 'subtotal')
        ->withTimestamps();
}

// Servico.php - Relacionamento reverso
public function ordemServicos() {
    return $this->belongsToMany(App\Models\OrdemServico::class, 'ordemservicoservicos', 'id_servico', 'id_ordemservico')
        ->withPivot('quantidade', 'preco_unitario', 'subtotal')
        ->withTimestamps();
}
```

**Interface resultante:**
- ✅ **Formulário de Ordem** com dropdown de pets e lista dinâmica de serviços
- ✅ **Modal de criação rápida** para novos serviços durante o agendamento
- ✅ **Badges visuais** dos serviços selecionados
- ✅ **Sync automático** dos relacionamentos no backend com valores padrão para campos pivot
- ✅ **CRUD da tabela pivot** para edição detalhada de quantidades e preços
- ✅ **Sync automático** dos relacionamentos no backend

---

## 🎨 Interface de Relacionamentos

O componente `RelationshipManyField` oferece uma interface rica e intuitiva para gerenciar relacionamentos 1:N:

### ✨ Recursos do Componente:
- **🏷️ Badges Visuais**: Lista clara dos itens selecionados com opção de remoção
- **🔽 Dropdown Inteligente**: Mostra apenas itens disponíveis para seleção
- **➕ Criação Rápida**: Modal para criar novos registros relacionados sem sair da tela
- **🎯 TypeScript**: Totalmente tipado para maior segurança
- **📱 Responsivo**: Interface adaptada para diferentes tamanhos de tela
- **🔄 Sync Automático**: Sincronização automática com o backend via Inertia.js

### 🖼️ Como Funciona:
1. **Seleção**: Escolha itens do dropdown
2. **Visualização**: Veja os selecionados como badges
3. **Remoção**: Clique no X para remover itens
4. **Criação**: Use o modal para criar novos registros relacionados
5. **Submissão**: O formulário principal envia tudo automaticamente

---

## 🔧 Melhorias Técnicas Recentes

### ✅ **Sistema Otimizado de Geração**
- **Geração Condicional**: Imports e blocos de código são gerados apenas quando necessários
- **Performance Aprimorada**: Vue.js carrega apenas componentes utilizados
- **TypeScript Completo**: Tipagem total para segurança de código

### 🗑️ **Soft Delete Avançado**
- **Filtros Automáticos**: Todos os CRUDs incluem `where('deleted', 0)` automaticamente
- **Controle de Estado**: Sistema completo de exclusão lógica
- **Interface Limpa**: Apenas registros ativos são exibidos por padrão

### 🔗 **Relacionamentos Inteligentes**
- **Pivot Tables**: Suporte completo a relacionamentos N:N com interface rica
- **Sync Automático**: Gravação e edição de relacionamentos sem complexidade
- **Componentes Dinâmicos**: Interface responsiva para gerenciamento de relações

---

## 💻 Interface de Desenvolvimento para CRUDs

Além do comando `make:crud`, você pode acessar uma interface gráfica para gerar CRUDs diretamente no navegador. Isso facilita o processo de configuração e criação.

### Como acessar?
1. Certifique-se de que o servidor Laravel está em execução:
   ```bash
   php artisan serve
   ```
2. Acesse a rota específica no navegador:
   ```
   http://localhost:8000/dev/crud-generator
   ```

### Recursos da interface
- **Formulário intuitivo**: Preencha os campos necessários para gerar o CRUD.
- **Pré-visualização**: Veja os arquivos que serão criados antes de confirmar.
- **Geração instantânea**: Clique em "Gerar" para criar o CRUD e visualizar os resultados.

---

## 📋 Exemplo de Saída

### CRUD Básico:
```
✅ CRUD para 'Post' gerado com sucesso!
------------------------------------------------
📄 Arquivos criados:
  • Modelo: app/Models/Post.php
  • Controlador: app/Http/Controllers/PostController.php
  • Views:
    - resource/js/pages/Post/index.vue
    - resource/js/pages/Post/create.vue
  • Migração: database/migrations/2025_11_22_123456_create_posts_table.php
  • Rotas adicionadas em: routes/web.php
  • Item de menu adicionado em: resource/js/components/AppSidebar.vue
------------------------------------------------
📋 Campos configurados:
  • 'Título' - titulo - string
  • 'Conteúdo' - conteudo - text
------------------------------------------------
🎉 Pronto! Verifique os arquivos e ajuste conforme necessário.
```

### CRUD com Relacionamentos Pivot:
```
✅ CRUD para 'OrdemServico' gerado com sucesso!
------------------------------------------------
📄 Arquivos criados:
  • Modelo: app/Models/OrdemServico.php
  • Controlador: app/Http/Controllers/OrdemServicoController.php
  • Views:
    - resource/js/pages/OrdemServico/index.vue
    - resource/js/pages/OrdemServico/create.vue
  • Migração: database/migrations/2025_11_22_123456_create_ordem_servicos_table.php
  • Rotas adicionadas em: routes/web.php
  • Item de menu adicionado em: resource/js/components/AppSidebar.vue
------------------------------------------------
📝 TODOs:
  • Configurar relacionamento pivot para 'servicos' no modelo.
  • Ajustar sync do relacionamento 'servicos' no controlador.
  • Verificar se a tabela pivot existe ou criar migration para ela.
  ⚠️  Certifique-se de que o modelo relacionado 'Servico' existe.
  ⚠️  Ajustar campo de exibição no controller (atualmente usando 'nome').
------------------------------------------------
🔗 Relacionamentos Pivot detectados:
  • Campo: servicos → Tabela pivot sugerida: ordemservico_servico
------------------------------------------------
📦 Componente criado:
  • RelationshipManyField: resource/js/components/RelationshipManyField.vue
------------------------------------------------
🎉 Pronto! Verifique os arquivos e ajuste conforme necessário.
```

---

## 🎯 **Sistema Completo Testado: Clínica Veterinária**

O exemplo da clínica veterinária demonstra todo o poder do LaraVue IzCRUD com:

### 📋 **Funcionalidades Implementadas:**
- ✅ **CRUD Completo**: Donos, Pets, Serviços e Ordens de Serviço
- ✅ **Relacionamentos 1:1**: Pet → Dono (chave estrangeira)
- ✅ **Relacionamentos 1:N**: OrdemServico → Serviços (pivot table)
- ✅ **Soft Delete**: Todos os registros com exclusão lógica
- ✅ **Interface Rica**: Modals, dropdowns dinâmicos, badges visuais
- ✅ **TypeScript**: Tipagem completa para segurança
- ✅ **Validação**: Regras de validação automáticas

### 🚀 **Execute em 30 segundos:**
```bash
git clone https://github.com/NicolasKovsky/izicrud.git
cd izicrud
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate:fresh

# Copie e cole todos os comandos de uma vez:
php artisan make:crud Dono nome:"Nome":string email:"E-mail":email telefone:"Telefone":string endereco:"Endereço":text && php artisan make:crud Pet nome:"Nome":string especie:"Espécie":string raca:"Raça":string idade:"Idade":integer id_dono:"Dono":integer peso:"Peso":float observacoes:"Observações":text && php artisan make:crud Servico nome:"Nome":string descricao:"Descrição":text preco:"Preço":moeda duracao_minutos:"Duração (min)":integer ativo:"Ativo":boolean && php artisan make:crud OrdemServicoServico id_ordemservico:"Ordem de Serviço":integer id_servico:"Serviço":integer && php artisan make:crud OrdemServico numero:"Número":string id_pet:"Pet":integer data_servico:"Data do Serviço":datetime servicos:"Serviços":pServico valor_total:"Valor Total":moeda status:"Status":string observacoes:"Observações":text

php artisan migrate && npm run dev
```

**Pronto!** Sistema completo funcionando em `http://localhost:8000` 🎉

---

## 🤝 Contribuições

Contribuições são bem-vindas! Sinta-se à vontade para abrir *issues* ou enviar *pull requests* no [repositório GitHub](https://github.com/NicolasKovsky/izicrud).

---

## 👤 Autor

**Nicolas Slujalkovsky**
Entusiasta de Laravel e Vue.js, apaixonado por criar soluções que tornam o desenvolvimento mais rápido e eficiente.
📬 **Conecte-se comigo**: [linkedin.com/in/nicolas-slujalkovsky](https://linkedin.com/in/nicolas-slujalkovsky)
🌐 **Veja mais projetos**: [github.com/nicolas-slujalkovsky](https://github.com/nicolas-slujalkovsky)

---

## 📜 Licença

Este projeto é licenciado sob a [MIT License](LICENSE).

