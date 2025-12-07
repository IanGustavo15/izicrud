# LaraVue IzCRUD - AI Coding Agent Instructions

## Project Overview
This is a **Laravel 12 + Vue.js 3 + TypeScript** CRUD generator system that creates complete full-stack CRUDs with a single command. The core value is rapid prototyping through intelligent code generation.

## Architecture Patterns

### CRUD Generator Command
- **Core Command**: `php artisan make:crud ModelName field:"Label":type`
- **Location**: `app/Console/Commands/MakeCrud.php` (1000+ lines)
- **Generates**: Model, Controller, Views (Vue), Migration, Routes, Menu items
- **Stubs Directory**: `stubs/` contains all templates (`.stub` files)

### Field Type System
```bash
# Basic types
nome:"Nome":string          # Text input
descricao:"Descrição":text  # Textarea  
preco:"Preço":moeda        # Currency input
ativo:"Ativo":boolean      # Checkbox
data:"Data":date           # Date picker

# Relationships  
id_categoria:"Categoria":integer     # belongsTo (dropdown)
tags:"Tags":pTag                     # belongsToMany (multi-select)
```

### Model Conventions
- All models use `$displayLabels` array for UI field labels
- Soft deletes via `deleted` field (not Laravel's soft deletes)
- Auto-generated relationships with TODO comments for reverse relations
- Example: `app/Models/Pet.php` shows belongsTo pattern

### Frontend Structure
- **Framework**: Inertia.js + Vue 3 + TypeScript
- **UI Components**: Custom components in `resources/js/components/ui/`
- **Pages**: `resources/js/pages/{Model}/index.vue` and `create.vue`
- **Layout**: Single `AppLayout.vue` with sidebar navigation

## Development Workflows

### Creating New CRUDs
1. Run: `php artisan make:crud ModelName fields...`
2. Run: `php artisan migrate`
3. Auto-generated: Controller, Model, Views, Routes, Menu item

### Field Type Examples
```bash
# Veterinary clinic example
php artisan make:crud Pet nome:"Nome":string id_dono:"Dono":integer peso:"Peso":float
php artisan make:crud OrdemServico servicos:"Serviços":pServico  # Many-to-many
```

### Testing & Development
- **Frontend Dev**: `npm run dev` (Vite)
- **Backend**: Laravel Artisan commands
- **Database**: `php artisan migrate:fresh` for clean slate

## Code Conventions

### Model Relationships
- `id_fieldname` = belongsTo relationship
- `pModelname` prefix = belongsToMany relationship  
- Generated models include TODO comments for reverse relationships
- All pivots use `deleted` column for soft deletes

### Vue Component Patterns
- TypeScript interfaces for props: `{ id: number; field: string }[]`
- Inertia forms: `useForm()` composable
- UI state: `ref()` for alerts, dialogs, loading states
- Computed properties for filtering/sorting

### File Organization
- Controllers: `app/Http/Controllers/{Model}Controller.php`
- Views: `resources/js/pages/{Model}/` (index.vue, create.vue)
- Routes: Auto-appended to `routes/web.php`
- Menu: Auto-added to sidebar navigation

## Integration Points

### Backend-Frontend Bridge
- Inertia.js handles SPA-like experience with server-side routing
- Props passed via controller return `Inertia::render()`
- No API layer - direct controller-to-view data flow

### Database Patterns
- Migration generator creates fields based on type system
- Foreign keys auto-detected from `id_` prefix
- Pivot tables for many-to-many relationships

## Key Files to Reference
- `app/Console/Commands/MakeCrud.php` - Core generator logic
- `stubs/crud.model.stub` - Model template with $displayLabels
- `resources/js/pages/Dono/index.vue` - Example generated index view
- `README.MD` - Complete syntax reference and examples

## Critical Notes
- Always run `php artisan migrate` after CRUD generation
- Relationship fields require manual addition of reverse relationships
- UI uses shadcn/vue components with Tailwind CSS
- All views are responsive and include dark mode support
