<?php

namespace JeffersonGoncalves\Brevo\Resources;

use InvalidArgumentException;
use JeffersonGoncalves\Brevo\BrevoClient;

class Senders
{
    public function __construct(
        protected BrevoClient $client,
    ) {}

    public function list(): array
    {
        return $this->client->get('/senders');
    }

    public function create(string $name, string $email): array
    {
        if ($name === '' || $email === '') {
            throw new InvalidArgumentException('Both "name" and "email" are required.');
        }

        return $this->client->post('/senders', ['name' => $name, 'email' => $email]);
    }
}
