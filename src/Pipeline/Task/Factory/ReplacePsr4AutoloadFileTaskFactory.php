<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory;

use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;
use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\Psr4Autoload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\ReplacePsr4AutoloadFileTask;

class ReplacePsr4AutoloadFileTaskFactory implements FactoryInterface
{

    public function __invoke(Container $container, string $name): object
    {
        return new ReplacePsr4AutoloadFileTask($container->get(Psr4Autoload::class));
    }
}
