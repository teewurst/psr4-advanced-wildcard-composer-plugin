<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\FileAccessor\Factory;

use Composer\Composer;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;
use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\ComposerDevelopmentJson;

/**
 * Class ComposerJsonDevelopmentFactory
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\FileAccessor\Factory
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class ComposerDevelopmentJsonFactory implements FactoryInterface
{

    /**
     * Creates an Instance of ComposerJsonDevelopmentFactory
     *
     * @param Container $container
     * @param string    $name
     *
     * @return object
     */
    public function __invoke(Container $container, string $name)
    {
        /** @var Composer $composer */
        $composer = $container->get(Composer::class);
        return new ComposerDevelopmentJson($composer->getConfig()->get('vendor-dir'));
    }
}
