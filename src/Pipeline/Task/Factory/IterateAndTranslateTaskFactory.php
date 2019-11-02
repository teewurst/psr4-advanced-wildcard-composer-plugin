<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory;

use Composer\Composer;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\IterateAndTranslateTask;

class IterateAndTranslateTaskFactory implements FactoryInterface
{

    /**
     * Creates an Instance of IterateAndTranslateTask
     *
     * @param Container $container
     * @param string    $name
     *
     * @return object
     */
    public function __invoke(Container $container, string $name): object
    {
        /** @var Composer $composer */
        $composer = $container->get(Composer::class);
        return new IterateAndTranslateTask($composer->getConfig()->get('vendor-dir'));
    }
}
