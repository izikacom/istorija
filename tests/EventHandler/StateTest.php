<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\EventHandler;

use Dayuse\Istorija\EventHandler\State;
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
    public function could_set_then_get_value_using_absolute_key()
    {
        $state = new State([
            'owner.name' => 'Jane Doe',
            'owner'      => [
                'name' => 'John Doe',
            ],
        ]);

        $this->assertEquals('John Doe', $state->get('owner.name'));
        $this->assertEquals('Jane Doe', $state->get('$owner.name'));

        $updatedState = $state
            ->set('$owner.name', 'Jean Michel')
            ->set('owner.name', 'Philippe Lu');

        $this->assertEquals('John Doe', $state->get('owner.name'));
        $this->assertEquals('Jane Doe', $state->get('$owner.name'));

        $this->assertEquals('Philippe Lu', $updatedState->get('owner.name'));
        $this->assertEquals('Jean Michel', $updatedState->get('$owner.name'));
    }

    /**
     * @test
     */
    public function could_set_then_get_value_using_dot_notation()
    {
        $state = new State([
            'vehicle' => [
                'id'      => 'vehicle-123',
                'drivers' => [
                    'Jean',
                    'Paul',
                    'Michel',
                ],
                'owner'   => [
                    'name' => 'John Doe',
                ],
            ],
        ]);

        $this->assertEquals('vehicle-123', $state->get('vehicle.id'));
        $this->assertEquals([
            'Jean',
            'Paul',
            'Michel',
        ], $state->get('vehicle.drivers'));
        $updatedState = $state
            ->set('vehicle.owner.phoneNumber', '0626525698')
            ->set('vehicle.owner.name', 'Jane Doe')
            ->set('vehicle.title', 'La rapide')
            ->set('vehicle.drivers.1', 'Louise');

        $this->assertEquals('John Doe', $state->get('vehicle.owner.name'));
        $this->assertEquals(null, $state->get('vehicle.owner.phoneNumber'));
        $this->assertEquals(null, $state->get('vehicle.title'));
        $this->assertEquals([
            'Jean',
            'Paul',
            'Michel',
        ], $state->get('vehicle.drivers'));
        $this->assertEquals('La rapide', $updatedState->get('vehicle.title'));
        $this->assertEquals('0626525698', $updatedState->get('vehicle.owner.phoneNumber'));
        $this->assertEquals('Jane Doe', $updatedState->get('vehicle.owner.name'));
        $this->assertEquals([
            'Jean',
            'Louise',
            'Michel',
        ], $updatedState->get('vehicle.drivers'));
    }

    /**
     * @test
     */
    public function could_override_value_using_set_and_dot_notation()
    {
        $state = new State([
            'vehicle' => [
                'id'    => 'vehicle-123',
                'owner' => [
                    'name' => 'John Doe',
                ],
            ],
        ]);

        $this->assertEquals('vehicle-123', $state->get('vehicle.id'));
        $updatedState = $state
            ->set('vehicle.owner', 'Jane Doe');

        $this->assertEquals('John Doe', $state->get('vehicle.owner.name'));
        $this->assertEquals('Jane Doe', $updatedState->get('vehicle.owner'));
    }

    /**
     * @test
     */
    public function could_append_value_to_key()
    {
        $state = new State([
            'names' => ['John'],
        ]);

        $this->assertCount(1, $state->get('names'));

        $updatedState = $state->append('names', 'Jane');

        $this->assertCount(1, $state->get('names'));
        $this->assertCount(2, $updatedState->get('names'));
    }

    /**
     * @test
     */
    public function could_remove_value_from_key()
    {
        $state = new State([
            'names'    => ['John', 'Doe', 'Jane'],
            'vehicles' => [
                'corsa'  => [
                    'owners' => ['Doe'],
                ],
                'espace' => [
                    'owners' => ['John', 'Doe'],
                ],
            ],
        ]);

        $this->assertCount(3, $state->get('names'));

        $updatedState = $state->without('names', 'Doe');

        $this->assertCount(3, $state->get('names'));
        $this->assertCount(2, $updatedState->get('names'));

        $this->assertSame([
            'names'    => ['John', 'Doe', 'Jane'],
            'vehicles' => [
                'corsa'  => [
                    'owners' => ['Doe'],
                ],
                'espace' => [
                    'owners' => ['John'],
                ],
            ],
        ], $state->without('vehicles.espace.owners', 'Doe')->all());
    }

    /**
     * @test
     */
    public function could_append_value_to_root_key()
    {
        $state = new State();

        $updatedState = $state->append('users', [
            'firstName' => 'John',
            'lastName'  => 'Doe',
        ])->set('users.0.firstName', 'Jane');

        $this->assertSame([
            'users' => [
                [
                    'firstName' => 'Jane',
                    'lastName'  => 'Doe',
                ],
            ],
        ], $updatedState->all());
    }

    /**
     * @test
     */
    public function could_append_value_to_key_using_dot_notation()
    {
        $state = new State([
            'vehicles' => [
                'owners' => ['John'],
            ],
        ]);

        $this->assertCount(1, $state->get('vehicles.owners'));

        $updatedState = $state->append('vehicles.owners', 'Jane');

        $this->assertCount(1, $state->get('vehicles.owners'));
        $this->assertCount(2, $updatedState->get('vehicles.owners'));
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