<?php

namespace App\Remote\Button;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class VolumeDownButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Change the volume down');
    }
}
