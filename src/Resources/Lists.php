<?php

namespace JeffersonGoncalves\Brevo\Resources;

use InvalidArgumentException;
use JeffersonGoncalves\Brevo\BrevoClient;

class Lists
{
    public function __construct(
        protected BrevoClient $client,
        protected int $defaultLimit = 50,
    ) {}

    /** @param array<string, mixed> $params */
    public function list(array $params = []): array
    {
        return $this->client->get('/contacts/lists', array_merge([
            'limit' => $this->defaultLimit,
            'offset' => 0,
        ], $params));
    }

    public function get(int $id): array
    {
        return $this->client->get("/contacts/lists/{$id}");
    }

    public function create(string $name, int $folderId): array
    {
        if ($name === '') {
            throw new InvalidArgumentException('The "name" attribute is required.');
        }

        return $this->client->post('/contacts/lists', ['name' => $name, 'folderId' => $folderId]);
    }

    public function update(int $id, string $name): array
    {
        return $this->client->put("/contacts/lists/{$id}", ['name' => $name]);
    }

    public function delete(int $id): array
    {
        return $this->client->delete("/contacts/lists/{$id}");
    }

    /** @param array<string, mixed> $params */
    public function contacts(int $id, array $params = []): array
    {
        return $this->client->get("/contacts/lists/{$id}/contacts", array_merge([
            'limit' => $this->defaultLimit,
            'offset' => 0,
        ], $params));
    }

    /** @param array<int, string> $emails */
    public function addContacts(int $id, array $emails): array
    {
        return $this->client->post("/contacts/lists/{$id}/contacts/add", ['emails' => $emails]);
    }

    /** @param array<int, string> $emails */
    public function removeContacts(int $id, array $emails): array
    {
        return $this->client->post("/contacts/lists/{$id}/contacts/remove", ['emails' => $emails]);
    }
}
