<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task;

use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;

/**
 * Class IterateAndTranslateTask
 *
 * Iterates all Advanced Key/Value Pairs + finds an structures Class names accordingly
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class IterateAndTranslateTask implements TaskInterface
{

    /**
     * Iterates all Advanced Key/Value Pairs + finds an structures Class names accordingly
     *
     * @param Payload  $payload
     * @param Pipeline $pipeline
     *
     * @return Payload
     */
    public function __invoke(Payload $payload, Pipeline $pipeline): Payload
    {
        $advancedWildcards = $payload->getAdvancedWildcards();
        $psr4Definitions = $payload->getFullPsr4Definitions();

        $newDefinitions = [];
        foreach ($advancedWildcards as $nameSpace) {
            foreach ($psr4Definitions[$nameSpace] as $replacementPath) {
                foreach ($this->getMatchingFolders($replacementPath) as $file) {
                    // get regex to read values from $file
                    $pattern = $this->getRegexFromGlob($replacementPath);

                    // read values from path
                    preg_match_all($pattern, $file, $matches);

                    // remove full match
                    unset($matches[0]);

                    // fill namespace and add path
                    $newDefinitions[sprintf($nameSpace, ...$matches)][] = $file;
                }

                unset($psr4Definitions[$nameSpace]);
            }
        }

        $psr4Definitions = array_merge_recursive($psr4Definitions, $newDefinitions);

        $payload->setFullPsr4Definitions($psr4Definitions);

        return $pipeline->handle($payload);
    }

    /**
     * Recursively iterate through all folders for given structure
     *
     * @param string $path
     *
     * @return array
     */
    private function getMatchingFolders(string $path): array
    {
        return glob($path, GLOB_BRACE | GLOB_ONLYDIR);
    }

    private function getRegexFromGlob($replacementPath): string
    {
        return '#' . str_replace(
            ['{', '}', '*', ','],
            ['(', ')', '.+', '|'],
            $replacementPath
        ) . '#';
    }
}
