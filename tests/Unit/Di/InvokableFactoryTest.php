<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Di;

use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\InvokableFactory;
use PHPUnit\Framework\TestCase;
use teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Di\stub\TestFactory;

/**
 * Class InvokableFactoryTest
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Di
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class InvokableFactoryTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function checkIfItCreatesClassesByItsName()
    {
        $container = $this->prophesize(Container::class);

        $factory = new InvokableFactory();
        self::assertInstanceOf(TestFactory::class, $factory($container->reveal(), TestFactory::class));
    }
}
