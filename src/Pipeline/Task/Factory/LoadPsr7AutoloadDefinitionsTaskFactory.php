<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory;

use Composer\Composer;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;
use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\Psr4Autoload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\LoadPsr7AutoloadDefinitionsTask;

class LoadPsr7AutoloadDefinitionsTaskFactory implements FactoryInterface
{

    public function __invoke(Container $container, string $name): object
    {
        return new LoadPsr7AutoloadDefinitionsTask($container->get(Composer::class));
    }
}
