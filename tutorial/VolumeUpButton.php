<?php

namespace App\Remote\Button;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class VolumeUpButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Change the volume up');
    }
}
