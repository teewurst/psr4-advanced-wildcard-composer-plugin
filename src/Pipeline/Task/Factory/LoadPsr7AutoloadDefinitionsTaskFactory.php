<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory;

use Composer\Composer;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\LoadPsr7AutoloadDefinitionsTask;

/**
 * Class LoadPsr7AutoloadDefinitionsTaskFactory
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class LoadPsr7AutoloadDefinitionsTaskFactory implements FactoryInterface
{

    /**
     * Creates an Instance of LoadPsr7AutoloadDefinitionsTask
     *
     * @param Container $container
     * @param string    $name
     *
     * @return LoadPsr7AutoloadDefinitionsTask
     */
    public function __invoke(Container $container, string $name)
    {
        return new LoadPsr7AutoloadDefinitionsTask($container->get(Composer::class));
    }
}
