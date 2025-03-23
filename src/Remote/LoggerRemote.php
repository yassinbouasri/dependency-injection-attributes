<?php

namespace App\Remote;

use Psr\Log\LoggerInterface;

final class LoggerRemote implements RemoteInterface
{

    public function __construct(
        private LoggerInterface $logger,
        private ButtonRemote $inner
    ) {}

    public function press(string $name): void
    {

        $this->logger->info("Pressing button {name}",[
            "name" => $name
        ] );

        $this->inner->press($name);

        $this->logger->info("Pressed button {name}",[
            "name" => $name
        ] );
    }

    public function buttons(): iterable
    {
        return $this->inner->buttons();
    }

}