<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\Projection;

use Dayuse\Istorija\Projection\State;
use PHPUnit\Framework\TestCase;

class StateTest extends TestCase
{
    /**
     * @test
     */
    public function could_get_initialized_value()
    {
        $state = new State([
            'title' => 'Le petit chat',
        ]);

        $this->assertEquals('Le petit chat', $state->get('title'));
    }

    /**
     * @test
     */
    public function could_set_then_get_value()
    {
        $state = new State();

        $updatedState = $state->set('title', 'Le petit chat');

        $this->assertEquals(null, $state->get('title'));
        $this->assertEquals('Le petit chat', $updatedState->get('title'));
    }

    /**
     * @test
     */
    public function could_copy()
    {
        $state = new State([
            'title' => 'Le petit chat',
        ]);

        $copiedState  = $state->copy();
        $updatedState = $state->set('title', 'Le petit chien');

        $this->assertEquals('Le petit chat', $state->get('title'));
        $this->assertEquals('Le petit chat', $copiedState->get('title'));
        $this->assertEquals('Le petit chien', $updatedState->get('title'));
    }

    /**
     * @test
     */
    public function could_set_then_merge_value()
    {
        $state = new State([
            'title' => 'Le petit chat',
        ]);

        $updatedState = $state->merge([
            'title' => 'Le gros chien',
        ]);

        $this->assertEquals('Le petit chat', $state->get('title'));
        $this->assertEquals('Le gros chien', $updatedState->get('title'));
    }

    /**
     * @test
     */
    public function could_serialize_to_array()
    {
        $state = new State([
            'title' => 'Le petit chat',
        ]);

        $this->assertEquals([
            'title' => 'Le petit chat',
        ], $state->toArray());
    }
}
