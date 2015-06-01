<?php

namespace spec\FSM;

use FSM\Adapters\GraphStructure;
use FSM\Contracts\EventInterface;
use FSM\Contracts\StructureInterface;
use FSM\Exceptions\UninitialisedException;
use FSM\States\State;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\Event;

class MachineSpec extends ObjectBehavior
{
    function let(EventInterface $eventInterface)
    {
        $this->beConstructedWith($eventInterface);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('FSM\Machine');
    }

    function it_should_throw_uninitialised_exception()
    {
        $this->shouldThrow(UninitialisedException::class)->during('transition', ['nextstate', []]);
        $this->shouldThrow(UninitialisedException::class)->during('handle', ['handler', []]);
    }

    function it_should_set_an_id()
    {
        $this->setId('id');
    }

    function it_should_get_an_id()
    {
        $this->getId()->shouldReturn('machine');

        $this->setId('id');

        $this->getId()->shouldReturn('id');
    }
    function it_should_set_a_structure(StructureInterface $interface)
    {
        $this->setStructure($interface);
    }

    function it_should_get_the_structure(StructureInterface $interface)
    {
        $this->setStructure($interface);

        $this->getStructure($interface)->shouldReturn($interface);
    }

    function it_should_set_an_active_state(State $state)
    {
        $this->setState($state);
    }

    function it_should_get_the_active_state(State $state)
    {
        $this->setState($state);

        $this->getState()->shouldReturn($state);
    }

    function it_should_initialise(GraphStructure $structure, State $state)
    {
        $state->onEnter($state)->shouldBeCalled()->willReturn(true);

        $structure->getInitialState(null)->shouldBeCalled()->willReturn($state);

        $this->setStructure($structure);

        $this->initialise();
    }

    function it_should_transition(GraphStructure $structure, State $state)
    {
        $state->getId()->shouldBecalled()->willReturn('state');
        $state->onExit($state)->shouldBeCalled()->willReturn(true);
        $state->onEnter($state)->shouldBeCalled()->willReturn(true);

        $this->setState($state);

        $structure->canTransitionFrom('state', 'newstate')->shouldBeCalled()->willReturn(true);
        $structure->getState('newstate')->shouldBeCalled()->willReturn($state);
        $structure->getState('state')->shouldBeCalled()->willReturn($state);

        $this->setStructure($structure);

        $this->transition('newstate');
    }

    function it_should_handle_state_methods(State $state)
    {
        $state->onExit($state)->shouldBeCalled()->willReturn(true);

        $this->setState($state);

        $this->handle('onExit', []);
    }

    function it_should_emit_events(EventInterface $events)
    {
        $this->emit('transition', []);
    }

    function it_should_register_event_listeners()
    {
        $this->listen('transition', function() {});
    }
}
