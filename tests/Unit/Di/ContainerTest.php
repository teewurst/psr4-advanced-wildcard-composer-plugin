<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Di;

use PHPUnit\Framework\TestCase;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Di\stub\TestFactory;

class ContainerTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function checkIfHasChecksForExistingKey()
    {
        $container = new Container([], ['exists' => 'lala']);

        self::assertTrue($container->has('exists'));
        self::assertFalse($container->has('doesNotExists'));
    }

    /**
     * @test
     * @return void
     */
    public function checkIfGetReturnsSimpleValues()
    {
        $container = new Container([],  ['exists' => 'lala']);

        self::assertSame('lala', $container->get('exists'));
        self::assertNull($container->get('doesNotExists'));
    }

    /**
     * @test
     * @return void
     */
    public function checkIfSetSetsAndOverwritesStoreValues()
    {
        $container = new Container([], []);

        self::assertFalse($container->has('key'));
        $container->set('key', 'true');
        self::assertSame('true', $container->get('key'));
        $container->set('key', 'other');
        self::assertSame('other', $container->get('key'));
    }

    /**
     * @test
     * @return void
     */
    public function checkIfGetUsesFactoryToCreateClasses()
    {
        $container = new Container(['key' => TestFactory::class]);

        $result = $container->get('key');
        self::assertInstanceOf(\stdClass::class, $result);
        $secondTry = $container->get('key');
        self::assertSame($result, $secondTry);
    }
}
