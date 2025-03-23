<?php

namespace App\Remote\Button;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem("power", 50)]
final class PowerButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Power on/off the TV');
    }
}
