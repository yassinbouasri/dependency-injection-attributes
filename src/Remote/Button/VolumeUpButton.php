<?php

namespace App\Remote\Button;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem("volume-up", 20)]
final class VolumeUpButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Change the volume up');
    }
}
