<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\FileAccessor\Factory;

use Composer\Composer;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;
use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\ComposerJsonDevelopment;
use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\Psr4Autoload;

/**
 * Class Psr4AutloadFactory
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\FileAccessor\Factory
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class Psr4AutloadFactory implements FactoryInterface
{

    /**
     * Creates an Instance of Psr4Autload
     *
     * @param Container $container
     * @param string    $name
     *
     * @return object
     */
    public function __invoke(Container $container, string $name): object
    {
        /** @var Composer $composer */
        $composer = $container->get(ComposerJsonDevelopment::class);
        return new Psr4Autoload($composer->getConfig()->get('vendor-dir'));
    }
}
