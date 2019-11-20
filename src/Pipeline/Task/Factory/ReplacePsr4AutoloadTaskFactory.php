<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory;

use Composer\Composer;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\ReplacePsr4AutoloadTask;

/**
 * Class ReplacePsr4AutoloadTaskFactory
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class ReplacePsr4AutoloadTaskFactory implements FactoryInterface
{

    /**
     * Creates an Instance of ReplacePsr4AutoloadTask
     *
     * @param Container $container
     * @param string    $name
     *
     * @return ReplacePsr4AutoloadTask
     */
    public function __invoke(Container $container, string $name)
    {
        return new ReplacePsr4AutoloadTask($container->get(Composer::class));
    }
}
