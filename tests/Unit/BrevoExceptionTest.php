<?php

use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\Response as HttpResponse;
use JeffersonGoncalves\Brevo\Exceptions\BrevoException;

function fakeBrevoResponse(int $status, array $body): Response
{
    $psr = new GuzzleHttp\Psr7\Response($status, [], json_encode($body));

    return new HttpResponse($psr);
}

it('builds the exception message from the response "message" field', function () {
    $response = fakeBrevoResponse(404, ['code' => 'document_not_found', 'message' => 'Contact not found']);

    $exception = BrevoException::fromResponse($response);

    expect($exception->getMessage())->toBe('Contact not found')
        ->and($exception->getCode())->toBe(404)
        ->and($exception->errorBody())->toBe(['code' => 'document_not_found', 'message' => 'Contact not found']);
});

it('falls back to a generic message when the body has no "message" key', function () {
    $response = fakeBrevoResponse(500, []);

    $exception = BrevoException::fromResponse($response);

    expect($exception->getMessage())->toBe('Brevo API error (HTTP 500).');
});
