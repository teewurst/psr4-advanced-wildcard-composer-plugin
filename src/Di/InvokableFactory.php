<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Di;

/**
 * Class InvokableFactory
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Di
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class InvokableFactory implements FactoryInterface
{

    /**
     * Creates an Instance of $name
     *
     * @param Container $container
     * @param string    $name
     *
     * @return object
     */
    public function __invoke(Container $container, string $name)
    {
        return new $name;
    }
}
