<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

require __DIR__ . '/../../../vendor/autoload.php';

use Dayuse\Istorija\Messaging\Settings;
use Dayuse\Istorija\Messaging\Configuration;
use Dayuse\Istorija\Messaging\Bus;
use Dayuse\Istorija\Utils\ExecutionContext;
use Dayuse\Istorija\Messaging\Message;
use Dayuse\Istorija\Identifiers\GenericUuidIdentifier;
use Dayuse\Istorija\Messaging\Subscription;
use Dayuse\Istorija\Messaging\MessageHandler;
use Dayuse\Istorija\Messaging\MessageHandlerContext;
use Dayuse\Istorija\Messaging\SendOptions;

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
