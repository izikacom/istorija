# How to create a process

```php
<?php

use Dayuse\Istorija\Process\StatefulProcess;
use Dayuse\Istorija\Process\State;

class RegisterReservationWhenBookingAttemptConfirmedProcess extends StatefulProcess
{
    public function whenAttemptBookingRequested(Event\AttemptBookingRequested $event)
    {
        $this->initState(
            $event->getAttemptId(),
            new State([
                'offerId'        => (string)$event->getOfferId(),
                'attemptId'      => (string)$event->getAttemptId(),
                'checkInDate'    => (string)$event->getCheckInDate(),
                'guestFirstName' => $event->getGuest()->getFirstName(),
                'guestLastName'  => $event->getGuest()->getLastName(),
            ])
        );
    }

    public function whenBookingAttemptConfirmed(Event\BookingAttemptConfirmed $event)
    {
        $state = $this->getState($event->getAttemptId());

        $this->handleCommand(new Command\RegisterReservationFromBookingAttempt(
            ReservationId::generate(),
            OfferId::fromString($state->get('offerId')),
            BookingAttemptId::fromString($state->get('offerId')),
            new Guest($state->get('guestFirstName'), $state->get('guestLastName')),
            CheckInDate::fromString($state->get('checkInDate'))
        ));

        $this->closeState($event->getAttemptId());
    }

    public function whenBookingAttemptCancelled(Event\BookingAttemptCancelled $event)
    {
        $this->closeState($event->getAttemptId());
    }
}

```