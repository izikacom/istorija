<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Messaging;

class Settings
{
    private $settings = [];

    public function set(string $key, $value): void
    {
        $this->settings[$key] = $value;
    }

    public function get(string $key)
    {
        if (!isset($this->settings[$key])) {

            throw new \DomainException(sprintf('"%s" doesn\'t exist', $key));
        }

        return $this->settings[$key];
    }
}
