<?php

namespace App\Remote\Button;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem("volume-down", 10)]
final class VolumeDownButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Change the volume down');
    }
}
