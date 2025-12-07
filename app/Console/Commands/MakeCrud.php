<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeCrud extends Command
{

    protected $signature = 'make:crud {modelName : Nome do modelo (ex: Post)} {fields?* : Campos no formato nome:"Nome da Coluna":tipo (ex: title:"Título":string ou servicos:"Serviços":pServico)}';

    protected $description = 'Gera um CRUD completo com modelo, controlador, views Vue, migração, rotas e item de menu no sidebar. Suporta relacionamentos 1:1 (id_campo) e 1:N (pModelo).';

    public function handle()
    {
        $modelNameArg = explode(':', $this->argument('modelName'));
        $model = $modelNameArg[0];
        $modelTitle = count($modelNameArg) > 1 ? trim($modelNameArg[1], "'\"") : $model;
        $controller = $model . 'Controller';
        $viewFolder = ucfirst($model);
        $routePrefix = strtolower($model);
        $modelLower = strtolower($model);
        $modelPluralTitle = Str::plural($modelTitle);
        $modelPluralLower = strtolower($modelPluralTitle);

        $fields = $this->parseFields($this->argument('fields'));

        $this->createModel($model, $fields);
        $this->createController($controller, $model, $viewFolder, $routePrefix, $modelLower, $modelTitle, $modelPluralTitle, $fields);
        $this->createViews($viewFolder, $routePrefix, $model, $modelLower, $modelTitle, $modelPluralTitle, $modelPluralLower, $fields);
        $this->appendControllerImport($controller);
        $this->appendRoutes($controller, $routePrefix);
        $this->appendMenuItem($modelPluralTitle, $routePrefix);
        $this->createMigration($model, $fields);

        $this->informativo($model, $controller, $viewFolder, $fields);

    }

    protected function parseFields($fieldsArg)
    {
        $fields = [];
        foreach ($fieldsArg as $fieldArg) {
            // Validate the field format
            $parts = explode(':', $fieldArg);
            if (count($parts) !== 3) {
                throw new \InvalidArgumentException("Invalid field format: {$fieldArg}. Expected format is 'name:label:type'.");
            }

            [$name, $label, $type] = $parts;
            $type = trim($type, "'\""); // Remover aspas mas manter case

            // Detectar se é relacionamento pivot (formato: pModelo)
            $isPivot = str_starts_with($type, 'p') && ctype_upper($type[1] ?? '');

            // Validação e mapeamento de tipos (case insensitive)
            $validTypes = [
                'string', 'text', 'integer', 'biginteger', 'float', 'double',
                'decimal', 'boolean', 'date', 'datetime', 'timestamp', 'json', 'email', 'moeda', 'file', 'files'
            ];

            if (!$isPivot && !in_array(strtolower($type), $validTypes)) {
                $this->warn("Tipo '{$type}' não é válido. Tipos suportados: " . implode(', ', $validTypes) . " ou formato pModelo para relacionamentos.");
                continue;
            }

            // Mapear tipos para os nomes corretos do Laravel
            $mappedType = match (true) {
                $isPivot => 'pivot',
                strtolower($type) === 'biginteger' => 'bigInteger',
                strtolower($type) === 'moeda' => 'float',
                default => strtolower($type)
            };

            $fields[] = [
                'name' => $name,
                'label' => trim($label, "'\""),
                'type' => $mappedType,
                'is_foreign' => str_starts_with($name, 'id_'), // Detecta se é chave estrangeira
                'is_pivot' => $isPivot, // Detecta se é relacionamento pivot
                'related_model' => $this->getRelatedModelName($name, $type), // Nome do modelo relacionado
            ];
        }
        return $fields;
    }

    protected function getRelatedModelName($fieldName, $type = null)
    {
        // Se for relacionamento pivot (pModelo), extrai o nome do modelo
        if ($type && str_starts_with($type, 'p') && ctype_upper($type[1] ?? '')) {
            return substr($type, 1); // Remove 'p' prefix - pServico -> Servico
        }

        // Extrai o nome do modelo relacionado a partir do campo id_
        if (str_starts_with($fieldName, 'id_')) {
            return ucfirst(Str::camel(str_replace('id_', '', $fieldName)));
        }

        return null;
    }

    protected function createModel($model, $fields)
    {
        $fillable = implode(', ', array_map(fn($f) => "'{$f['name']}'", array_filter($fields, fn($f) => !$f['is_pivot'])));
        $displayLabels = $this->generateDisplayLabels($fields);
        $casts = implode(', ', array_filter(array_map(fn($f) => match ($f['type']) {
            'files' => "'{$f['name']}' => 'array'",
            'float', 'double', 'decimal', 'moeda' => "'{$f['name']}' => 'float'",
            'date', 'datetime', 'timestamp' => "'{$f['name']}' => 'date'",
            default => null
        }, array_filter($fields, fn($f) => !$f['is_pivot']))));

        $relationships = $this->generateRelationships($fields, $model);

        $tableName = $this->generateTableName($model);

        $stub = File::get(base_path('stubs/crud.model.stub'));
        $stub = str_replace(['{{model}}', '{{tableName}}', '{{fillable}}', '{{displayLabels}}', '{{casts}}', '{{relationships}}'], [$model, $tableName, $fillable, $displayLabels, $casts, $relationships], $stub);
        File::put(app_path("Models/{$model}.php"), $stub);
    }

    protected function createController($controller, $model, $viewFolder, $routePrefix, $modelLower, $modelTitle, $modelPluralTitle, $fields)
    {
        $validationRules = implode("\n            ", array_map(fn($f) => "'{$f['name']}' => '" . match ($f['type']) {
            'integer', 'bigInteger' => 'nullable|integer',
            'float', 'double', 'decimal', 'moeda' => 'nullable|numeric',
            'email' => 'nullable|email',
            'date', 'datetime', 'timestamp' => 'nullable|date',
            'boolean' => 'nullable|boolean',
            'json' => 'nullable|json',
            'file' => 'nullable|file|max:10240', // 10MB max
            'files' => 'nullable|array',
            'pivot' => 'nullable|array', // Pivot fields são arrays de IDs
            default => 'nullable|string|max:255'
        } . "',", array_filter($fields, fn($f) => !$f['is_pivot'] || $f['type'] === 'pivot')));

        // Adicionar validação específica para arquivos múltiplos
        $fileValidationRules = [];
        foreach ($fields as $field) {
            if ($field['type'] === 'files') {
                $fileValidationRules[] = "            '{$field['name']}.*' => 'nullable|file|max:10240',";
            }
        }
        if (!empty($fileValidationRules)) {
            $validationRules .= "\n" . implode("\n", $fileValidationRules);
        }

        $dropdownData = implode("\n        ", array_filter(array_map(
            function ($f) {
                if ($f['is_foreign']) {
                    $displayField = $this->getModelDisplayField($f['related_model']);
                    return "\${$f['name']}Options = \\App\\Models\\{$f['related_model']}::where('deleted', 0)->orderBy('{$displayField}', 'asc')->get()->map(function (\$item) {\n                return [\n                    'value' => \$item->id,\n                    'label' => \$item->{$displayField}\n                ];\n            });";
                } elseif ($f['is_pivot']) {
                    $displayField = $this->getModelDisplayField($f['related_model']);
                    return "\${$f['name']}Options = \\App\\Models\\{$f['related_model']}::where('deleted', 0)->orderBy('{$displayField}', 'asc')->get()->map(function (\$item) {\n                return [\n                    'value' => \$item->id,\n                    'label' => \$item->{$displayField}\n                ];\n            });";
                }
                return '';
            },
            $fields
        )));

        $pivotSync = implode("\n        ", array_filter(array_map(
            fn($f) => $f['is_pivot'] ? "// Sincronizar relacionamento pivot para {$f['name']}\n        if (!empty(\$servicosIds)) {\n            \$model->{$f['name']}()->sync(\$servicosIds);\n        }" : '',
            $fields
        )));

        $storeMethod = <<<EOT
    public function store(Request \$request)
    {
        \$data = \$request->validate([
            {$validationRules}
        ]);

{$this->generateFileUploadCode($fields, '        ', 'model', false)}
        // Remover campos pivot dos dados principais
        {$this->generatePivotDataRemoval($fields)}
        \$model = {$model}::create(\$data);
        {$this->generatePivotProcessingCall($fields, false, '$model')}

        return redirect()->route('{$routePrefix}.index')->with('success', '{$modelTitle} criado com sucesso!');
    }
EOT;

        $updateMethod = <<<EOT
    public function update(Request \$request, {$model} \${$modelLower})
    {
{$this->generateDynamicValidation($fields, '        ')}
{$this->generateSimplifiedFileUploadCode($fields, '        ', $modelLower)}
        // Remover campos pivot dos dados principais
        {$this->generatePivotDataRemoval($fields)}
        \${$modelLower}->update(\$data);
        {$this->generatePivotProcessingCall($fields, true, '$' . $modelLower)}

        return redirect()->route('{$routePrefix}.index')->with('success', '{$modelTitle} atualizado com sucesso!');
    }
EOT;

        $createMethod = <<<EOT
    public function create()
    {
        {$dropdownData}

        return inertia('{$viewFolder}/create', [
            'sidebarNavItems' => \$this->getSidebarNavItems()
            {$this->generateDropdownProps($fields)}
        ]);
    }
EOT;

        $editMethod = <<<EOT
    public function edit({$model} \${$modelLower})
    {
        if (\${$modelLower}->deleted) {
            return redirect()->route('{$routePrefix}.index')->with('error', '{$modelTitle} excluído.');
        }

        {$dropdownData}

        // Preparar dados para edição
        \$itemData = \${$modelLower}->toArray();
        {$this->generatePivotDataLoading($fields, $modelLower)}

        return inertia('{$viewFolder}/create', [
            'item' => \$itemData,
            'sidebarNavItems' => \$this->getSidebarNavItems()
            {$this->generateDropdownProps($fields)}
        ]);
    }
EOT;

        $stub = File::get(base_path('stubs/crud.controller.stub'));
        // Adicionar imports necessários para modelos pivot
        $pivotImports = $this->generatePivotImports($fields);

        // Adicionar método helper para pivot se necessário
        $pivotHelperMethod = $this->generatePivotHelperMethod($fields);

        $stub = str_replace(
            ['{{model}}', '{{controller}}', '{{viewFolder}}', '{{routePrefix}}', '{{modelLower}}', '{{modelTitle}}', '{{modelPluralTitle}}', '{{validationRules}}', '{{createMethod}}', '{{editMethod}}', '{{storeMethod}}', '{{updateMethod}}', '{{pivotHelper}}', '{{pivotImports}}'],
            [$model, $controller, $viewFolder, $routePrefix, $modelLower, $modelTitle, $modelPluralTitle, $validationRules, $createMethod, $editMethod, $storeMethod, $updateMethod, $pivotHelperMethod, $pivotImports],
            $stub
        );
        File::put(app_path("Http/Controllers/{$controller}.php"), $stub);
    }

    private function generateDropdownProps($fields)
    {
        return implode("\n            ", array_filter(array_map(
            function ($f) {
                if ($f['is_foreign']) {
                    return ",'{$f['name']}Options' => \${$f['name']}Options";
                } elseif ($f['is_pivot']) {
                    return ",'{$f['name']}Options' => \${$f['name']}Options";
                }
                return '';
            },
            $fields
        )));
    }

    protected function createViews($viewFolder, $routePrefix, $model, $modelLower, $modelTitle, $modelPluralTitle, $modelPluralLower, $fields)
    {
        $viewPath = resource_path("js/pages/{$viewFolder}");
        File::ensureDirectoryExists($viewPath);

        $propFields = implode('; ', array_map(fn($f) => "{$f['name']}: " . match ($f['type']) {
            'boolean' => 'boolean',
            'integer', 'bigInteger', 'float', 'double', 'decimal' => 'number',
            'pivot' => 'number[]',
            'file' => 'string',
            'files' => 'string',
            default => 'string',
        }, $fields));

        $formFields = implode(",\n    ", array_map(fn($f) => "{$f['name']}: props.item?.{$f['name']}" . match (true) {
            $f['is_foreign'] => "?.toString() || ''", // Campos de chave estrangeira devem ser strings para o Select
            $f['type'] === 'boolean' => ' || false',
            $f['type'] === 'integer' || $f['type'] === 'bigInteger' || $f['type'] === 'float' || $f['type'] === 'double' || $f['type'] === 'decimal' => ' || 0',
            $f['type'] === 'pivot' => ' || []',
            $f['type'] === 'file' => ' || null as File | null',
            $f['type'] === 'files' => ' || null as File[] | null',
            default => "?.toString() || ''",
        }, $fields));

        // Adicionar filesToRemove aos campos do formulário
        $formFields .= ",\n    filesToRemove: [] as {field: string, index?: number}[]";

        // Refatoração 1: Remover a lógica hardcoded de dropdowns
        // A lógica de props de dropdowns já é gerada no Controller,
        // e o nome da prop segue o padrão 'id_campoOptions'.
        $dropdownOptions = ''; // Não é mais necessário gerar esta string

        $formInputs = implode("\n                ", array_map(
            fn($f) => match (true) {
                // Adição da lógica para campos de chave estrangeira (id_)
                $f['is_foreign'] => $this->generateSelectComponent($f),

                // Adição da lógica para campos pivot (relacionamentos 1:N)
                $f['is_pivot'] => $this->generateRelationshipComponent($f),

                $f['type'] === 'boolean' => "<div class=\"flex items-center space-x-2\">\n                    <Checkbox id=\"{$f['name']}\" v-model=\"form.{$f['name']}\" />\n                    <Label for=\"{$f['name']}\">{$f['label']}</Label>\n                </div>",
                $f['type'] === 'text' => "<div>\n                    <Label for=\"{$f['name']}\">{$f['label']}</Label>\n                    <Textarea id=\"{$f['name']}\" v-model=\"form.{$f['name']}\" placeholder=\"Digite {$f['label']}\" rows=\"4\" />\n                </div>",
                $f['type'] === 'date' || $f['type'] === 'datetime' || $f['type'] === 'timestamp' => "<div>\n                    <Label for=\"{$f['name']}\">{$f['label']}</Label>\n                    <Input id=\"{$f['name']}\" v-model=\"form.{$f['name']}\" type=\"" . ($f['type'] === 'date' ? 'date' : 'datetime-local') . "\" />\n                </div>",
                $f['type'] === 'json' => "<div>\n                    <Label for=\"{$f['name']}\">{$f['label']}</Label>\n                    <Textarea id=\"{$f['name']}\" v-model=\"form.{$f['name']}\" placeholder='Exemplo: {\"key\": \"value\"}' rows=\"4\" />\n                </div>",
                $f['type'] === 'email' => "<div>\n                    <Label for=\"{$f['name']}\">{$f['label']}</Label>\n                    <Input id=\"{$f['name']}\" v-model=\"form.{$f['name']}\" type=\"email\" placeholder=\"Digite {$f['label']}\" />\n                </div>",
                $f['type'] === 'integer' || $f['type'] === 'bigInteger' => "<div>\n                    <Label for=\"{$f['name']}\">{$f['label']}</Label>\n                    <Input id=\"{$f['name']}\" v-model.number=\"form.{$f['name']}\" type=\"number\" step=\"1\" placeholder=\"Digite {$f['label']}\" />\n                </div>",
                $f['type'] === 'float' || $f['type'] === 'double' || $f['type'] === 'decimal' => "<div>\n                    <Label for=\"{$f['name']}\">{$f['label']}</Label>\n                    <Input id=\"{$f['name']}\" v-model.number=\"form.{$f['name']}\" type=\"number\" step=\"0.01\" placeholder=\"Digite {$f['label']}\" />\n                </div>",
                $f['type'] === 'moeda' => "<div>\n                    <Label for=\"{$f['name']}\">{$f['label']}</Label>\n                    <Input id=\"{$f['name']}\" v-model.number=\"form.{$f['name']}\" type=\"text\" placeholder=\"Digite {$f['label']}\" @input=\"form.{$f['name']} = parseFloat(form.{$f['name']}.replace(/[R$\\s,]/g, '').replace('.', '').replace(',', '.'))\" />\n                </div>",
                $f['type'] === 'file' => $this->generateFileComponent($f),
                $f['type'] === 'files' => $this->generateFilesComponent($f),
                default => "<div>\n                    <Label for=\"{$f['name']}\">{$f['label']}</Label>\n                    <Input id=\"{$f['name']}\" v-model=\"form.{$f['name']}\" type=\"text\" placeholder=\"Digite {$f['label']}\" />\n                </div>",
            },
            $fields
        ));

        $createStub = File::get(base_path('stubs/crud.create.vue.stub'));

        // Gerar imports condicionalmente
        $conditionalImports = $this->generateConditionalImports($fields);

        // Refatoração 2: Adicionar as props de dropdowns ao defineProps
        $dropdownProps = implode("\n    ", array_filter(array_map(
            function ($f) {
                if ($f['is_foreign']) {
                    return "{$f['name']}Options: { value: number; label: string }[];";
                } elseif ($f['is_pivot']) {
                    return "{$f['name']}Options: { value: number; label: string }[];";
                }
                return '';
            },
            $fields
        )));

        // Verificar se há campos pivot para adicionar import do componente
        $hasPivotFields = array_filter($fields, fn($f) => $f['is_pivot']);
        $relationshipImport = $hasPivotFields ? "\nimport RelationshipManyField from '@/components/RelationshipManyField.vue';" : '';

    //     $createStub = str_replace(
    //         '<script setup>',
    //         "<script setup>\n{$selectImports}\n\nconst props = defineProps<{\n    item?: Record<string, any>;\n    sidebarNavItems: { title: string; href: string }[];\n    // TODO: Ajustar a prop 'usuarios' para ser dinâmica conforme o model relacionado
    // usuarios: { id: number; name: string }[];\n    {$dropdownProps}\n}>();\n\n", // Removido $dropdownOptions
    //         $createStub
    //     );

        $createStub = str_replace(
            ['{{modelPluralTitle}}', '{{routePrefix}}', '{{modelPluralLower}}', '{{modelTitle}}', '{{modelLower}}', '{{propFields}}', '{{formFields}}', '{{formInputs}}', '{{dropdownProps}}', '{{conditionalImports}}'],
            [$modelPluralTitle, $routePrefix, $modelPluralLower, $modelTitle, $modelLower, $propFields, $formFields, $formInputs, $dropdownProps, $conditionalImports],
            $createStub
        );

        File::put("{$viewPath}/create.vue", $createStub);

        // Processar os campos para a tabela
        $tableHeaders = implode("\n                            ", array_map(
            fn($f) => "<TableHead class=\"cursor-pointer\" @click=\"toggleSort('{$f['name']}')\">{$f['label']}<span v-if=\"sortColumn === '{$f['name']}'\" class=\"ml-2\">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span></TableHead>",
            $fields
        ));

        $tableCells = implode("\n                            ", array_map(
            fn($f) => "<TableCell>{{ item.{$f['name']} }}</TableCell>",
            $fields
        ));

        $filterConditions = implode(' || ', array_map(
            fn($f) => "(item.{$f['name']} || '').toString().toLowerCase().includes(query)",
            $fields
        ));

        $indexStub = File::get(base_path('stubs/crud.index.vue.stub'));
        $indexStub = str_replace(
            ['{{modelPluralTitle}}', '{{routePrefix}}', '{{modelPluralLower}}', '{{modelTitle}}', '{{modelLower}}', '{{tableHeaders}}', '{{tableCells}}', '{{filterConditions}}', '{{propFields}}'],
            [$modelPluralTitle, $routePrefix, $modelPluralLower, $modelTitle, $modelLower, $tableHeaders, $tableCells, $filterConditions, $propFields],
            $indexStub
        );

        File::put("{$viewPath}/index.vue", $indexStub);
    }

    /**
     * Gera o componente Select (dropdown) para campos de chave estrangeira.
     * @param array $field
     * @return string
     */
    private function generateSelectComponent(array $field): string
    {
        $propName = "{$field['name']}Options";
        $label = $field['label'];
        $fieldName = $field['name'];

        return <<<VUE
<div>
    <div>
                    <Label for="{$fieldName}">{$label}</Label>
                    <Select v-model="form.{$fieldName}">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Selecione um {$label}" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="option in (props.{$propName} || [])" :key="option.value" :value="option.value.toString()">
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
</div>
VUE;
    }

    /**
     * Gera o componente de relacionamento para campos pivot (1:N).
     * @param array $field
     * @return string
     */
    private function generateRelationshipComponent(array $field): string
    {
        $propName = "{$field['name']}Options";
        $label = $field['label'];
        $fieldName = $field['name'];
        $relatedModel = $field['related_model'];

        return <<<VUE
<div>
                    <RelationshipManyField
                        v-model="form.{$fieldName}"
                        :available-items="props.{$propName} || []"
                        label="{$label}"
                        related-model="{$relatedModel}"
                        placeholder="Selecione {$label}"
                    />
                </div>
VUE;
    }

    protected function appendControllerImport($controller)
    {
        $webPath = base_path('routes/web.php');
        $webContent = File::get($webPath);
        $importStub = File::get(base_path('stubs/crud.controller.import.stub'));
        $import = str_replace('{{controller}}', $controller, $importStub);

        if (!Str::contains($webContent, $import)) {
            $controllerSection = "// Controllers\n";
            $pos = strpos($webContent, $controllerSection);
            if ($pos !== false) {
                $insertPos = $pos + strlen($controllerSection);
                $webContent = substr($webContent, 0, $insertPos) . $import . "\n" . substr($webContent, $insertPos);
                File::put($webPath, $webContent);
            } else {
                $this->warn("Seção '// Controllers' não encontrada em web.php. Adicione-a manualmente e tente novamente.");
            }
        }
    }

    protected function appendRoutes($controller, $routePrefix)
    {
        $webPath = base_path('routes/web.php');
        $webContent = File::get($webPath);
        $routeStub = File::get(base_path('stubs/crud.routes.stub'));
        $routes = str_replace(['{{controller}}', '{{routePrefix}}'], [$controller, $routePrefix], $routeStub);

        if (!Str::contains($webContent, $routes)) {
            $routeSection = "// Rotas\n";
            $pos = strpos($webContent, $routeSection);
            if ($pos !== false) {
                $insertPos = $pos + strlen($routeSection);
                $webContent = substr($webContent, 0, $insertPos) . $routes . "\n" . substr($webContent, $insertPos);
                File::put($webPath, $webContent);
            } else {
                $this->warn("Seção '// Rotas' não encontrada em web.php. Adicione-a manualmente e tente novamente.");
            }
        }
    }

    protected function appendMenuItem($modelPluralTitle, $routePrefix)
    {
        $sidebarPath = resource_path('js/components/AppSidebar.vue');
        $sidebarContent = File::get($sidebarPath);
        $menuStub = File::get(base_path('stubs/crud.menu.item.stub'));
        $menuItem = str_replace(
            ['{{modelPluralTitle}}', '{{routePrefix}}'],
            [$modelPluralTitle, $routePrefix],
            $menuStub
        );

        if (!Str::contains($sidebarContent, $menuItem)) {
            $menuSection = "// Novos Itens do Menu\n";
            $pos = strpos($sidebarContent, $menuSection);
            if ($pos !== false) {
            // Adiciona o menuItem acima do comentário
            $webContent = substr($sidebarContent, 0, $pos) . $menuItem . "\n" . substr($sidebarContent, $pos);
            File::put($sidebarPath, $webContent);
            } else {
            $this->warn("Seção '// Novos Itens do Menu' não encontrada em AppSidebar.vue. Adicione-a manualmente e tente novamente.");
            }
        }
    }

    protected function createMigration($model, $fields)
    {
        $tableName = strtolower(Str::plural($model));
        $migrationName = 'create_' . $tableName . '_table';
        $migrationPath = database_path('migrations');
        $migrationFileName = date('Y_m_d_His') . '_' . $migrationName . '.php';
        $migrationFilePath = $migrationPath . '/' . $migrationFileName;

        if (!File::exists($migrationFilePath)) {
            File::ensureDirectoryExists($migrationPath);
            $stub = File::get(base_path('stubs/crud.migration.stub'));

            $columns = implode("\n            ", array_filter(array_map(function ($f) {
                // Pular campos pivot - eles não são colunas da tabela principal
                if ($f['is_pivot']) {
                    return null;
                }

                // Se for chave estrangeira, usa a sintaxe foreignId()->constrained()
                if ($f['is_foreign']) {
                    $relatedTable = strtolower(Str::plural($f['related_model']));
                    return "\$table->foreignId('{$f['name']}')"
                        . "\n                ->constrained('{$relatedTable}')"
                        . "\n                ->cascadeOnDelete();";
                }

                // Para os demais campos, usa o mapeamento de tipos
                $columnType = match (true) {
                    $f['type'] === 'email' => 'string',
                    $f['type'] === 'moeda' => 'float', // Sugestão: usar 'decimal' para precisão
                    $f['type'] === 'file' => 'string',
                    $f['type'] === 'files' => 'json',
                    default => $f['type']
                };

                $nullable = in_array($f['type'], ['file', 'files']) ? '->nullable()' : '';
                return "\$table->{$columnType}('{$f['name']}'){$nullable};";

            }, $fields)));

            $columns .= "\n            \$table->boolean('deleted')->default(false);";
            $columns .= "\n            \$table->timestamps();";
            $stub = str_replace(['{{model}}', '{{table}}', '{{columns}}'], [$model, $tableName, $columns], $stub);
            File::put($migrationFilePath, $stub);
            $this->info("Migração criada: {$migrationFileName}");
        } else {
            $this->warn("Migração já existe: {$migrationFileName}");
        }
    }

    protected function informativo($model, $controller, $viewFolder, $fields)
    {
        $this->info("✅ CRUD para '{$model}' gerado com sucesso!");

        $this->info("📄 Arquivos criados:");
        $this->info("  • Modelo: app/Models/{$model}.php");
        $this->info("  • Controlador: app/Http/Controllers/{$controller}.php");
        $this->info("  • View de criação: resource/js/pages/{$viewFolder}/create.vue");
        $this->info("  • View de listagem: resource/js/pages/{$viewFolder}/index.vue");
        $this->info("  • Migração: database/migrations/" . date('Y_m_d_His') . "_create_" . strtolower(Str::plural($model)) . "_table.php");
        $this->info("  • Rotas adicionadas em: routes/web.php");
        $this->info("  • Item de menu adicionado em: resource/js/components/AppSidebar.vue");

        $this->info("\n📝 TODOs:");

        foreach ($fields as $field) {
            if ($field['is_foreign']) {
                $this->info("  • Configurar dropdown para o campo '{$field['name']}' no controlador e vue.");
                $this->warn("⚠️  Certifique-se de que o modelo relacionado '{$field['related_model']}' existe e está configurado corretamente.");
                $this->info("  📋 Relacionamento belongsTo criado automaticamente");
                $this->warn("  🔄 Copie o relacionamento reverso para o modelo {$field['related_model']}.php");
            }

            if ($field['is_pivot']) {
                $this->info("  • Relacionamento pivot '{$field['name']}' configurado automaticamente.");
                $this->info("  • Sync do relacionamento '{$field['name']}' implementado no controlador.");
                $this->info("  • Verificar se a tabela pivot existe ou criar migration para ela.");
                $this->warn("⚠️  Certifique-se de que o modelo relacionado '{$field['related_model']}' existe.");
                $this->info("  📋 Relacionamento belongsToMany criado automaticamente");
                $this->warn("  🔄 Copie o relacionamento reverso para o modelo {$field['related_model']}.php");
            }
        }

        $hasPivotFields = array_filter($fields, fn($f) => $f['is_pivot']);
        if ($hasPivotFields) {
            $this->info("\n🔗 Relacionamentos Pivot detectados:");
            foreach ($hasPivotFields as $field) {
                $pivotTableName = $this->generatePivotTableName($model, $field['related_model']);
                $this->info("  • Campo: {$field['name']} → Tabela pivot sugerida: {$pivotTableName}");
            }
            $this->info("\n📦 Componente criado:");
            $this->info("  • RelationshipManyField: resource/js/components/RelationshipManyField.vue");
        }

        $this->info("\n🎉 Pronto! Verifique os arquivos e ajuste conforme necessário.");
        $this->info("\nCriado por: Nicolas Slujalkovsky");
        $this->info("Starter Kit Laravel 12 + Vue - Feito para acelerar seu desenvolvimento!");
        $this->info("Conecte-se comigo: linkedin.com/in/nicolas-slujalkovsky");
    }

    /**
     * Gera método centralizado para processar relacionamentos pivot
     */
    private function generatePivotHelperMethod(array $fields): string
    {
        $pivotFields = array_filter($fields, fn($f) => $f['is_pivot']);

        if (empty($pivotFields)) {
            return '';
        }

        $fieldProcessing = [];
        foreach ($pivotFields as $field) {
            $fieldName = $field['name'];
            $pivotModelName = $this->generatePivotModelName($fieldName);
            $singularName = $this->getSingularName($fieldName);

            $fieldProcessing[] = "        if (isset(\$data['{$fieldName}'])) {";
            $fieldProcessing[] = "            // Remove relacionamentos existentes apenas durante update";
            $fieldProcessing[] = "            if (\$isUpdate && \$model->id) {";
            $fieldProcessing[] = "                {$pivotModelName}::where('id_" . strtolower(explode(':', $this->argument('modelName'))[0]) . "', \$model->id)->delete();";
            $fieldProcessing[] = "            }";
            $fieldProcessing[] = "            ";
            $fieldProcessing[] = "            // Cria novos relacionamentos";
            $fieldProcessing[] = "            foreach (\$data['{$fieldName}'] as \$id_{$singularName}) {";
            $fieldProcessing[] = "                {$pivotModelName}::create([";
            $fieldProcessing[] = "                    'id_" . strtolower(explode(':', $this->argument('modelName'))[0]) . "' => \$model->id,";
            $fieldProcessing[] = "                    'id_{$singularName}' => \$id_{$singularName},";
            $fieldProcessing[] = "                    'deleted' => 0,";
            $fieldProcessing[] = "                ]);";
            $fieldProcessing[] = "            }";
            $fieldProcessing[] = "        }";
        }

        return "    private function processPivotRelationships(\$model, array \$data, bool \$isUpdate = false)\n    {\n" .
               implode("\n", $fieldProcessing) . "\n    }";
    }    /**
     * Gera o código de processamento de pivot para store/update
     */
    private function generatePivotProcessing(array $fields, string $modelName, bool $isCreate = true): string
    {
        $pivotFields = array_filter($fields, fn($f) => $f['is_pivot']);

        if (empty($pivotFields)) {
            return $isCreate ?
                "\$model = {$modelName}::create(\$data);" :
                "\$model->update(\$data);";
        }

        if ($isCreate) {
            return "\$model = {$modelName}::create(\$data);\n        \$this->processPivotRelationships(\$model, \$request->all());";
        } else {
            return "\$model->update(\$data);\n        \$this->processPivotRelationships(\$model, \$request->all());";
        }
    }    /**
     * Gera o nome do modelo pivot baseado no campo
     */
    private function generatePivotModelName(string $fieldName): string
    {
        // Remove 's' do final se existir (servicos -> servico) e converte para PascalCase
        $singular = $this->getSingularName($fieldName);
        // Pega o nome do modelo atual (ex: OrdemServico) e combina com o singular (ex: Servico)
        $currentModel = explode(':', $this->argument('modelName'))[0];
        return $currentModel . ucfirst($singular);
    }

    /**
     * Gera imports necessários para modelos pivot
     */
    private function generatePivotImports(array $fields): string
    {
        $pivotFields = array_filter($fields, fn($f) => $f['is_pivot']);

        if (empty($pivotFields)) {
            return '';
        }

        $imports = [];
        foreach ($pivotFields as $field) {
            $pivotModelName = $this->generatePivotModelName($field['name']);
            $imports[] = "use App\\Models\\{$pivotModelName};";
        }

        return implode("\n", $imports);
    }

    /**
     * Gera o nome da tabela baseado no modelo (mesmo padrão das migrações)
     */
    private function generateTableName(string $model): string
    {
        // Usa a mesma lógica das migrações: strtolower + s
        $tableName = strtolower($model) . 's';
        return "protected \$table = '{$tableName}';";
    }

    /**
     * Gera código para carregar dados pivot na edição
     */
    private function generatePivotDataLoading(array $fields, string $modelVar): string
    {
        $pivotFields = array_filter($fields, fn($f) => $f['is_pivot']);

        if (empty($pivotFields)) {
            return '// Dados carregados para edição';
        }

        $loadings = [];
        $currentModel = strtolower(explode(':', $this->argument('modelName'))[0]);

        foreach ($pivotFields as $field) {
            $fieldName = $field['name'];
            $pivotModelName = $this->generatePivotModelName($fieldName);
            $singularName = $this->getSingularName($fieldName);

            $loadings[] = "// Carregar {$fieldName} relacionados";
            $loadings[] = "\$itemData['{$fieldName}'] = {$pivotModelName}::where('id_{$currentModel}', \${$modelVar}->id)";
            $loadings[] = "    ->where('deleted', 0)";
            $loadings[] = "    ->pluck('id_{$singularName}')";
            $loadings[] = "    ->map(fn(\$id) => (int) \$id)";
            $loadings[] = "    ->toArray();";
        }

        return implode("\n        ", $loadings);
    }

    /**
     * Gera código para remover campos pivot dos dados principais
     */
    private function generatePivotDataRemoval(array $fields): string
    {
        $pivotFields = array_filter($fields, fn($f) => $f['is_pivot']);

        if (empty($pivotFields)) {
            return '// Dados validados, prosseguir com criação';
        }

        $removals = [];
        foreach ($pivotFields as $field) {
            $removals[] = "unset(\$data['{$field['name']}']);";
        }

        return implode("\n        ", $removals);
    }    /**
     * Gera imports condicionalmente baseado nos tipos de campos
     */
    private function generateConditionalImports(array $fields): string
    {
        $imports = [];

        // Verificar se há campos de select (foreign keys ou pivot)
        $hasSelectFields = array_filter($fields, fn($f) => $f['is_foreign'] || $f['is_pivot']);
        if ($hasSelectFields) {
            $imports[] = "import {\n" .
                "  Select,\n" .
                "  SelectTrigger,\n" .
                "  SelectValue,\n" .
                "  SelectContent,\n" .
                "  SelectItem\n" .
                "} from '@/components/ui/select';";
        }

        // Verificar se há campos de checkbox
        $hasCheckboxFields = array_filter($fields, fn($f) => $f['type'] === 'boolean');
        if ($hasCheckboxFields) {
            $imports[] = "import { Checkbox } from '@/components/ui/checkbox';";
        }

        // Verificar se há campos de textarea
        $hasTextAreaFields = array_filter($fields, fn($f) => in_array($f['type'], ['text', 'json']));
        if ($hasTextAreaFields) {
            $imports[] = "import { Textarea } from '@/components/ui/textarea';";
        }

        // Verificar se há campos pivot para adicionar import do componente
        $hasPivotFields = array_filter($fields, fn($f) => $f['is_pivot']);
        if ($hasPivotFields) {
            $imports[] = "import RelationshipManyField from '@/components/RelationshipManyField.vue';";
        }

        return implode("\n", $imports);
    }

    /**
     * Gera o código de upload de arquivos apenas se há campos de arquivo
     */
    private function generateFileUploadCode(array $fields, string $indent = '', string $modelLower = 'model', bool $isUpdate = true): string
    {
        $fileFields = array_filter($fields, fn($f) => in_array($f['type'], ['file', 'files']));

        if (empty($fileFields)) {
            return '';
        }

        $code = [];
        $code[] = $indent . "// Handle file uploads if necessary";

        foreach ($fileFields as $field) {
            if ($field['type'] === 'file') {
                $code[] = $indent . "if (\$request->hasFile('{$field['name']}')) {";
                if ($isUpdate) {
                    $code[] = $indent . "    // Se há um arquivo antigo, remover";
                    $code[] = $indent . "    if (\$oldFile = \${$modelLower}->{$field['name']}) {";
                    $code[] = $indent . "        Storage::disk('public')->delete(\$oldFile);";
                    $code[] = $indent . "    }";
                }
                $code[] = $indent . "    \$data['{$field['name']}'] = \$request->file('{$field['name']}')->store('uploads', 'public');";
                $code[] = $indent . "}";
                if ($isUpdate) {
                    $code[] = $indent . "// Se não há arquivo novo, manter o existente (não incluir no \$data)";
                }
            } elseif ($field['type'] === 'files') {
                $code[] = $indent . "if (\$request->hasFile('{$field['name']}')) {";
                if ($isUpdate) {
                    $code[] = $indent . "    // Obter arquivos existentes";
                    $code[] = $indent . "    \$existingFiles = \${$modelLower}->{$field['name']} ? (is_array(\${$modelLower}->{$field['name']}) ? \${$modelLower}->{$field['name']} : [\${$modelLower}->{$field['name']}]) : [];";
                    $code[] = $indent . "    ";
                    $code[] = $indent . "    // Fazer upload dos novos arquivos";
                    $code[] = $indent . "    \$newFiles = array_map(";
                    $code[] = $indent . "        fn(\$file) => \$file->store('uploads', 'public'),";
                    $code[] = $indent . "        \$request->file('{$field['name']}')";
                    $code[] = $indent . "    );";
                    $code[] = $indent . "    ";
                    $code[] = $indent . "    // Combinar arquivos existentes com novos";
                    $code[] = $indent . "    \$data['{$field['name']}'] = array_merge(\$existingFiles, \$newFiles);";
                } else {
                    $code[] = $indent . "    \$data['{$field['name']}'] = array_map(";
                    $code[] = $indent . "        fn(\$file) => \$file->store('uploads', 'public'),";
                    $code[] = $indent . "        \$request->file('{$field['name']}')";
                    $code[] = $indent . "    );";
                }
                $code[] = $indent . "}";
                if ($isUpdate) {
                    $code[] = $indent . "// Se não há arquivos novos, manter os existentes (não incluir no \$data)";
                }
            }
        }

        return implode("\n", $code) . "\n";
    }

    /**
     * Gera código simplificado de upload de arquivo para o método update
     */
    private function generateSimplifiedFileUploadCode(array $fields, string $indent = '', string $modelLower = 'model'): string
    {
        $fileFields = array_filter($fields, fn($f) => in_array($f['type'], ['file', 'files']));

        if (empty($fileFields)) {
            return $indent . "\$data = \$request->validate(\$validationRules);\n";
        }

        $code = [];
        $code[] = $indent . "\$data = \$request->validate(\$validationRules);";
        $code[] = "";
        $code[] = $indent . "// Handle file uploads if necessary";

        // Gerar código para arquivos únicos
        $singleFileFields = array_filter($fileFields, fn($f) => $f['type'] === 'file');
        foreach ($singleFileFields as $field) {
            $code[] = $indent . "if (\$request->hasFile('{$field['name']}')) {";
            $code[] = $indent . "    // Se há um arquivo antigo, remover";
            $code[] = $indent . "    if (\$oldFile = \${$modelLower}->{$field['name']}) {";
            $code[] = $indent . "        Storage::disk('public')->delete(\$oldFile);";
            $code[] = $indent . "    }";
            $code[] = $indent . "    \$data['{$field['name']}'] = \$request->file('{$field['name']}')->store('uploads', 'public');";
            $code[] = $indent . "}";
            $code[] = "";
        }

        // Gerar código para múltiplos arquivos
        $multipleFileFields = array_filter($fileFields, fn($f) => $f['type'] === 'files');
        foreach ($multipleFileFields as $field) {
            $varName = "current" . ucfirst($field['name']);
            $code[] = $indent . "// Processar campo de múltiplos arquivos";
            $code[] = $indent . "\${$varName} = \${$modelLower}->{$field['name']} ?? [];";
            $code[] = "";
        }

        // Gerar lógica única de remoção para todos os campos de arquivo
        if (!empty($fileFields)) {
            $code[] = $indent . "// Se há arquivos para remover, processar primeiro";
            $code[] = $indent . "if (\$request->has('filesToRemove') && is_array(\$request->filesToRemove)) {";
            $code[] = $indent . "    foreach (\$request->filesToRemove as \$removal) {";

            // Lógica para arquivos únicos
            foreach ($singleFileFields as $field) {
                $code[] = $indent . "        if (\$removal['field'] === '{$field['name']}' && !isset(\$removal['index'])) {";
                $code[] = $indent . "            // Arquivo único";
                $code[] = $indent . "            if (\${$modelLower}->{$field['name']} && Storage::disk('public')->exists(\${$modelLower}->{$field['name']})) {";
                $code[] = $indent . "                Storage::disk('public')->delete(\${$modelLower}->{$field['name']});";
                $code[] = $indent . "            }";
                $code[] = $indent . "            \$data['{$field['name']}'] = null;";
                $code[] = $indent . "        }";
            }

            // Lógica para múltiplos arquivos
            foreach ($multipleFileFields as $field) {
                $varName = "current" . ucfirst($field['name']);
                $code[] = $indent . "        elseif (\$removal['field'] === '{$field['name']}' && isset(\$removal['index'])) {";
                $code[] = $indent . "            if (isset(\${$varName}[\$removal['index']])) {";
                $code[] = $indent . "                \$filePath = \${$varName}[\$removal['index']];";
                $code[] = $indent . "                if (Storage::disk('public')->exists(\$filePath)) {";
                $code[] = $indent . "                    Storage::disk('public')->delete(\$filePath);";
                $code[] = $indent . "                }";
                $code[] = $indent . "                unset(\${$varName}[\$removal['index']]);";
                $code[] = $indent . "            }";
                $code[] = $indent . "        }";
            }

            $code[] = $indent . "    }";

            // Reindexar arrays de múltiplos arquivos
            foreach ($multipleFileFields as $field) {
                $varName = "current" . ucfirst($field['name']);
                $code[] = $indent . "    \${$varName} = array_values(\${$varName}); // Reindexar";
            }

            $code[] = $indent . "}";
            $code[] = "";
        }

        // Gerar lógica de adição para múltiplos arquivos
        foreach ($multipleFileFields as $field) {
            $varName = "current" . ucfirst($field['name']);
            $code[] = $indent . "// Se há novos arquivos para adicionar";
            $code[] = $indent . "if (\$request->hasFile('{$field['name']}')) {";
            $code[] = $indent . "    \$newFiles = array_map(";
            $code[] = $indent . "        fn(\$file) => \$file->store('uploads', 'public'),";
            $code[] = $indent . "        \$request->file('{$field['name']}')";
            $code[] = $indent . "    );";
            $code[] = $indent . "    \${$varName} = array_merge(\${$varName}, \$newFiles);";
            $code[] = $indent . "}";
            $code[] = "";
            $code[] = $indent . "// Atualizar apenas se houve mudanças";
            $code[] = $indent . "if (\$request->has('filesToRemove') || \$request->hasFile('{$field['name']}')) {";
            $code[] = $indent . "    \$data['{$field['name']}'] = \${$varName};";
            $code[] = $indent . "}";
            $code[] = "";
        }

        return implode("\n", $code) . "\n";
    }

    /**
     * Gera validação dinâmica que inclui arquivos apenas quando necessário
     */
    private function generateDynamicValidation(array $fields, string $indent = ''): string
    {
        $fileFields = array_filter($fields, fn($f) => in_array($f['type'], ['file', 'files']));
        $nonFileFields = array_filter($fields, fn($f) => !in_array($f['type'], ['file', 'files']) && (!$f['is_pivot'] || $f['type'] === 'pivot'));

        $code = [];
        $code[] = $indent . "\$validationRules = [";

        // Adicionar campos não-arquivo
        foreach ($nonFileFields as $field) {
            $rule = match ($field['type']) {
                'string' => 'nullable|string|max:255',
                'text' => 'nullable|string|max:255',
                'integer' => 'nullable|integer',
                'boolean' => 'nullable|boolean',
                'date' => 'nullable|date',
                'decimal', 'float' => 'nullable|numeric',
                'moeda' => 'nullable|numeric|min:0',
                default => 'nullable'
            };
            $code[] = $indent . "    '{$field['name']}' => '{$rule}',";
        }

        $code[] = $indent . "];";
        $code[] = "";

        // Adicionar validação para arquivos apenas se estão sendo enviados
        if (!empty($fileFields)) {
            $code[] = $indent . "// Adicionar validação para arquivos apenas se estão sendo enviados";
            foreach ($fileFields as $field) {
                if ($field['type'] === 'file') {
                    $code[] = $indent . "if (\$request->hasFile('{$field['name']}')) {";
                    $code[] = $indent . "    \$validationRules['{$field['name']}'] = 'nullable|file|max:10240';";
                    $code[] = $indent . "}";
                } elseif ($field['type'] === 'files') {
                    $code[] = $indent . "if (\$request->hasFile('{$field['name']}')) {";
                    $code[] = $indent . "    \$validationRules['{$field['name']}'] = 'nullable|array';";
                    $code[] = $indent . "    \$validationRules['{$field['name']}.*'] = 'nullable|file|max:10240';";
                    $code[] = $indent . "}";
                }
            }
            $code[] = "";
        }

        $code[] = $indent . "\$data = \$request->validate(\$validationRules);";

        return implode("\n", $code);
    }

    /**
     * Gera a chamada para processamento de pivot apenas se há campos pivot
     */
    private function generatePivotProcessingCall(array $fields, bool $isUpdate, string $modelVar = '$model'): string
    {
        $pivotFields = array_filter($fields, fn($f) => $f['is_pivot']);

        if (empty($pivotFields)) {
            return '';
        }

        return "\$this->processPivotRelationships({$modelVar}, \$request->all(), " . ($isUpdate ? 'true' : 'false') . ");";
    }

    /**
     * Gera relacionamentos automáticos para o modelo
     */
    private function generateRelationships(array $fields, string $model): string
    {
        $relationships = [];
        $todos = [];

        foreach ($fields as $field) {
            if ($field['is_foreign']) {
                $relatedModel = $field['related_model'];
                $relationshipName = strtolower($relatedModel);

                $relationships[] = "    // Relacionamento belongsTo automático";
                $relationships[] = "    public function {$relationshipName}()";
                $relationships[] = "    {";
                $relationships[] = "        return \$this->belongsTo(\\App\\Models\\{$relatedModel}::class, '{$field['name']}');";
                $relationships[] = "    }";
                $relationships[] = "";

                // TODO para o modelo relacionado (hasMany)
                $todos[] = "    // TODO: Cole no modelo {$relatedModel}.php:";
                $todos[] = "    // public function " . strtolower(Str::plural($model)) . "()";
                $todos[] = "    // {";
                $todos[] = "    //     return \$this->hasMany(\\App\\Models\\{$model}::class, '{$field['name']}')->where('deleted', 0);";
                $todos[] = "    // }";
                $todos[] = "";

            } elseif ($field['is_pivot']) {
                $relatedModel = $field['related_model'];
                $relationshipName = strtolower($field['name']);
                $pivotTableName = $this->generatePivotTableName($model, $relatedModel);

                $relationships[] = "    // Relacionamento belongsToMany automático";
                $relationships[] = "    public function {$relationshipName}()";
                $relationships[] = "    {";
                $relationships[] = "        return \$this->belongsToMany(\\App\\Models\\{$relatedModel}::class, '{$pivotTableName}', 'id_" . strtolower($model) . "', 'id_" . strtolower(Str::singular($field['name'])) . "')";
                $relationships[] = "            ->wherePivot('deleted', 0)";
                $relationships[] = "            ->withTimestamps();";
                $relationships[] = "        // TODO: Use ->withPivot() se a tabela pivot tiver campos extras além dos IDs";
                $relationships[] = "    }";
                $relationships[] = "";

                // TODO para o modelo relacionado (belongsToMany reverso)
                $todos[] = "    // TODO: Cole no modelo {$relatedModel}.php:";
                $todos[] = "    // public function " . strtolower(Str::plural($model)) . "()";
                $todos[] = "    // {";
                $todos[] = "    //     return \$this->belongsToMany(\\App\\Models\\{$model}::class, '{$pivotTableName}', 'id_" . strtolower(Str::singular($field['name'])) . "', 'id_" . strtolower($model) . "')";
                $todos[] = "    //         ->wherePivot('deleted', 0)";
                $todos[] = "    //         ->withTimestamps();";
                $todos[] = "    // }";
                $todos[] = "";
            }
        }

        // Adiciona TODOs como comentários no final
        if (!empty($todos)) {
            $relationships[] = "    /*";
            $relationships[] = "     * ⚠️  RELACIONAMENTOS REVERSOS - Copie e cole nos modelos indicados:";
            $relationships[] = "     */";
            $relationships = array_merge($relationships, $todos);
        }

        return implode("\n", $relationships);
    }

    /**
     * Gera o array $displayLabels baseado nos campos
     */
    private function generateDisplayLabels(array $fields): string
    {
        $labels = [];
        foreach ($fields as $field) {
            // Pular campos pivot pois eles não estão no fillable
            if ($field['is_pivot']) {
                continue;
            }

            if ($field['name'] === 'deleted') {
                $labels[] = 'null';
            } else {
                $labels[] = "'{$field['label']}'";
            }
        }
        return implode(', ', $labels);
    }

    /**
     * Extrai o campo display de um modelo existente baseado no $displayLabels
     */
    private function getModelDisplayField(string $modelName): string
    {
        $modelPath = app_path("Models/{$modelName}.php");
        if (!File::exists($modelPath)) {
            return $this->guessBestDisplayField($modelName);
        }

        $content = File::get($modelPath);

        // Extrair fillable e displayLabels
        $fillable = $this->extractFillableFields($content);
        $displayLabels = $this->extractDisplayLabels($content);

        // Encontrar o primeiro campo não-nulo em displayLabels que não seja 'deleted'
        for ($i = 0; $i < count($fillable); $i++) {
            if (isset($displayLabels[$i]) &&
                $displayLabels[$i] !== null &&
                $fillable[$i] !== 'deleted') {
                return $fillable[$i];
            }
        }

        // Fallback para convenção
        return $this->guessBestDisplayField($modelName, $fillable);
    }

    /**
     * Extrai $displayLabels de um modelo existente
     */
    private function extractDisplayLabels(string $content): array
    {
        if (preg_match('/protected\s+\$displayLabels\s*=\s*\[(.*?)\];/s', $content, $matches)) {
            $arrayContent = $matches[1];
            $labels = [];

            // Parse básico do array
            $items = explode(',', $arrayContent);
            foreach ($items as $item) {
                $item = trim($item);
                if ($item === 'null') {
                    $labels[] = null;
                } elseif (preg_match('/[\'"]([^\'"]*)[\'"]/', $item, $match)) {
                    $labels[] = $match[1];
                } else {
                    $labels[] = null;
                }
            }
            return $labels;
        }

        return [];
    }

    /**
     * Adivinha o melhor campo para display baseado em convenções
     */
    private function guessBestDisplayField(string $modelName, array $fillable = []): string
    {
        $priorities = ['nome', 'title', 'name', 'email', 'description'];

        foreach ($priorities as $field) {
            if (in_array($field, $fillable)) {
                return $field;
            }
        }

        // Se não encontrou nenhum, pega o primeiro campo que não seja deleted ou id
        foreach ($fillable as $field) {
            if (!in_array($field, ['deleted', 'id'])) {
                return $field;
            }
        }

        return 'id'; // último recurso
    }

    /**
     * Extrai campos fillable de um modelo
     */
    private function extractFillableFields(string $content): array
    {
        if (preg_match('/protected\s+\$fillable\s*=\s*\[(.*?)\];/s', $content, $matches)) {
            $arrayContent = $matches[1];
            $fields = [];

            // Parse do array fillable
            $items = explode(',', $arrayContent);
            foreach ($items as $item) {
                if (preg_match('/[\'"]([^\'"]*)[\'"]/', trim($item), $match)) {
                    $fields[] = $match[1];
                }
            }
            return $fields;
        }

        return [];
    }

    /**
     * Gera componente para campo de arquivo único com preview e gerenciamento
     */
    private function generateFileComponent(array $field): string
    {
        return "<div>
                    <Label for=\"{$field['name']}\">{$field['label']}</Label>

                    <!-- Arquivo existente -->
                    <div v-if=\"isEditing && currentFiles.{$field['name']}\" class=\"mb-3 p-3 border rounded-lg bg-gray-50 dark:bg-gray-800\">
                        <div class=\"flex items-center justify-between\">
                            <div class=\"flex items-center space-x-2\">
                                <svg class=\"w-5 h-5 text-blue-500\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"/>
                                </svg>
                                <span class=\"text-sm text-gray-600 dark:text-gray-300\">{{ getFileName(currentFiles.{$field['name']}) }}</span>
                            </div>
                            <div class=\"flex space-x-2\">
                                <Button @click.prevent=\"downloadFile(currentFiles.{$field['name']})\" type=\"button\" variant=\"outline\" size=\"sm\">
                                    <svg class=\"w-4 h-4 mr-1\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"/>
                                    </svg>
                                    Download
                                </Button>
                                <Button @click=\"removeFileLocally('{$field['name']}')\" type=\"button\" variant=\"destructive\" size=\"sm\">
                                    <svg class=\"w-4 h-4 mr-1\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\"/>
                                    </svg>
                                    Remover
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Upload de novo arquivo -->
                    <Input id=\"{$field['name']}\" @change=\"(e: Event) => form.{$field['name']} = (e.target as HTMLInputElement).files?.[0] || null\" type=\"file\" />
                    <p class=\"text-xs text-gray-500 mt-1\">{{ isEditing ? 'Selecione um novo arquivo para substituir o atual' : 'Selecione um arquivo' }}</p>
                </div>";
    }

    /**
     * Gera componente para campo de múltiplos arquivos com preview e gerenciamento
     */
    private function generateFilesComponent(array $field): string
    {
        return "<div>
                    <Label for=\"{$field['name']}\">{$field['label']}</Label>

                    <!-- Arquivos existentes -->
                    <div v-if=\"isEditing && currentFiles.{$field['name']} && Array.isArray(currentFiles.{$field['name']}) && currentFiles.{$field['name']}.length > 0\" class=\"mb-3\">
                        <h4 class=\"text-sm font-medium mb-2 text-gray-700 dark:text-gray-300\">Arquivos existentes:</h4>
                        <div class=\"space-y-2\">
                            <div v-for=\"(file, index) in currentFiles.{$field['name']}\" :key=\"index\" class=\"flex items-center justify-between p-2 border rounded bg-gray-50 dark:bg-gray-800\">
                                <div class=\"flex items-center space-x-2\">
                                    <svg class=\"w-4 h-4 text-blue-500\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"/>
                                    </svg>
                                    <span class=\"text-sm text-gray-600 dark:text-gray-300\">{{ getFileName(file) }}</span>
                                </div>
                                <div class=\"flex space-x-1\">
                                    <Button @click.prevent=\"downloadFile(file)\" type=\"button\" variant=\"outline\" size=\"sm\">
                                        <svg class=\"w-3 h-3\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"/>
                                        </svg>
                                    </Button>
                                    <Button @click=\"removeFileLocally('{$field['name']}', index)\" type=\"button\" variant=\"destructive\" size=\"sm\">
                                        <svg class=\"w-3 h-3\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\"/>
                                        </svg>
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload de novos arquivos -->
                    <Input id=\"{$field['name']}\" @change=\"(e: Event) => form.{$field['name']} = Array.from((e.target as HTMLInputElement).files || [])\" type=\"file\" multiple />
                    <p class=\"text-xs text-gray-500 mt-1\">{{ isEditing ? 'Selecione novos arquivos para adicionar' : 'Selecione um ou mais arquivos' }}</p>
                </div>";
    }

    /**
     * Converte plural para singular
     */
    private function getSingularName(string $fieldName): string
    {
        // Lógica simples: remove 's' do final se existir
        return rtrim($fieldName, 's');
    }

    /**
     * Gera o nome da tabela pivot seguindo o padrão Laravel: model1_model2 (em ordem alfabética)
     * Converte nomes compostos para snake_case (ex: OrdemServico -> ordem_servico)
     */
    private function generatePivotTableName(string $model1, string $model2): string
    {
        // Usa o mesmo padrão das migrações: modelo1 + modelo2 + s (tudo lowercase)
        // Ex: OrdemServico + Servico = ordemservicoservicos
        $table1 = strtolower($model1);
        $table2 = strtolower($model2);
        return $table1 . $table2 . 's';
    }
}
