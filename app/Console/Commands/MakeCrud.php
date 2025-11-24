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
        $casts = implode(', ', array_filter(array_map(fn($f) => match ($f['type']) {
            'files' => "'{$f['name']}' => 'array'",
            'float', 'double', 'decimal', 'moeda' => "'{$f['name']}' => 'float'",
            'date', 'datetime', 'timestamp' => "'{$f['name']}' => 'date'",
            default => null
        }, array_filter($fields, fn($f) => !$f['is_pivot']))));

        $relationships = implode("\n    ", array_filter(array_map(function ($f) use ($model) {
            if ($f['is_foreign']) {
                $relatedModel = $f['related_model'];
                $relationshipName = strtolower($relatedModel);
                return "// TODO: Implement relationship for {$f['name']}\n    public function {$relationshipName}()\n    {\n        return \$this->belongsTo(\\App\\Models\\{$relatedModel}::class, '{$f['name']}');\n    }";
            } elseif ($f['is_pivot']) {
                $relatedModel = $f['related_model'];
                $relationshipName = strtolower($f['name']);
                $pivotTableName = $this->generatePivotTableName($model, $relatedModel);
                return "// TODO: Implement pivot relationship for {$f['name']}\n    public function {$relationshipName}()\n    {\n        return \$this->belongsToMany(\\App\\Models\\{$relatedModel}::class, '{$pivotTableName}')\n            ->withTimestamps();\n        // TODO: Adicionar withPivot() se houver campos extras na tabela pivot\n    }";
            }
            return '';
        }, $fields)));

        $tableName = $this->generateTableName($model);

        $stub = File::get(base_path('stubs/crud.model.stub'));
        $stub = str_replace(['{{model}}', '{{tableName}}', '{{fillable}}', '{{casts}}', '{{relationships}}'], [$model, $tableName, $fillable, $casts, $relationships], $stub);
        File::put(app_path("Models/{$model}.php"), $stub);
    }

    protected function createController($controller, $model, $viewFolder, $routePrefix, $modelLower, $modelTitle, $modelPluralTitle, $fields)
    {
        $validationRules = implode("\n            ", array_map(fn($f) => "'{$f['name']}' => 'required|" . match ($f['type']) {
            'integer', 'bigInteger' => 'integer',
            'float', 'double', 'decimal', 'moeda' => 'numeric',
            'email' => 'email',
            'date', 'datetime', 'timestamp' => 'date',
            'boolean' => 'boolean',
            'json' => 'json',
            'file' => 'file',
            'files' => 'array',
            'pivot' => 'array', // Pivot fields são arrays de IDs
            default => 'string'
        } . "|max:255',", array_filter($fields, fn($f) => !$f['is_pivot'] || $f['type'] === 'pivot')));

        $dropdownData = implode("\n        ", array_filter(array_map(
            function ($f) {
                if ($f['is_foreign']) {
                    return "\${$f['name']}Options = \\App\\Models\\{$f['related_model']}::where('deleted', 0)->orderBy('id', 'desc')->get()->map(function (\$item) {\n                return [\n                    'value' => \$item->id,\n                    'label' => \$item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado\n                ];\n            });";
                } elseif ($f['is_pivot']) {
                    return "\${$f['name']}Options = \\App\\Models\\{$f['related_model']}::where('deleted', 0)->orderBy('nome', 'asc')->get()->map(function (\$item) {\n                return [\n                    'value' => \$item->id,\n                    'label' => \$item->nome // TODO: Ajustar o campo 'nome' conforme o modelo relacionado\n                ];\n            });";
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

{$this->generateFileUploadCode($fields, '        ')}
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
        \$data = \$request->validate([
            {$validationRules}
        ]);

{$this->generateFileUploadCode($fields, '        ')}
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
            default => 'string',
        }, $fields));

        $formFields = implode(",\n    ", array_map(fn($f) => "{$f['name']}: props.item?.{$f['name']}" . match (true) {
            $f['is_foreign'] => "?.toString() || ''", // Campos de chave estrangeira devem ser strings para o Select
            $f['type'] === 'boolean' => ' || false',
            $f['type'] === 'integer' || $f['type'] === 'bigInteger' || $f['type'] === 'float' || $f['type'] === 'double' || $f['type'] === 'decimal' => ' || 0',
            $f['type'] === 'pivot' => ' || []',
            default => "?.toString() || ''",
        }, $fields));

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
                $f['type'] === 'file' => "<div>\n                    <Label for=\"{$f['name']}\">{$f['label']}</Label>\n                    <Input id=\"{$f['name']}\" v-model=\"form.{$f['name']}\" type=\"file\" />\n                </div>",
                $f['type'] === 'files' => "<div>\n                    <Label for=\"{$f['name']}\">{$f['label']}</Label>\n                    <Input id=\"{$f['name']}\" v-model=\"form.{$f['name']}\" type=\"file\" multiple />\n                </div>",
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

                return "\$table->{$columnType}('{$f['name']}');";

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
            }

            if ($field['is_pivot']) {
                $this->info("  • Configurar relacionamento pivot para '{$field['name']}' no modelo.");
                $this->info("  • Ajustar sync do relacionamento '{$field['name']}' no controlador.");
                $this->info("  • Verificar se a tabela pivot existe ou criar migration para ela.");
                $this->warn("⚠️  Certifique-se de que o modelo relacionado '{$field['related_model']}' existe.");
                $this->warn("⚠️  Ajustar campo de exibição no controller (atualmente usando 'nome').");
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
    private function generateFileUploadCode(array $fields, string $indent = ''): string
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
                $code[] = $indent . "    \$data['{$field['name']}'] = \$request->file('{$field['name']}')->store('uploads', 'public');";
                $code[] = $indent . "}";
            } elseif ($field['type'] === 'files') {
                $code[] = $indent . "if (\$request->hasFile('{$field['name']}')) {";
                $code[] = $indent . "    \$data['{$field['name']}'] = array_map(";
                $code[] = $indent . "        fn(\$file) => \$file->store('uploads', 'public'),";
                $code[] = $indent . "        \$request->file('{$field['name']}')";
                $code[] = $indent . "    );";
                $code[] = $indent . "}";
            }
        }

        return implode("\n", $code) . "\n";
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
