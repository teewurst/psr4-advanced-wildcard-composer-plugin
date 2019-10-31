<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task;

use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\ComposerJsonDevelopment;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;

/**
 * Class GenerateComposerDeveplomentJsonTask
 *
 * Creates a pseudo composer json file to be used in several IDEs (Auto completion, namespace recognition ...)
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class GenerateComposerDeveplomentJsonTask implements TaskInterface
{

    /** @var ComposerJsonDevelopment */
    private $composerFile;
    /** @var bool */
    private $developmentMode;

    public function __construct(ComposerJsonDevelopment $composerFile, bool $developmentMode = true)
    {
        $this->composerFile = $composerFile;
        $this->developmentMode = $developmentMode;
    }

    /**
     * Creates a pseudo composer json file to be used in several IDEs
     *
     * @param Payload  $payload
     * @param Pipeline $pipeline
     *
     * @return Payload
     */
    public function __invoke(Payload $payload, Pipeline $pipeline): Payload
    {
        if ($this->developmentMode) {
            $this->composerFile->setDefinitons($payload->getFullPsr4Definitions());
            $this->composerFile->persist();
        }

        return $pipeline->handle($payload);
    }
}
