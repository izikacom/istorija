<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/CustomerRegistered.php';

use Contrib\Example\CustomerRegistered;
use DayUse\Istorija\Identifiers\GenericUuidIdentifier;
use DayUse\Istorija\SimpleMessaging\Message;

$customerRegistered = new CustomerRegistered(
    GenericUuidIdentifier::generate(),
    'Boris Guéry',
    'guery.b@gmail.com'
);

$bus = new \DayUse\Istorija\SimpleMessaging\Bus();
$bus->subscribe(CustomerRegistered::class, function(Message $message){
    var_dump($message);
});

$bus->publish($customerRegistered);

$i2 = new \DayUse\Istorija\Msg\Interf2();
$i3 = new \DayUse\Istorija\Msg\Interf2();
$i4 = new \DayUse\Istorija\Msg\Interf3();

$i2->foo($i2);
$i2->foo($i4);
