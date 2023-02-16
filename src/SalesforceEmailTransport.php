<?php

declare(strict_types=1);

namespace BBSLab\SalesforceEmailTransport;

use BBSLab\SalesforceEmailTransport\Exceptions\TokenException;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class SalesforceEmailTransport extends AbstractTransport
{
    public static Closure|null $contactKeyCallback = null;

    public static function withContactKeyCallback(Closure $closure): void
    {
        static::$contactKeyCallback = $closure;
    }

    /**
     * @throws \BBSLab\SalesforceEmailTransport\Exceptions\TokenException
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $recipient = collect($email->getTo())->first();

        $payload = [
            'definitionKey' => config('salesforce-email-transport.api.definition_key'),
            'recipient' => [
                'contactKey' => $this->contactKey($email),
                'to' => $recipient?->getAddress(),
                'attributes' => [
                    'html' => $email->getHtmlBody(),
                    'SubjectLine' => $email->getSubject(),
                ],
            ]
        ];

        if (!$token = $this->token()) {
            throw TokenException::missingToken();
        }

        Http::asJson()
            ->throw()
            ->withToken($token)
            ->post(config('salesforce-email-transport.api.url') . '/' . Str::uuid(), $payload);
    }

    public function __toString(): string
    {
        return 'salesforce';
    }

    protected function token(): ?string
    {
        $callback = function() {
            return Http::asForm()
                ->throw()
                ->post(config('salesforce-email-transport.auth.url'), [
                    'client_id' => config('salesforce-email-transport.auth.client_id'),
                    'client_secret' => config('salesforce-email-transport.auth.client_secret'),
                    'grant_type' => config('salesforce-email-transport.auth.grant_type'),
                    'resource' => config('salesforce-email-transport.auth.resource'),
                ]);
        };

        if(!config('salesforce-email-transport.auth.cache.enabled')) {
            return $callback()->json('access_token');
        }

        $key = config('salesforce-email-transport.auth.cache.key');

        if(Cache::has($key)) {
            return Cache::get($key);
        }

        $response = $callback();

        $delay = ((int)$response->json('expires_in')) - 30;

        return Cache::remember(
            key: $key,
            ttl: $delay,
            callback: fn() => $response->json('access_token')
        );
    }

    protected function contactKey(Email $email): ?string
    {
        if(is_callable(static::$contactKeyCallback)) {
            return call_user_func(static::$contactKeyCallback, $email);
        }

        return collect($email->getTo())->first()?->getAddress();
    }
}
