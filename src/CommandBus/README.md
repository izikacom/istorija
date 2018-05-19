# Handle business command

```php
<?php

use Dayuse\Istorija\CommandBus\NullCommandBus;

$commandBus = new NullCommandBus();

$commandBus->handle(new CreateMember(
    MemberId::fromString('member-123'),
    Email::fromString('john.doe@acme.com')
));
``` 

Because of `CommandBus::handle(Command $command): void`

Take care that:
1. Nothing is returned. Command accepted.
2. Exception could be thrown. Command refused.



## CommandBusValidator

This wrapper could be used to validate command.

```php
<?php

use Dayuse\Istorija\CommandBus\Command;
use Dayuse\Istorija\CommandBus\CommandValidator;
use Dayuse\Istorija\CommandBus\CommandBusValidator;
use Dayuse\Istorija\CommandBus\NullCommandBus;
use Dayuse\Istorija\CommandBus\Validator\Exception\CommandNotValidException;

$commandBus = new CommandBusValidator(
    new NullCommandBus(),
    [
        new class() implements CommandValidator {
            private $usageCount = 0;
            
            public function validate(Command $command): void {
                $this->usageCount++;
                
                if(5 === $this->usageCount) {
                    throw new CommandNotValidException(
                        $command,
                        'Could not handle more than 5 commands during runtime'
                    );
                }
            }
            
            public function support(Command $command) : bool {
                return true;
            }
        }
    ]
);

$commandBus->handle(new CreateMember(
    MemberId::fromString('member-123'),
    Email::fromString('john.doe@acme.com')
));

```

## LoggerCommandBus

This wrapper could be used to log command bus activity using PSR-3.

Logger activities:
* **[info]** Command handler have been registered
* **[debug]** Command will be handled 
* **[info]** Command have been handled
* **[error]** Command could not be handled due to exception

