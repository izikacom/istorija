<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Contrib\Example;

use DayUse\Istorija\EventSourcing\DomainEvent;

class CustomerRegistered implements DomainEvent
{
    private $customerId;
    private $customerName;
    private $customerEmail;

    public function __construct(string $customerId, string $customerName, string $customerEmail)
    {
        $this->customerId = $customerId;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }
}
