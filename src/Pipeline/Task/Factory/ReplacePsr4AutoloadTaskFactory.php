<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory;

use Composer\Composer;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\ReplacePsr4AutoloadTask;

class ReplacePsr4AutoloadTaskFactory implements FactoryInterface
{

    public function __invoke(Container $container, string $name): object
    {
        return new ReplacePsr4AutoloadTask($container->get(Composer::class));
    }
}
