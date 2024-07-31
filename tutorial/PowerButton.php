<?php

namespace App\Remote\Button;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class PowerButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Power on/off the TV');
    }
}
