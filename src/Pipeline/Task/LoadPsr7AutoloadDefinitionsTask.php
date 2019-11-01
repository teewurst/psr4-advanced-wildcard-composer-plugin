<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task;

use Composer\Composer;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;

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
        $autoload = $this->composer->getPackage()->getAutoload()['psr-4'] ?? [];
        $devAutoload = $this->composer->getPackage()->getDevAutoload()['psr-4'] ?? [];

        // if no psr-4 namespaces are set
        if (!$autoload && !$devAutoload) {
            // interrupt pipeline
            return $payload;
        }

        $payload->setPsr4Definitions($autoload);
        $payload->setDevPsr4Definitions($devAutoload);
        return $pipeline->handle($payload);
    }
}
