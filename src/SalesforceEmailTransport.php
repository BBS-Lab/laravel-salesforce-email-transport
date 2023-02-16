<?php

declare(strict_types=1);

namespace BBSLab\SalesforceEmailTransport;

use BBSLab\SalesforceEmailTransport\Exceptions\TokenException;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class SalesforceEmailTransport extends AbstractTransport
{
    /** @var array<string,Closure> $contactKeyCallback */
    public static array $contactKeyCallback = [];

    public static function withContactKeyCallback(string $name, Closure $closure): void
    {
        static::$contactKeyCallback[$name] = $closure;
    }

    public function __construct(public string $name, public array $config = [], EventDispatcherInterface $dispatcher = null, LoggerInterface $logger = null)
    {
        parent::__construct($dispatcher, $logger);
    }

    /**
     * @throws \BBSLab\SalesforceEmailTransport\Exceptions\TokenException
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $recipient = collect($email->getTo())->first();

        $payload = [
            'definitionKey' => $this->config('api.definition_key'),
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
            ->post($this->config('api.url') . '/' . Str::uuid(), $payload);
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
                ->post($this->config('auth.url'), [
                    'client_id' => $this->config('auth.client_id'),
                    'client_secret' => $this->config('auth.client_secret'),
                    'grant_type' => $this->config('auth.grant_type'),
                    'resource' => $this->config('auth.resource'),
                ]);
        };

        if(!$this->config('auth.cache.enabled')) {
            return $callback()->json('access_token');
        }

        $key = $this->config('auth.cache.key');

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
        if(is_callable(static::$contactKeyCallback[$this->name] ?? null)) {
            return call_user_func(static::$contactKeyCallback[$this->name], $email);
        }

        return collect($email->getTo())->first()?->getAddress();
    }

    protected function config(string $key)
    {
        return data_get($this->config, $key, config("salesforce-email-transport.{$key}"));
    }
}
