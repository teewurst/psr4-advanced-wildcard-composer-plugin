<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory;

use Composer\Composer;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\ExpandWildcardFilesTask;

/**
 * Class ExpandWildcardFilesTaskFactory
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory
 */
class ExpandWildcardFilesTaskFactory implements FactoryInterface
{
    /**
     * Creates an Instance of ExpandWildcardFilesTask
     *
     * @param Container $container
     * @param string    $name
     *
     * @return ExpandWildcardFilesTask
     */
    public function __invoke(Container $container, string $name)
    {
        /** @var Composer $composer */
        $composer = $container->get(Composer::class);
        $vendorDir = $composer->getConfig()->get('vendor-dir');
        $projectRoot = dirname($vendorDir);

        return new ExpandWildcardFilesTask($projectRoot);
    }
}
