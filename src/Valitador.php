<?php

namespace Asaas;

class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $validatedData = [];
        
        foreach ($rules as $field => $rule) {
            $rulesParsed = self::parseRules($rule);
            $value = $data[$field] ?? null;
            
            if (in_array('required', $rulesParsed) && (is_null($value) || $value === '')) {
                throw new \InvalidArgumentException("O campo '{$field}' é obrigatório.");
            }
            
            if (!in_array('required', $rulesParsed) && (is_null($value) || $value === '')) {
                continue;
            }
            
            if (!is_null($value) && $value !== '') {
                $value = self::applyValidations($field, $value, $rulesParsed);
                $validatedData[$field] = $value;
            }
        }
        
        return $validatedData;
    }

    protected static function parseRules(string $rules): array
    {
        return array_map('trim', explode('|', $rules));
    }

    protected static function applyValidations(string $field, $value, array $rules)
    {
        foreach ($rules as $rule) {
            if (str_contains($rule, ':')) {
                [$ruleName, $parameter] = explode(':', $rule, 2);
            } else {
                $ruleName = $rule;
                $parameter = null;
            }

            switch ($ruleName) {
                case 'required':
                case 'nullable':
                    break;
                case 'string':
                    $value = (string) $value;
                    break;
                case 'integer':
                case 'int':
                    if (!is_numeric($value)) {
                        throw new \InvalidArgumentException("O campo '{$field}' deve ser um número inteiro.");
                    }
                    $value = (int) $value;
                    break;
                case 'float':
                case 'numeric':
                    if (!is_numeric($value)) {
                        throw new \InvalidArgumentException("O campo '{$field}' deve ser um número.");
                    }
                    $value = (float) $value;
                    break;
                case 'boolean':
                case 'bool':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        throw new \InvalidArgumentException("O campo '{$field}' deve ser um email válido.");
                    }
                    break;
                case 'max':
                    if (is_string($value) && strlen($value) > (int) $parameter) {
                        throw new \InvalidArgumentException("O campo '{$field}' não pode ter mais que {$parameter} caracteres.");
                    }
                    break;
                case 'min':
                    if (is_string($value) && strlen($value) < (int) $parameter) {
                        throw new \InvalidArgumentException("O campo '{$field}' deve ter pelo menos {$parameter} caracteres.");
                    }
                    break;
                case 'in':
                    $allowedValues = explode(',', $parameter);
                    if (!in_array($value, $allowedValues)) {
                        throw new \InvalidArgumentException("O campo '{$field}' deve ser um dos valores: " . implode(', ', $allowedValues));
                    }
                    break;
                case 'cpf_cnpj':
                    $cleaned = preg_replace('/[^0-9]/', '', $value);
                    if (strlen($cleaned) !== 11 && strlen($cleaned) !== 14) {
                        throw new \InvalidArgumentException("O campo '{$field}' deve ser um CPF ou CNPJ válido.");
                    }
                    $value = $cleaned;
                    break;
                case 'phone':
                    $cleaned = preg_replace('/[^0-9]/', '', $value);
                    if (strlen($cleaned) < 10 || strlen($cleaned) > 11) {
                        throw new \InvalidArgumentException("O campo '{$field}' deve ser um telefone válido.");
                    }
                    $value = $cleaned;
                    break;
                case 'date':
                    if (!strtotime($value)) {
                        throw new \InvalidArgumentException("O campo '{$field}' deve ser uma data válida.");
                    }
                    break;
                case 'url':
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        throw new \InvalidArgumentException("O campo '{$field}' deve ser uma URL válida.");
                    }
                    break;
            }
        }

        return $value;
    }

    public static function applyDefaults(array $data, array $defaults): array
    {
        return array_merge($defaults, $data);
    }
}