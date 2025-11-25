# 🚀 LaraVue IzCRUD

> Gerador de CRUDs inteligente para **Laravel 12 + Vue.js** com relacionamentos avançados e TypeScript.

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?style=flat&logo=vue.js)](https://vuejs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?style=flat&logo=typescript)](https://typescriptlang.org)

**Transforme uma ideia em aplicação completa em 30 segundos.**

Criado por [**Nicolas Slujalkovsky**](https://github.com/NicolasKovsky) 💙

---

## 🚀 Quick Start

```bash
# Clone e configure
git clone https://github.com/NicolasKovsky/izicrud.git && cd izicrud
composer install && npm install
cp .env.example .env && php artisan key:generate && php artisan migrate

# Primeiro CRUD
php artisan make:crud Post titulo:"Título":string conteudo:"Conteúdo":text
php artisan migrate && php artisan serve & npm run dev
```

✅ **Pronto!** Acesse `http://localhost:8000`

---

## ⚡ O que Gera Automaticamente?

### **Backend**
- **Model** com relacionamentos
- **Controller** com validação completa
- **Migration** com índices
- **Routes** integradas

### **Frontend**
- **Views Vue** responsivas + TypeScript
- **Componentes** para relacionamentos N:N
- **Menu** atualizado automaticamente
- **Interface rica** com modals e badges

---

## 📚 Sintaxe de Campos

### **Básicos**
```bash
nome:"Nome":string          # Texto curto
descricao:"Descrição":text  # Texto longo
idade:"Idade":integer       # Número
preco:"Preço":moeda        # Valor monetário
ativo:"Ativo":boolean      # Sim/Não
data:"Data":date           # Data
```

### **Upload**
```bash
foto:"Foto":file           # Arquivo único
anexos:"Anexos":files      # Múltiplos arquivos
```

### **Relacionamentos**
```bash
id_categoria:"Categoria":integer     # 1:1 (belongsTo)
tags:"Tags":pTag                     # N:N (pivot)
```

---

## 🎯 Exemplo Prático

### **🏥 Clínica Veterinária**
```bash
php artisan migrate:fresh

php artisan make:crud Dono nome:"Nome":string email:"E-mail":email telefone:"Telefone":string endereco:"Endereço":text

php artisan make:crud Pet nome:"Nome":string especie:"Espécie":string raca:"Raça":string idade:"Idade":integer id_dono:"Dono":integer peso:"Peso":float observacoes:"Observações":text

php artisan make:crud Servico nome:"Nome":string descricao:"Descrição":text preco:"Preço":moeda duracao_minutos:"Duração (min)":integer ativo:"Ativo":boolean

php artisan make:crud OrdemServicoServico id_ordemservico:"Ordem de Serviço":integer id_servico:"Serviço":integer

php artisan make:crud OrdemServico numero:"Número":string id_pet:"Pet":integer data_servico:"Data do Serviço":datetime servicos:"Serviços":pServico valor_total:"Valor Total":moeda status:"Status":string observacoes:"Observações":text

php artisan migrate

```

**🎯 Demonstra:** belongsTo, hasMany, relacionamentos pivot

---

## 🎨 Interface Rica

### **Relacionamentos N:N**
- **Badges visuais** dos itens selecionados
- **Dropdown inteligente** com busca
- **Modal de criação** rápida
- **Sync automático** no backend

### **Design Moderno**
- **Responsivo** mobile-first
- **Dark mode** automático
- **TypeScript** completo
- **Soft delete** integrado

---

## 🔧 Comandos Úteis

```bash
# Interface de desenvolvimento
php artisan serve  # http://localhost:8000/dev/crud-generator

# Exemplos rápidos
php artisan make:crud Post titulo:"Título":string conteudo:"Conteúdo":text
php artisan make:crud Produto nome:"Nome":string preco:"Preço":moeda categorias:"Categorias":pCategoria
```

---

## 🛠️ Requisitos

- **PHP** 8.2+ | **Laravel** 12+ | **Node.js** 18+ | **Composer** 2.0+

---

## 👤 Autor

<div align="center">

**Nicolas Slujalkovsky**
*Full Stack Developer*

[![LinkedIn](https://img.shields.io/badge/LinkedIn-nicolas--slujalkovsky-0077B5?style=flat&logo=linkedin)](https://linkedin.com/in/nicolas-slujalkovsky)
[![GitHub](https://img.shields.io/badge/GitHub-NicolasKovsky-181717?style=flat&logo=github)](https://github.com/NicolasKovsky)

**⭐ Ajudou? Deixe uma estrela!**

*Feito com 💙 para a comunidade Laravel*

</div>
