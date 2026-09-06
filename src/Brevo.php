<?php

namespace JeffersonGoncalves\Brevo;

use InvalidArgumentException;
use JeffersonGoncalves\Brevo\Resources\Campaigns;
use JeffersonGoncalves\Brevo\Resources\Contacts;
use JeffersonGoncalves\Brevo\Resources\Lists;
use JeffersonGoncalves\Brevo\Resources\Senders;
use JeffersonGoncalves\Brevo\Resources\SmsCampaigns;

/**
 * Entry point exposing one resource per Brevo API v3 group, plus the two
 * single-call transactional endpoints (email/SMS) and the account endpoint.
 */
class Brevo
{
    protected BrevoClient $client;

    public function __construct(string $apiUrl, string $apiKey, protected int $defaultLimit = 50)
    {
        $this->client = new BrevoClient($apiUrl, $apiKey);
    }

    // ponytail: /account is a single parameterless GET — not worth a dedicated
    // resource class, unlike the multi-endpoint groups below.
    public function account(): array
    {
        return $this->client->get('/account');
    }

    public function contacts(): Contacts
    {
        return new Contacts($this->client, $this->defaultLimit);
    }

    public function lists(): Lists
    {
        return new Lists($this->client, $this->defaultLimit);
    }

    public function campaigns(): Campaigns
    {
        return new Campaigns($this->client, $this->defaultLimit);
    }

    public function smsCampaigns(): SmsCampaigns
    {
        return new SmsCampaigns($this->client, $this->defaultLimit);
    }

    public function senders(): Senders
    {
        return new Senders($this->client);
    }

    /**
     * @param  array<int, array<string, string>>  $to  Each item: ['email' => ..., 'name' => ...]
     * @param  array<int, string>  $tags
     */
    public function sendEmail(
        string $senderEmail,
        string $senderName,
        array $to,
        string $subject,
        ?string $htmlContent = null,
        ?string $textContent = null,
        ?string $replyTo = null,
        array $tags = [],
    ): array {
        if ($htmlContent === null && $textContent === null) {
            throw new InvalidArgumentException('Either "htmlContent" or "textContent" is required.');
        }

        return $this->client->post('/smtp/email', array_filter([
            'sender' => ['email' => $senderEmail, 'name' => $senderName],
            'to' => $to,
            'subject' => $subject,
            'htmlContent' => $htmlContent,
            'textContent' => $textContent,
            'replyTo' => $replyTo === null ? null : ['email' => $replyTo],
            'tags' => $tags === [] ? null : $tags,
        ], fn (mixed $value) => $value !== null));
    }

    public function sendSms(
        string $sender,
        string $recipient,
        string $content,
        string $type = 'transactional',
    ): array {
        return $this->client->post('/transactionalSMS/sms', [
            'sender' => $sender,
            'recipient' => $recipient,
            'content' => $content,
            'type' => $type,
        ]);
    }
}
