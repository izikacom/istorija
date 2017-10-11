<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\Messaging;

class Configuration
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function getTransport(): Transport
    {
        return $this->settings->get('transport');
    }
}
