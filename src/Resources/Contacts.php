<?php

namespace JeffersonGoncalves\Brevo\Resources;

use InvalidArgumentException;
use JeffersonGoncalves\Brevo\BrevoClient;

class Contacts
{
    public function __construct(
        protected BrevoClient $client,
        protected int $defaultLimit = 50,
    ) {}

    /** @param array<string, mixed> $params */
    public function list(array $params = []): array
    {
        return $this->client->get('/contacts', array_merge([
            'limit' => $this->defaultLimit,
            'offset' => 0,
        ], $params));
    }

    public function get(int|string $identifier): array
    {
        return $this->client->get('/contacts/'.$identifier);
    }

    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): array
    {
        if (empty($attributes['email'])) {
            throw new InvalidArgumentException('The "email" attribute is required.');
        }

        return $this->client->post('/contacts', $attributes);
    }

    /** @param array<string, mixed> $attributes */
    public function update(int|string $identifier, array $attributes): array
    {
        return $this->client->put('/contacts/'.$identifier, $attributes);
    }

    public function delete(int|string $identifier): array
    {
        return $this->client->delete('/contacts/'.$identifier);
    }

    /**
     * @param  array<int, array<string, mixed>>  $contacts
     * @param  array<int, int>  $listIds
     */
    public function import(array $contacts, array $listIds = []): array
    {
        return $this->client->post('/contacts/import', array_filter([
            'jsonBody' => $contacts,
            'listIds' => $listIds === [] ? null : $listIds,
        ], fn (mixed $value) => $value !== null));
    }
}
