<?php

namespace Asaas;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AsaasClient
{
    protected Client $client;
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge(config('asaas', []), $config);
        $this->client = new Client([
            'base_uri' => $this->getBaseUri(),
            'verify' => $this->config['certificate'] ?? true,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'access_token' => $this->config['api_key'],
            ],
            'timeout' => $this->config['timeout'] ?? 30,
        ]);
    }

    protected function getBaseUri(): string
    {
        $url = $this->config['sandbox'] 
            ? $this->config['urls']['sandbox'] 
            : $this->config['urls']['production'];

        return rtrim($url, '/') . '/' . ltrim($this->config['version'], '/') . '/';
    }

    public function request(string $method, string $endpoint, array $params = []): array
    {
        $method = strtoupper($method);
        $options = [];

        if ($method === 'GET') {
            $options['query'] = $params;
        } else {
            $options['json'] = $params;
        }

        try {
            $response = $this->client->request($method, ltrim($endpoint, '/'), $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            throw new \Exception(
                'Erro na requisição: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function get(string $endpoint, array $params = []): array
    {
        return $this->request('GET', $endpoint, $params);
    }

    public function post(string $endpoint, array $params = []): array
    {
        return $this->request('POST', $endpoint, $params);
    }

    public function put(string $endpoint, array $params = []): array
    {
        return $this->request('PUT', $endpoint, $params);
    }

    public function delete(string $endpoint, array $params = []): array
    {
        return $this->request('DELETE', $endpoint, $params);
    }
}
