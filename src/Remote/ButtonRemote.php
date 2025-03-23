<?php

namespace App\Remote;

use App\Remote\Button\ButtonInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\ServiceCollectionInterface;

#[AsAlias]
final class ButtonRemote implements RemoteInterface
{
    public function __construct(
        #[AutowireLocator(ButtonInterface::class, "key")]
        private ServiceCollectionInterface $buttons,
    ) {
    }

    public function press(string $name): void
    {
        $this->buttons->get($name)->press();
    }

    public function buttons(): iterable
    {
        return array_keys($this->buttons->getProvidedServices());
    }
}