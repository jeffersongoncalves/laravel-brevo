<?php

namespace JeffersonGoncalves\Brevo;

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Brevo\Exceptions\BrevoException;

/**
 * Thin wrapper around Laravel's Http client for the Brevo REST API v3.
 *
 * ponytail: Http::retry() already covers transient-failure retries if ever
 * needed later — no custom retry/backoff layer built here speculatively.
 */
class BrevoClient
{
    public function __construct(
        protected string $apiUrl,
        protected string $apiKey,
    ) {}

    /** @param array<string, mixed> $query */
    public function get(string $path, array $query = []): array
    {
        return $this->request('get', $path, $query);
    }

    /** @param array<string, mixed>|null $body */
    public function post(string $path, ?array $body = null): array
    {
        return $this->request('post', $path, $body);
    }

    /** @param array<string, mixed>|null $body */
    public function put(string $path, ?array $body = null): array
    {
        return $this->request('put', $path, $body);
    }

    public function delete(string $path): array
    {
        return $this->request('delete', $path);
    }

    /** @param array<string, mixed>|null $data */
    protected function request(string $method, string $path, ?array $data = null): array
    {
        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
            ->baseUrl(rtrim($this->apiUrl, '/'))
            ->{$method}($path, $data ?? []);

        if ($response->failed()) {
            throw BrevoException::fromResponse($response);
        }

        return (array) ($response->json() ?? []);
    }
}
