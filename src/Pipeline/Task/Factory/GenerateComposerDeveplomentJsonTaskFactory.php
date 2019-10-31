<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory;

use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;
use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\ComposerJsonDevelopment;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\GenerateComposerDeveplomentJsonTask;

class GenerateComposerDeveplomentJsonTaskFactory implements FactoryInterface
{

    public function __invoke(Container $container, string $name): object
    {
        return new GenerateComposerDeveplomentJsonTask($container->get(ComposerJsonDevelopment::class));
    }
}
