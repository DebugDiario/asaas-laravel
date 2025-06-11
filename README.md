# Asaas Laravel SDK

SDK Laravel para integração com a API do Asaas.

## Instalação

```bash
composer require asaas/laravel-sdk
```

## Configuração

1. Publique o arquivo de configuração:
```bash
php artisan vendor:publish --tag=asaas-config
```

2. Configure suas credenciais no `.env`:
```env
ASAAS_API_KEY=your_api_key_here
ASAAS_SANDBOX=true
ASAAS_VERSION=v3
ASAAS_TIMEOUT=30
```

## Uso

### Facade
```php
use Asaas\Facades\Asaas;

// Clientes
$customers = Asaas::customers()->all();
$customer = Asaas::customers()->create([
    'name' => 'João Silva',
    'cpfCnpj' => '12345678901',
    'email' => 'joao@exemplo.com',
    'mobilePhone' => '11999999999'
]);

// Links de Pagamento
$links = Asaas::paymentLinks()->all();
$link = Asaas::paymentLinks()->create([
    'name' => 'Produto X',
    'value' => 100.00,
    'billingType' => 'PIX',
    'chargeType' => 'DETACHED'
]);
```

### Injeção de Dependência
```php
use Asaas\Customers;
use Asaas\PaymentLinks;

class PaymentController extends Controller
{
    public function __construct(
        protected Customers $customers,
        protected PaymentLinks $paymentLinks
    ) {}

    public function index()
    {
        $customers = $this->customers->all();
        return view('customers.index', compact('customers'));
    }
}
```

## Métodos Disponíveis

### Customers
- `all(array $params = [])` - Lista todos os clientes
- `find(string $id)` - Busca cliente por ID
- `create(array $data)` - Cria novo cliente
- `update(string $id, array $data)` - Atualiza cliente
- `delete(string $id)` - Remove cliente

### PaymentLinks
- `all(array $params = [])` - Lista todos os links
- `find(string $id)` - Busca link por ID  
- `create(array $data)` - Cria novo link
- `update(string $id, array $data)` - Atualiza link
- `delete(string $id)` - Remove link

## Validação

O SDK inclui validação automática baseada em regras:

```php
// Campos obrigatórios são validados automaticamente
$customer = Asaas::customers()->create([
    'name' => 'required|string|max:100',
    'cpfCnpj' => 'required|cpf_cnpj',
    'email' => 'required|email|max:255',
    'mobilePhone' => 'required|phone',
    'phone' => 'nullable|phone'
]);
```

## Licença

MIT
