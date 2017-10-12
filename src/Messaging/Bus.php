<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\Messaging;

use DayUse\Istorija\Identifiers\GenericUuidIdentifier;
use DayUse\Istorija\Messaging\Transport\Headers;
use DayUse\Istorija\Utils\ExecutionContext as GlobalExecutionContext;
use DayUse\Istorija\Utils\NotImplemented;
use Verraes\ClassFunctions\ClassFunctions;

class Bus
{
    private $configuration;
    private $globalExecutionContext;
    /** @var Subscription[][] */
    private $subscriptions = [];

    public function __construct(Configuration $configuration, GlobalExecutionContext $executionContext)
    {
        $this->configuration = $configuration;
        $this->globalExecutionContext = $executionContext;
    }

    public function send(Message $message, ?SendOptions $options = null): void
    {
        $options = $options ?? new SendOptions();
        $headers = new Headers();
        $headers['MessageId'] = $options->getMessageId() ?? (string) GenericUuidIdentifier::generate();

        if ($options->useEndpointLoopback()) {

            $headers['Destination'] = SendOptions::ENDPOINT_LOOPBACK;

            foreach ($this->globalExecutionContext->all() as $context => $value) {
                $headers[$context] = $value;
            }

            $executionPipeline = new ExecutionPipeline();

            // TODO Verifier messageContract === $message::class
            foreach ($this->subscriptions as $messageContract => $subscriptions) {
                foreach ($subscriptions as $subscription) {
                    if (ClassFunctions::fqcn($message) === ClassFunctions::fqcn($subscription->getMessageContract())){
                        $executionPipeline->addHandler($subscription->getHandler());
                    }
                }
            }

            $executionPipeline->execute($message, new MessageHandlerContext($this, $headers, $this->globalExecutionContext));

            return;
        }

        throw NotImplemented::feature('Asynchronous send() is not available yet. Use SendOptions::sendLocal() to send synchronous Message');
    }

    public function publish(Message $message, ?SendOptions $options = null): void
    {
        throw NotImplemented::method('Use send() instead');
    }

    public function subscribe(Subscription $subscription)
    {
        $this->subscriptions[$subscription->getMessageContract()][] = $subscription;
    }

    public function start()
    {
        throw NotImplemented::feature('Start is used when starting the bus in Consuming mode');
    }
}
