<?php

namespace Asaas;

class Customers
{
    protected AsaasClient $client;
    protected string $endpoint = 'customers';

    protected array $fields = [
        'id' => 'string|max:255',
        'name' => 'required|string|max:100',
        'cpfCnpj' => 'required|cpf_cnpj',
        'email' => 'required|email|max:255',
        'mobilePhone' => 'required|phone',
        'phone' => 'nullable|phone',
        'address' => 'nullable|string|max:255',
        'addressNumber' => 'nullable|string|max:20',
        'complement' => 'nullable|string|max:100',
        'province' => 'nullable|string|max:100',
        'postalCode' => 'nullable|string|max:20',
        'externalReference' => 'nullable|string|max:255',
        'notificationDisabled' => 'boolean',
        'additionalEmails' => 'nullable|string|max:500',
        'municipalInscription' => 'nullable|string|max:50',
        'stateInscription' => 'nullable|string|max:50',
        'observations' => 'nullable|string|max:1000',
        'groupName' => 'nullable|string|max:100',
        'company' => 'nullable|string|max:100',
        'foreignCustomer' => 'boolean'
    ];

    protected array $defaultValues = [
        'notificationDisabled' => false,
        'foreignCustomer' => false,
    ];

    public function __construct(AsaasClient $client)
    {
        $this->client = $client;
    }

    public function all(array $params = []): array
    {
        return $this->client->get($this->endpoint, $params);
    }

    public function find(string $id): array
    {
        return $this->client->get($this->endpoint . '/' . $id);
    }

    public function create(array $data): array
    {
        $data = Validator::applyDefaults($data, $this->defaultValues);
        $validatedData = Validator::validate($data, $this->fields);
        return $this->client->post($this->endpoint, $validatedData);
    }

    public function update(string $id, array $data): array
    {
        $updateRules = array_filter($this->fields, function($rule) {
            return !str_contains($rule, 'required');
        });
        
        $validatedData = Validator::validate($data, $updateRules);
        return $this->client->put($this->endpoint . '/' . $id, $validatedData);
    }

    public function delete(string $id): array
    {
        return $this->client->delete($this->endpoint . '/' . $id);
    }
}