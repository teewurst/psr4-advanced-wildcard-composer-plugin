<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task;

use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\Psr4Autoload;
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

    /** @var Psr4Autoload */
    private $autoloadAccessor;

    /**
     * BuildPsr7AutoloadArrayTask constructor.
     *
     * @param Psr4Autoload $autoloadAccessor
     */
    public function __construct(Psr4Autoload $autoloadAccessor)
    {
        $this->autoloadAccessor = $autoloadAccessor;
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
        $payload->setFullPsr4Definitions($this->autoloadAccessor->requireCurrentFile());
        return $pipeline->handle($payload);
    }
}
