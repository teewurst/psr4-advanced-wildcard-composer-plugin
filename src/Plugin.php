<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;

/**
 * Class Plugin
 *
 * @package teewurst\Prs4WildcardComposer
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class Plugin implements PluginInterface
{

    /** @var Composer */
    private $composer;
    /** @var IOInterface */
    private $io;
    /** @var Container */
    private $diContainer;

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
        $container = $this->getDiContainer();
        $container->set(IOInterface::class, $io);
        $container->set(Composer::class, $composer);
    }

    public static function getSubscribedEvents()
    {
        return array(
            'post-autoload-dump' => 'translateAdvancedHooks'
        );
    }

    public function translateAdvancedHooks(Event)
    {
        /** @var Pipeline|null $pipeline */
        $pipeline = $this->getDiContainer()->get(Pipeline::class);
        $payload = $this->getDiContainer()->get(Payload::class);

        if (!$pipeline || !$payload) {
            return;
        }
        $pipeline->handle($payload);
    }

    public function getDiContainer()
    {
        if (!$this->diContainer) {
            $factories = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
            $this->diContainer = new Container($factories);
        }

        return $this->diContainer;
    }
}
