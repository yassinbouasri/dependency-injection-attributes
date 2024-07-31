<?php

namespace App\Remote\Button;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ChannelDownButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Change the channel down');
    }
}
