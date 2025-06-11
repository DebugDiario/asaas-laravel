<?php

namespace Asaas;

class PaymentLinks
{
    protected AsaasClient $client;
    protected string $endpoint = 'paymentLinks';

    protected array $fields = [
        'id' => 'string|max:255',
        'name' => 'required|string|max:100',
        'description' => 'nullable|string|max:500',
        'endDate' => 'nullable|date',
        'value' => 'nullable|numeric|min:0.01',
        'billingType' => 'required|string|in:UNDEFINED,BOLETO,CREDIT_CARD,PIX',
        'chargeType' => 'required|string|in:DETACHED,RECURRENT,INSTALLMENT',
        'dueDateLimitDays' => 'nullable|integer|min:1|max:365',
        'subscriptionCycle' => 'nullable|string|in:MONTHLY,WEEKLY,BIWEEKLY,BIMONTHLY,QUARTERLY,SEMIANNUALLY,YEARLY',
        'maxInstallmentCount' => 'nullable|integer|min:1|max:36',
        'externalReference' => 'nullable|string|max:255',
        'notificationEnabled' => 'boolean',
        'isAddressRequired' => 'boolean'
    ];

    protected array $defaultValues = [
        'notificationEnabled' => true,
        'isAddressRequired' => false,
        'maxInstallmentCount' => 1,
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
        $this->validateBusinessRules($validatedData);
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

    protected function validateBusinessRules(array $data): void
    {
        if (isset($data['billingType']) && $data['billingType'] === 'BOLETO' && !isset($data['dueDateLimitDays'])) {
            throw new \InvalidArgumentException("Para pagamento via BOLETO é necessário informar dueDateLimitDays.");
        }

        if (isset($data['chargeType']) && $data['chargeType'] === 'INSTALLMENT' && !isset($data['maxInstallmentCount'])) {
            throw new \InvalidArgumentException("Para cobrança INSTALLMENT é necessário informar maxInstallmentCount.");
        }

        if (isset($data['chargeType']) && $data['chargeType'] === 'RECURRENT' && !isset($data['subscriptionCycle'])) {
            throw new \InvalidArgumentException("Para cobrança RECURRENT é necessário informar subscriptionCycle.");
        }
    }
}