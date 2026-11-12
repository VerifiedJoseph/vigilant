<?php

declare(strict_types=1);

namespace Vigilant\Notify;

use Vigilant\Config;
use Vigilant\Feed;
use Vigilant\Request;
use Vigilant\Message;
use Vigilant\Logger;
use Vigilant\Exception\RequestException;

class Notify
{
    /** @var array<string, mixed> serviceConfig Service config */
    private array $serviceConfig;

    /** @var ServicePayload ServicePayload class instance */
    private ServicePayload $servicePayload;

    /** @var Config Config class instance */
    private Config $config;

    /** @var Request Request class instance */
    private Request $request;

    /** @var Logger Logger class instance */
    private Logger $logger;

    /**
     * Create and config Notification class
     *
     * @param Feed\Details $details Feed details
     * @param Config $config Config class instance
     * @param Logger $logger Logger class instance
     */
    public function __construct(Feed\Details $details, Config $config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        $this->request = new Request($config->getUserAgent());
        $this->serviceConfig = (new ServiceConfig($config))->get($details);
        $this->servicePayload = new ServicePayload($this->config);
    }

    /**
     * Send messages
     * @param array<int, Message> $messages
     */
    public function send(array $messages): void
    {
        foreach ($messages as $message) {
            try {
                $payload = $this->servicePayload->get($message, $this->serviceConfig);

                $this->request->postJson(
                    $this->serviceConfig['server'],
                    $payload,
                    $this->createHeaders()
                );

                $this->logger->info('Sent notification using ' . $this->config->getNotificationService());
            } catch (RequestException $err) {
                $this->logger->error($err->getMessage());
            }
        }
    }

    private function createHeaders(): array
    {
        if (array_key_exists('token', $this->serviceConfig) === true) {
            return [
                'Authorization' => "Bearer $this->serviceConfig['token']"
            ];
        }

        return [];
    }
}
