<?php

namespace Extendy\JormallSmsBundle;

use Extendy\JormallSmsBundle\DependencyInjection\ExtendyJormallSmsExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ExtendyJormallSmsBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new ExtendyJormallSmsExtension();
    }
}