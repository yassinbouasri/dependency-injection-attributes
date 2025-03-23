<?php

namespace App\Remote;

interface RemoteInterface {

    public function press(string $name): void;

    public function buttons(): iterable;
}