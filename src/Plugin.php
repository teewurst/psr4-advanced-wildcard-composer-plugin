<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;

/**
 * Class Plugin
 *
 * @package teewurst\Prs4WildcardComposer
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class Plugin implements PluginInterface, EventSubscriberInterface
{
    public const NAME = 'teewurst/psr4-advanced-wildcard-composer-plugin';

    /** @var Composer */
    private $composer;
    /** @var IOInterface */
    private $io;
    /** @var Container */
    private $diContainer;
    /** @var string */
    private $configPath;

    /**
     * Plugin constructor.
     *
     * @param string $configPath
     */
    public function __construct($configPath = __DIR__ . DIRECTORY_SEPARATOR . 'config.php')
    {
        $this->configPath = $configPath;
    }

    /**
     * @param Composer    $composer
     * @param IOInterface $io
     *
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::PRE_AUTOLOAD_DUMP => ['translateAdvancedHooks', 10] // high prio, or else other plugins can't handle wildcards
        );
    }

    /**
     * Creates Di Container + Execute Pipeline
     *
     * @param Event $event
     *
     * @return bool
     */
    public function translateAdvancedHooks(Event $event)
    {
        /** @var Pipeline|null $pipeline */
        $container = $this->getDiContainer();

        // set classes for DI
        $container->set(IOInterface::class, $this->io);
        $container->set(Composer::class, $this->composer);
        $container->set(Event::class, $event);

        // get execution classes
        $pipeline = $container->get(Pipeline::class);
        $payload = $container->get(Payload::class);

        // check if everything went right
        if (!$pipeline || !$payload) {
            return false;
        }

        // execute
        $pipeline->handle($payload);
        return true;
    }

    /**
     * Lazy generation of DI container
     *
     * @return Container
     */
    public function getDiContainer()
    {
        if ($this->diContainer === null) {
            $factories = require $this->configPath;
            $this->diContainer = new Container($factories);
        }

        return $this->diContainer;
    }

    /**
     * Returns field Composer
     *
     * @return Composer
     */
    public function getComposer(): Composer
    {
        return $this->composer;
    }

    /**
     * Returns field Io
     *
     * @return IOInterface
     */
    public function getIo(): IOInterface
    {
        return $this->io;
    }

    /**
     * Sets field
     *
     * @param Container $diContainer
     *
     * @return void
     */
    public function setDiContainer(Container $diContainer): void
    {
        $this->diContainer = $diContainer;
    }
}
