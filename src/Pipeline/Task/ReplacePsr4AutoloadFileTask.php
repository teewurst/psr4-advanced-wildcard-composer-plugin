<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task;

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
class ReplacePsr4AutoloadFileTask implements TaskInterface
{

    /** @var string */
    private $baseDir;
    /** @var Psr4Autoload */
    private $psr4Autoload;

    public function __construct(Psr4Autoload $psr4Autoload)
    {
        $this->psr4Autoload = $psr4Autoload;
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
        $this->psr4Autoload->setContents($payload->getFullPsr4Definitions());
        $this->psr4Autoload->persist();

        return $pipeline->handle($payload);
    }
}
