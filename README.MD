# 🚀 LaraVue IzCRUD

> Gerador de CRUDs inteligente para **Laravel 12 + Vue.js** com relacionamentos automáticos e TypeScript.

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?style=flat&logo=vue.js)](https://vuejs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?style=flat&logo=typescript)](https://typescriptlang.org)

**Transforme uma ideia em aplicação completa em 30 segundos.**

---

## 🚀 Quick Start

```bash
# Setup
git clone https://github.com/NicolasKovsky/izicrud.git && cd izicrud
composer install && npm install
cp .env.example .env && php artisan key:generate && php artisan migrate

# Primeiro CRUD
php artisan make:crud Post titulo:"Título":string conteudo:"Conteúdo":text
php artisan migrate && npm run dev
```

✅ **Pronto!** Acesse `http://localhost:8000`

---

## ⚡ O que Gera?

**Backend:** Model + Controller + Migration + Routes
**Frontend:** Views Vue + TypeScript + Menu automático
**Plus:** Relacionamentos N:N com interface visual

---

## 📚 Sintaxe

### **Campos Básicos**
```bash
nome:"Nome":string          # Texto
descricao:"Descrição":text  # Texto longo
preco:"Preço":moeda        # Valor monetário
ativo:"Ativo":boolean      # Sim/Não
data:"Data":date           # Data
foto:"Foto":file           # Upload único
anexos:"Anexos":files      # Upload múltiplo
```

### **Relacionamentos**
```bash
id_categoria:"Categoria":integer     # 1:1 (belongsTo)
tags:"Tags":pTag                     # N:N (belongsToMany)
```

---

## 🎯 Exemplo: Clínica Veterinária

```bash
php artisan migrate:fresh

# Donos
php artisan make:crud Dono nome:"Nome":string email:"E-mail":email telefone:"Telefone":string foto:"Foto":file --c

# Pets com relacionamento
php artisan make:crud Pet nome:"Nome":string especie:"Espécie":string id_dono:"Dono":integer peso:"Peso":float

# Serviços
php artisan make:crud Servico nome:"Nome":string preco:"Preço":moeda ativo:"Ativo":boolean

# Tabela pivot
php artisan make:crud OrdemServicoServico id_ordemservico:"Ordem de Serviço":integer id_servico:"Serviço":integer

# Ordens com relacionamento N:N e documentos da consulta
php artisan make:crud OrdemServico numero:"Número":string id_pet:"Pet":integer servicos:"Serviços":pServico valor_total:"Valor Total":moeda documentos:"Documentos":files --c

php artisan migrate
```

**🎯 Gera:** Sistema completo com relacionamentos belongsTo, hasMany e belongsToMany + upload/download de arquivos

---

## 🎨 Recursos

- **Dropdowns inteligentes** - Detecta campos automaticamente via `$displayLabels`
- **Relacionamentos N:N** - Interface com badges e seleção múltipla
- **TypeScript completo** - Tipagem automática em todos os componentes
- **Soft delete integrado** - Sistema de exclusão reversível
- **Design responsivo** - Mobile-first com dark mode

---

## 🛠️ Requisitos

**PHP** 8.2+ | **Laravel** 12+ | **Node.js** 18+ | **Composer** 2.0+

---

<div align="center">

**Nicolas Slujalkovsky** - *Full Stack Developer*

[![GitHub](https://img.shields.io/badge/GitHub-NicolasKovsky-181717?style=flat&logo=github)](https://github.com/NicolasKovsky)

**⭐ Ajudou? Deixe uma estrela!** | *Feito com 💙 para a comunidade Laravel*

</div>
