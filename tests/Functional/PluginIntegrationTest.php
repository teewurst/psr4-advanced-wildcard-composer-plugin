<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Functional;

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\RootPackage;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use PHPStan\Testing\TestCase;
use teewurst\Prs4AdvancedWildcardComposer\Plugin;

class PluginIntegrationTest extends TestCase
{
    private const INTEGRATION_TEST_CONFIG_PATH = __DIR__ . '/../../src/config.php';
    private const VENDOR_PATH = __DIR__ . '/files/vendor';

    public static function setUpBeforeClass(): void
    {
        @unlink(self::VENDOR_PATH);
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        @unlink(self::VENDOR_PATH);
        parent::tearDownAfterClass();
    }

    public function tearDown(): void
    {
        @unlink(self::VENDOR_PATH);
        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider pluginData
     * @return void
     */
    public function checkIfComposerExecutesPluginCorrectly(array $extraConfig, array $autoloadConfig)
    {

        $package = new RootPackage('name', '1.0', 'v1.0');
        $package->setExtra($extraConfig);
        $package->setAutoload($autoloadConfig);
        $package->setDevAutoload([]);

        $io = $this->prophesize(IOInterface::class);

        $config = $this->prophesize(Config::class);
        $config->get('vendor-dir')->willReturn(self::VENDOR_PATH);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($package);
        $composer->getConfig()->willReturn($config->reveal());

        $event = $this->prophesize(Event::class);
        $event->isDevMode()->willReturn(true);

        // Composer generates Plugin File
        $plugin = new Plugin(self::INTEGRATION_TEST_CONFIG_PATH);
        self::assertInstanceOf(EventSubscriberInterface::class, $plugin);

        $plugin->activate($composer->reveal(), $io->reveal());

        // composer requires Hooks
        $hook = Plugin::getSubscribedEvents();

        self::assertTrue($plugin->{$hook[ScriptEvents::PRE_AUTOLOAD_DUMP][0]}($event->reveal()));

        self::assertFileExists(__DIR__ . '/files/composer.development.json');
        $file = json_decode(file_get_contents(__DIR__ . '/files/composer.development.json'), true);
        self::assertSame($package->getAutoload(), $file['autoload']);
        self::assertSame($package->getDevAutoload(), $file['autoload-dev']);
        self::assertSame('teewurst/integration-test', $file['name']);
    }

    public function pluginData()
    {
        return [
            'default test production case' => [
                require __DIR__ . '/testcases/default_case/extra.php',
                require __DIR__ . '/testcases/default_case/autoload.php'
            ]
        ];
    }
}
