<?php

namespace App\Remote\Button;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag()]
interface ButtonInterface
{
    public function press(): void;
}