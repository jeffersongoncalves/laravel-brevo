<?php

namespace JeffersonGoncalves\Brevo\Resources;

use JeffersonGoncalves\Brevo\BrevoClient;

class SmsCampaigns
{
    public function __construct(
        protected BrevoClient $client,
        protected int $defaultLimit = 50,
    ) {}

    /** @param array<string, mixed> $params */
    public function list(array $params = []): array
    {
        return $this->client->get('/smsCampaigns', array_merge([
            'limit' => $this->defaultLimit,
            'offset' => 0,
        ], $params));
    }
}
