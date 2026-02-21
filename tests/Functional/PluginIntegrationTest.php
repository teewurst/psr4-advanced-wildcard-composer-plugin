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
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use teewurst\Prs4AdvancedWildcardComposer\Plugin;

/**
 * Class PluginIntegrationTest
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\tests\Functional
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class PluginIntegrationTest extends TestCase
{
    use ProphecyTrait;
    private const INTEGRATION_TEST_CONFIG_PATH = __DIR__ . '/../../src/config.php';
    private const VENDOR_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'vendor';

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

        // Suppress deprecation notices from vendor code (implicit nullable params in composer/composer 2.2.x)
        $errorLevel = error_reporting(E_ALL & ~E_DEPRECATED);
        $config = new Config(false);
        $config->merge(['config' => ['vendor-dir' => self::VENDOR_PATH]]);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($package);
        $composer->getConfig()->willReturn($config);
        error_reporting($errorLevel);

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
        self::assertSame('teewurst/integration-test', $file['name']);

        $autoloadFiles = $file['autoload']['files'] ?? [];
        self::assertContains('Helpers/one.php', $autoloadFiles, 'Generated autoload.files should contain expanded Helpers/one.php');
        self::assertContains('Helpers/two.php', $autoloadFiles, 'Generated autoload.files should contain expanded Helpers/two.php');
        self::assertCount(2, $autoloadFiles, 'Generated autoload.files should contain exactly two expanded helper files');

        $autoloadDevFiles = $file['autoload-dev']['files'] ?? [];
        self::assertContains('tests/helpers/bootstrap_test.php', $autoloadDevFiles, 'Generated autoload-dev.files should contain expanded bootstrap_test.php');
        self::assertContains('tests/helpers/helper_test.php', $autoloadDevFiles, 'Generated autoload-dev.files should contain expanded helper_test.php');
        self::assertCount(2, $autoloadDevFiles, 'Generated autoload-dev.files should contain exactly two expanded dev helper files');

        $psr4 = $file['autoload']['psr-4'] ?? [];
        self::assertTrue(
            isset($psr4['My\\Namespace\\Something']) || isset($psr4['My\\Namespace\\Something\\']),
            'Generated autoload.psr-4 should contain root package namespace My\\Namespace\\Something'
        );
        self::assertTrue(
            isset($psr4['My\\Namespace\\DomainA\\ModuleX']) || isset($psr4['My\\Namespace\\DomainA\\ModuleX\\']),
            'Generated autoload.psr-4 should contain expanded wildcard namespace DomainA\\ModuleX'
        );
        self::assertTrue(
            isset($psr4['My\\Namespace\\DomainB\\ModuleY']) || isset($psr4['My\\Namespace\\DomainB\\ModuleY\\']),
            'Generated autoload.psr-4 should contain expanded wildcard namespace DomainB\\ModuleY'
        );

        $psr4Dev = $file['autoload-dev']['psr-4'] ?? [];
        self::assertTrue(
            isset($psr4Dev['My\\Namespace\\Test\\Integration']) || isset($psr4Dev['My\\Namespace\\Test\\Integration\\']),
            'Generated autoload-dev.psr-4 should contain expanded test namespace Integration'
        );
        self::assertTrue(
            isset($psr4Dev['My\\Namespace\\Test\\Unit']) || isset($psr4Dev['My\\Namespace\\Test\\Unit\\']),
            'Generated autoload-dev.psr-4 should contain expanded test namespace Unit'
        );
    }

    /**
     * Returns use-cases
     *
     * @return array
     */
    public function pluginData(): array
    {
        return [
            'default test production case' => [
                require __DIR__ . '/testcases/default_case/extra.php',
                require __DIR__ . '/testcases/default_case/autoload.php'
            ]
        ];
    }
}
