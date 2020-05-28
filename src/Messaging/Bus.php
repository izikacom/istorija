<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Messaging;

use Dayuse\Istorija\Identifiers\GenericUuidIdentifier;
use Dayuse\Istorija\Messaging\Transport\Headers;
use Dayuse\Istorija\Utils\ExecutionContext as GlobalExecutionContext;
use Dayuse\Istorija\Utils\NotImplemented;
use Verraes\ClassFunctions\ClassFunctions;

class Bus
{
    private $configuration;
    private $globalExecutionContext;
    private $executionPipelineFactory;

    /** @var Subscription[][] */
    private $subscriptions = [];

    public function __construct(Configuration $configuration, GlobalExecutionContext $executionContext, ExecutionPipelineFactory $executionPipelineFactory)
    {
        $this->configuration = $configuration;
        $this->globalExecutionContext = $executionContext;
        $this->executionPipelineFactory = $executionPipelineFactory;
    }

    public function send(Message $message, ?SendOptions $options = null): void
    {
        $messageClassName = ClassFunctions::fqcn($message);
        $options = $options ?? new SendOptions();
        $headers = new Headers();
        $headers['MessageId'] = $options->getMessageId() ?? (string) GenericUuidIdentifier::generate();

        if ($options->useEndpointLoopback()) {
            $headers['Destination'] = SendOptions::ENDPOINT_LOOPBACK;

            foreach ($this->globalExecutionContext->all() as $context => $value) {
                $headers[$context] = (string) $value;
            }

            $executionPipeline = $this->executionPipelineFactory->createExecutionPipeline();

            // TODO Verifier messageContract === $message::class
            foreach ($this->subscriptions as $messageContract => $subscriptions) {
                foreach ($subscriptions as $subscription) {
                    if ($messageClassName === ClassFunctions::fqcn($subscription->getMessageContract())) {
                        $executionPipeline->addHandler($subscription->getHandler());
                    }
                }
            }

            $executionPipeline->execute($message, new MessageHandlerContext($this, $headers, $this->globalExecutionContext));

            return;
        }

        throw NotImplemented::feature('Asynchronous send() is not available yet. Use SendOptions::sendLocal() to send synchronous Message');
    }

    public function sendLocal(Message $message, ?SendOptions $options = null): void
    {
        $options = $options ?? new SendOptions();
        $options->sendLocal();

        $this->send($message, $options);
    }

    public function publish(Message $message, ?SendOptions $options = null): void
    {
        throw NotImplemented::method('Use send() instead');
    }

    public function subscribe(Subscription $subscription): void
    {
        $this->subscriptions[$subscription->getMessageContract()][] = $subscription;
    }

    public function start(): void
    {
        throw NotImplemented::feature('Start is used when starting the bus in Consuming mode');
    }
}
