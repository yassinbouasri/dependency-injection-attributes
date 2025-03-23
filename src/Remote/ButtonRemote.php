<?php

namespace App\Remote;

use App\Remote\Button\ButtonInterface;
use App\Remote\Button\ChannelDownButton;
use App\Remote\Button\ChannelUpButton;
use App\Remote\Button\PowerButton;
use App\Remote\Button\VolumeDownButton;
use App\Remote\Button\VolumeUpButton;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

final class ButtonRemote
{
    public function __construct(
        #[AutowireIterator(ButtonInterface::class, "key")]
        private iterable $buttons,
    ) {
    }

    public function press(string $name): void
    {
        $this->buttons->get($name)->press();
    }

    public function buttons(): iterable
    {
        $buttons = [];
        
        foreach($this->buttons as $name => $button){
            $buttons[] = $name;
        }

        return $buttons;
    }
}