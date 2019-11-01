<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task;

use Composer\Composer;
use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\Psr4Autoload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;

/**
 * Class ReplacePsr4AutoloadFileTask
 *
 * Replaces current psr4 autoload file with enhanced version
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class ReplacePsr4AutoloadTask implements TaskInterface
{

    /** @var Composer */
    private $composer;

    public function __construct(Composer $composer)
    {
        $this->composer = $composer;
    }

    /**
     * Replaces current psr4 autoload file with enhanced version
     *
     * @param Payload  $payload
     * @param Pipeline $pipeline
     *
     * @return Payload
     */
    public function __invoke(Payload $payload, Pipeline $pipeline): Payload
    {
        $package = $this->composer->getPackage();

        $currentAutoload = $package->getAutoload();
        $currentDevAutoload = $package->getDevAutoload();

        $currentAutoload['psr-4'] = $payload->getPsr4Definitions();
        $currentDevAutoload['psr-4'] = $payload->getDevPsr4Definitions();

        $package->setAutoload($currentAutoload);
        $package->setDevAutoload($currentDevAutoload);

        return $pipeline->handle($payload);
    }
}
