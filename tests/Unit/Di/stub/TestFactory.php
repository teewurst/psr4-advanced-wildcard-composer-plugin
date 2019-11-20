<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Di\stub;

use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;

class TestFactory implements FactoryInterface
{

    /**
     * Creates an Instance of certain objects
     *
     * @param Container $container
     * @param string    $name
     *
     * @return object
     */
    public function __invoke(Container $container, string $name)
    {
        return new \stdClass;
    }
}
