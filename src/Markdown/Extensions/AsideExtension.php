<?php

namespace Ibis\Markdown\Extensions;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

class AsideExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addBlockStartParser(new AsideParser())
            ->addRenderer(Aside::class, new AsideRenderer());
    }
}
