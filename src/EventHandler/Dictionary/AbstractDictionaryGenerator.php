<?php

namespace Dayuse\Istorija\EventHandler\Dictionary;

use Dayuse\Istorija\EventHandler\State;
use Dayuse\Istorija\EventSourcing\AbstractEventHandler;


/**
 * @author : Thomas Tourlourat <thomas@tourlourat.com>
 */
abstract class AbstractDictionaryGenerator extends AbstractEventHandler
{
    /** @var Dictionary */
    private $dictionary;

    public function __construct(Dictionary $dictionary)
    {
        $this->dictionary = $dictionary;
    }

    abstract public static function getInitialState(): State;

    protected function getState(string $identifier): State
    {
        $data = $this->dictionary->get($identifier);

        if ($data) {
            return State::createFromArray($data);
        }

        return State::createFromState(static::getInitialState());
    }

    protected function setState(string $identifier, callable $updateMethod): void
    {
        $currentState = $this->getState($identifier);

        /** @var State $nextState */
        $nextState = $updateMethod($currentState);

        $this->dictionary->save($identifier, $nextState->toArray());
    }
}