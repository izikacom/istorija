<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

require __DIR__ . '/../../../vendor/autoload.php';

use DayUse\Istorija\Messaging\Settings;
use DayUse\Istorija\Messaging\Configuration;
use DayUse\Istorija\Messaging\Bus;
use DayUse\Istorija\Utils\ExecutionContext;
use DayUse\Istorija\Messaging\Message;
use DayUse\Istorija\Identifiers\GenericUuidIdentifier;
use DayUse\Istorija\Messaging\Subscription;
use DayUse\Istorija\Messaging\MessageHandler;
use DayUse\Istorija\Messaging\MessageHandlerContext;
use DayUse\Istorija\Messaging\SendOptions;

$executionContext = new ExecutionContext();
$executionContext->set('InitiatedBy', trim(`whoami`));
$executionContext->set('CorrelationId', (string) GenericUuidIdentifier::generate());


$busConfiguration = new Configuration(new Settings());

$bus = new Bus($busConfiguration, $executionContext);

$bus->subscribe(new Subscription(OrderPlaced::class, new class implements MessageHandler {
    public function handle(Message $message, MessageHandlerContext $context): void
    {
        printf(
            "%s:\n => Order: #%s!\n => Headers: %s\n",
            self::class,
            $message->getOrderId(),
            json_encode($context->getMessageHeaders(), JSON_PRETTY_PRINT)
        );
    }
}));

$bus->subscribe(new Subscription(OrderPlaced::class, new class implements MessageHandler {
    private $count = 0;
    public function handle(Message $message, MessageHandlerContext $context): void
    {
        if (0 === $this->count) {

            ++$this->count;
            throw new \Exception('This handler fails at the first call');
        }
        printf(
            "%s:\n => Order: #%s!\n => Headers: %s\n",
            self::class,
            $message->getOrderId(),
            json_encode($context->getMessageHeaders(), JSON_PRETTY_PRINT)
        );
    }
}));

$orderPlaced = new class((string) GenericUuidIdentifier::generate()) implements Message {
    private $orderId;
    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }
};

$bus->send($orderPlaced, (new SendOptions())->sendLocal());
