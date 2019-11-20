<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Di;

/**
 * Interface FactoryInterface
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Di
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
interface FactoryInterface
{

    /**
     * Creates an Instance of certain objects
     *
     * @param Container $container
     * @param string    $name
     *
     * @return object
     */
    public function __invoke(Container $container, string $name);
}
