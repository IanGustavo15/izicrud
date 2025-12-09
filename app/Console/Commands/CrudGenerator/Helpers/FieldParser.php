<?php

namespace App\Console\Commands\CrudGenerator\Helpers;

use Illuminate\Support\Str;

class FieldParser
{
    public function parse(array $fieldsArg): array
    {
        $fields = [];

        foreach ($fieldsArg as $fieldArg) {
            $parts = explode(':', $fieldArg);

            if (count($parts) !== 3) {
                throw new \InvalidArgumentException("Invalid field format: {$fieldArg}. Expected format: name:\"Label\":type");
            }

            $name = $parts[0];
            $label = trim($parts[1], '"\'');
            $type = $parts[2];

            // Detect relationships
            $isRelationship = $this->detectRelationship($name, $type);

            $fields[] = [
                'name' => $name,
                'label' => $label,
                'type' => $type,
                'is_foreign' => $isRelationship['is_foreign'],
                'is_pivot' => $isRelationship['is_pivot'],
                'related_model' => $isRelationship['related_model'],
            ];
        }

        return $fields;
    }

    protected function detectRelationship(string $fieldName, string $type): array
    {
        $result = [
            'is_foreign' => false,
            'is_pivot' => false,
            'related_model' => null,
        ];

        // Detect belongsTo relationship (id_campo)
        if (str_starts_with($fieldName, 'id_')) {
            $result['is_foreign'] = true;
            $result['related_model'] = ucfirst(str_replace('id_', '', $fieldName));
        }

        // Detect belongsToMany relationship (pModelo)
        if (str_starts_with($type, 'p') && ctype_upper($type[1] ?? '')) {
            $result['is_pivot'] = true;
            $result['related_model'] = substr($type, 1);
        }

        return $result;
    }
}
