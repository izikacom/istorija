<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\Process;

use Dayuse\Istorija\Process\State;
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
            'data'     => [
                'title' => 'Le petit chat',
            ],
            'closedAt' => null,
        ], $state->toArray());
    }

    /**
     * @test
     */
    public function could_not_be_closed_twice()
    {
        $state = new State();

        $this->assertFalse($state->isClosed());

        $updatedState = $state->close();

        $this->assertFalse($state->isClosed());
        $this->assertTrue($updatedState->isClosed());

        $this->expectException(\InvalidArgumentException::class);
        $updatedState->close();
    }

    /**
     * @test
     */
    public function could_not_set_data_on_done_state()
    {
        $state = new State();
        $state->close();

        $updatedState = $state->close();

        $this->expectException(\InvalidArgumentException::class);
        $updatedState->set('title', 'Le petit chat');
    }

    /**
     * @test
     */
    public function could_not_merge_data_on_done_state()
    {
        $state = new State();
        $updatedState = $state->close();

        $this->expectException(\InvalidArgumentException::class);
        $updatedState->merge([
            'title' => 'Le petit chat',
        ]);
    }
}
