<?php

declare(strict_types=1);

namespace jirik1h\ZaslatAuthBundle;

use jirik1h\ZaslatAuthBundle\DependencyInjection\ZaslatAuthExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class ZaslatAuthBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new ZaslatAuthExtension();
    }
}