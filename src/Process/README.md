# How to create a process

```php
<?php

use Dayuse\Istorija\Process\Process;
use Dayuse\Istorija\Process\State;
use Dayuse\Istorija\Process\StateRepository;
use Dayuse\Istorija\Process\AutoNameProcessTrait;
use Dayuse\Istorija\CommandBus\CommandBus;

class RegisterReservationWhenBookingAttemptConfirmedProcess extends Process
{
    use AutoNameProcessTrait;
    
    /**
         * @var CommandBus
         */
        private $commandBus;
    
        /**
         * @var StateRepository
         */
        private $repository;
    
        public function whenAttemptBookingRequested(Event\AttemptBookingRequested $event)
        {
            $processId = $this->getProcessIdFromAggregate($event->getAttemptId());
            $state     = new State($processId, [
                'offerId'        => (string)$event->getOfferId(),
                'attemptId'      => (string)$event->getAttemptId(),
                'checkInDate'    => (string)$event->getCheckInDate(),
                'guestFirstName' => $event->getGuest()->getFirstName(),
                'guestLastName'  => $event->getGuest()->getLastName(),
            ]);
    
            $this->repository->save($state);
        }
    
        public function whenBookingAttemptConfirmed(Event\BookingAttemptConfirmed $event)
        {
            $processId = $this->getProcessIdFromAggregate($event->getAttemptId());
            $state     = $this->repository->find($processId);
    
            $this->commandBus->handle(new Command\RegisterReservationFromBookingAttempt(
                ReservationId::generate(),
                OfferId::fromString($state->get('offerId')),
                BookingAttemptId::fromString($state->get('offerId')),
                new Guest($state->get('guestFirstName'), $state->get('guestLastName')),
                CheckInDate::fromString($state->get('checkInDate'))
            ));
    
            $state->done();
            $this->repository->save($state);
        }
    
        public function whenBookingAttemptCancelled(Event\BookingAttemptCancelled $event)
        {
            $processId = $this->getProcessIdFromAggregate($event->getAttemptId());
            $state     = $this->repository->find($processId);
    
            $state->done();
            $this->repository->save($state);
        }
}

```