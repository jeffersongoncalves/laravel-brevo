<?php

namespace JeffersonGoncalves\Brevo\Resources;

use InvalidArgumentException;
use JeffersonGoncalves\Brevo\BrevoClient;

class Campaigns
{
    public function __construct(
        protected BrevoClient $client,
        protected int $defaultLimit = 50,
    ) {}

    /** @param array<string, mixed> $params */
    public function list(array $params = []): array
    {
        return $this->client->get('/emailCampaigns', array_merge([
            'limit' => $this->defaultLimit,
            'offset' => 0,
        ], $params));
    }

    public function get(int $id): array
    {
        return $this->client->get("/emailCampaigns/{$id}");
    }

    /**
     * @param  array<string, string>  $sender  ['name' => ..., 'email' => ...]
     * @param  array<int, int>  $listIds
     */
    public function create(
        string $name,
        array $sender,
        string $subject,
        string $htmlContent,
        array $listIds,
    ): array {
        if ($name === '') {
            throw new InvalidArgumentException('The "name" attribute is required.');
        }

        return $this->client->post('/emailCampaigns', [
            'name' => $name,
            'sender' => $sender,
            'subject' => $subject,
            'htmlContent' => $htmlContent,
            'recipients' => ['listIds' => $listIds],
        ]);
    }

    /** @param array<string, mixed> $attributes */
    public function update(int $id, array $attributes): array
    {
        return $this->client->put("/emailCampaigns/{$id}", $attributes);
    }

    public function delete(int $id): array
    {
        return $this->client->delete("/emailCampaigns/{$id}");
    }

    public function sendNow(int $id): array
    {
        return $this->client->post("/emailCampaigns/{$id}/sendNow");
    }

    /** @param array<int, string> $emailTo */
    public function sendTest(int $id, array $emailTo): array
    {
        return $this->client->post("/emailCampaigns/{$id}/sendTest", ['emailTo' => $emailTo]);
    }
}
