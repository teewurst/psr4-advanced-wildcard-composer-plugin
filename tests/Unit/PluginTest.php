<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;
use teewurst\Prs4AdvancedWildcardComposer\Plugin;

/**
 * Class PluginTest
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\tests\Unit
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class PluginTest extends TestCase
{
    use ProphecyTrait;
    /**
     * @test
     * @return void
     */
    public function checkIfThePluginHooksIntoAfterDumpAutoload()
    {
        $hooks = Plugin::getSubscribedEvents();

        self::assertSame(
            ['translateAdvancedHooks', 10], $hooks[ScriptEvents::PRE_AUTOLOAD_DUMP]
        );
    }

    /**
     * @test
     * @return void
     */
    public function checkIfActivateSetsComposerAndIo()
    {
        $composer = $this->prophesize(Composer::class);
        $io = $this->prophesize(IOInterface::class);

        $plugin = new Plugin();
        $plugin->activate($composer->reveal(), $io->reveal());

        self::assertSame($composer->reveal(), $plugin->getComposer());
        self::assertSame($io->reveal(), $plugin->getIo());
    }

    /**
     * @test
     * @return void
     */
    public function checkIftranslateAdvancedHooksSetsUpDiContainerAndStartsPipe()
    {
        $event = $this->prophesize(Event::class);
        $container = $this->prophesize(Container::class);
        $pipeline = $this->prophesize(Pipeline::class);
        $payload = $this->prophesize(Payload::class);

        $container->set(IOInterface::class, null)->hasReturnVoid();
        $container->set(Composer::class, null)->hasReturnVoid();
        $container->set(Event::class, $event->reveal())->hasReturnVoid();

        $container->get(Pipeline::class)->willReturn($pipeline->reveal());
        $container->get(Payload::class)->willReturn($payload->reveal());

        $pipeline->handle($payload->reveal())->shouldBeCalled()->willReturn($payload->reveal());

        $plugin = new Plugin();
        $plugin->setDiContainer($container->reveal());
        self::assertTrue($plugin->translateAdvancedHooks($event->reveal()));
    }

    /**
     * @test
     * @return void
     */
    public function checkIfDiContainerIsCreatedCorrectly()
    {
        $plugin = new Plugin(__DIR__ . '/files/di-config.php');
        $container = $plugin->getDiContainer();

        self::assertInstanceOf(Container::class, $container);
        self::assertNull($container->get('otherKey'));
    }

    /**
     * @test
     * @return void
     */
    public function checkIfPluginIsInterrupedIfItIsNotPossibleToCreatePipeOrPayload()
    {
        $event = $this->prophesize(Event::class);
        $container = $this->prophesize(Container::class);
        $pipeline = $this->prophesize(Pipeline::class);
        $payload = $this->prophesize(Payload::class);

        $container->set(IOInterface::class, null)->hasReturnVoid();
        $container->set(Composer::class, null)->hasReturnVoid();
        $container->set(Event::class, $event->reveal())->hasReturnVoid();

        $container->get(Pipeline::class)->willReturn(null, $pipeline->reveal());
        $container->get(Payload::class)->willReturn($payload->reveal(), null);

        $pipeline->handle($payload->reveal())->shouldNotBeCalled();

        $plugin = new Plugin();
        $plugin->setDiContainer($container->reveal());
        self::assertFalse($plugin->translateAdvancedHooks($event->reveal()));
        self::assertFalse($plugin->translateAdvancedHooks($event->reveal()));
    }

    /**
     * @test
     */
    public function checkIfDeactivateRunsWithoutError()
    {
        $composer = $this->prophesize(Composer::class)->reveal();
        $io = $this->prophesize(IOInterface::class)->reveal();

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->deactivate($composer, $io);

        self::assertTrue(true, 'deactivate must not throw');
    }

    /**
     * @test
     */
    public function checkIfUninstallRunsWithoutError()
    {
        $composer = $this->prophesize(Composer::class)->reveal();
        $io = $this->prophesize(IOInterface::class)->reveal();

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->uninstall($composer, $io);

        self::assertTrue(true, 'uninstall must not throw');
    }
}
