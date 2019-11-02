<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task;

use Composer\Composer;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;
use teewurst\Prs4AdvancedWildcardComposer\Plugin;

/**
 * Class BuildPsr7AutoloadArrayTask
 *
 * Open current psr4 autoload file and parse all contents + save them to payload
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class LoadPsr7AutoloadDefinitionsTask implements TaskInterface
{

    /** @var Composer */
    private $composer;

    /**
     * BuildPsr7AutoloadArrayTask constructor.
     *
     * @param Composer $composer
     */
    public function __construct(Composer $composer)
    {
        $this->composer = $composer;
    }

    /**
     * Open current psr4 autoload file and parse all contents + save them to payload
     *
     * @param Payload  $payload
     * @param Pipeline $pipeline
     *
     * @return Payload
     */
    public function __invoke(Payload $payload, Pipeline $pipeline): Payload
    {
        $rootPackage = $this->composer->getPackage();
        $pluginConfig = $rootPackage->getExtra()[Plugin::NAME] ?? false;

        // if no config is set
        if (!$pluginConfig) {
            // interrupt pipeline
            return $payload;
        }

        $autoload = ($rootPackage->getAutoload()['psr-4'] ?? []) + $pluginConfig['autoload']['psr-4'] ?? [];
        $devAutoload = ($rootPackage->getDevAutoload()['psr-4'] ?? []) + $pluginConfig['autoload-dev']['psr-4'] ?? [];

        $payload->setPsr4Definitions($autoload);
        $payload->setDevPsr4Definitions($devAutoload);
        return $pipeline->handle($payload);
    }
}
