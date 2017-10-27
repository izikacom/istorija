<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\Process;

use Dayuse\Istorija\Process\ProcessId;
use Dayuse\Istorija\Process\State;
use PHPUnit\Framework\TestCase;

class StateTest extends TestCase
{
    /**
     * @test
     */
    public function could_get_initialized_value()
    {
        $processIdProphecy = $this->prophesize(ProcessId::class);

        $state = new State($processIdProphecy->reveal(), [
            'title' => 'Le petit chat',
        ]);

        $this->assertEquals('Le petit chat', $state->get('title'));
    }

    /**
     * @test
     */
    public function could_set_then_get_value()
    {
        $processIdProphecy = $this->prophesize(ProcessId::class);

        $state = new State($processIdProphecy->reveal());
        $state->set('title', 'Le petit chat');

        $this->assertEquals('Le petit chat', $state->get('title'));
    }

    /**
     * @test
     */
    public function could_set_then_merge_value()
    {
        $processIdProphecy = $this->prophesize(ProcessId::class);

        $state = new State($processIdProphecy->reveal());
        $state->set('title', 'Le petit chat');
        $state->merge([
            'title' => 'Le gros chien',
        ]);

        $this->assertEquals('Le gros chien', $state->get('title'));
    }

    /**
     * @test
     */
    public function could_serialize_to_array()
    {
        $processIdProphecy = $this->prophesize(ProcessId::class);
        $processIdProphecy->__toString()->willReturn('abc');

        $state = new State($processIdProphecy->reveal());
        $state->set('title', 'Le petit chat');

        $this->assertEquals([
            'processId' => 'abc',
            'data'      => [
                'title' => 'Le petit chat',
            ],
            'doneAt'    => null,
        ], $state->toArray());

        $state->done();

        $data = $state->toArray();

        // 2017-10-26T13:37:09.083004+00:00
        $this->assertNotEmpty($data['doneAt']);
    }

    /**
     * @test
     */
    public function could_not_be_done_twice()
    {
        $processIdProphecy = $this->prophesize(ProcessId::class);
        $processIdProphecy->__toString()->willReturn('abc');

        $state = new State($processIdProphecy->reveal());

        $this->assertFalse($state->isDone());

        $state->done();
        $this->assertTrue($state->isDone());

        $this->expectException(\InvalidArgumentException::class);
        $state->done();
    }

    /**
     * @test
     */
    public function could_not_set_data_on_done_state()
    {
        $processIdProphecy = $this->prophesize(ProcessId::class);
        $processIdProphecy->__toString()->willReturn('abc');

        $state = new State($processIdProphecy->reveal());
        $state->done();

        $this->expectException(\InvalidArgumentException::class);
        $state->set('title', 'Le petit chat');
    }

    /**
     * @test
     */
    public function could_not_merge_data_on_done_state()
    {
        $processIdProphecy = $this->prophesize(ProcessId::class);
        $processIdProphecy->__toString()->willReturn('abc');

        $state = new State($processIdProphecy->reveal());
        $state->done();

        $this->expectException(\InvalidArgumentException::class);
        $state->merge([
            'title' => 'Le petit chat',
        ]);
    }
}