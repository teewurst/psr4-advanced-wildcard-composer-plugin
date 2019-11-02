<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task;

use Composer\Composer;
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

    /** @var callable */
    private $globCallback;

    /**
     * IterateAndTranslateTask constructor.
     *
     * @param string        $vendorDir
     * @param callable|null $globCallback
     */
    public function __construct(string $vendorDir, callable $globCallback = null)
    {
        if ($globCallback === null) {
            $globCallback = function ($path) use ($vendorDir) {
                $pattern = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, dirname($vendorDir) . DIRECTORY_SEPARATOR . $path);
                return glob($pattern, GLOB_BRACE | GLOB_ONLYDIR);
            };
        }

        $this->globCallback = $globCallback;
    }

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
        $psr4Definitions = $payload->getPsr4Definitions();

        $psr4Definitions = $this->replaceNamespaces($advancedWildcards, $psr4Definitions);
        $payload->setPsr4Definitions($psr4Definitions);

        $advancedWildcards = $payload->getDevAdvancedWildcards();
        $psr4Definitions = $payload->getDevPsr4Definitions();

        $psr4Definitions = $this->replaceNamespaces($advancedWildcards, $psr4Definitions);
        $payload->setDevPsr4Definitions($psr4Definitions);

        return $pipeline->handle($payload);
    }

    /**
     * Uses callback to generate matching folders
     *
     * @param string $path
     *
     * @return array
     */
    private function getMatchingFolders(string $path): ?array
    {
        return ($this->globCallback)($path);
    }

    /**
     * Translate a glob pattern to regex pattern
     *
     * @param string $replacementPath
     * @return string
     */
    private function getRegexFromGlob(string $replacementPath): string
    {
        return '#' . str_replace(
            ['{', '}', '*', ','],
            ['(', ')', '.+', '|'],
            $replacementPath
        ) . '#';
    }

    /**
     * Uses Wildcards to find folders and replace filler of folder and namespace
     *
     * @param array $advancedWildcards
     * @param array $psr4Definitions
     *
     * @return array
     */
    private function replaceNamespaces(array $advancedWildcards, array $psr4Definitions): array {
        $newDefinitions = [];
        foreach ($advancedWildcards as $nameSpace) {
            $replacementPaths = $psr4Definitions[$nameSpace];

            if (!is_array($replacementPaths)) {
                $replacementPaths = [$replacementPaths];
            }

            foreach ($replacementPaths as $replacementPath) {
                $matchingFolders = $this->getMatchingFolders($replacementPath);
                foreach ($matchingFolders as $folder) {
                    // get regex to read values from $folder
                    $pattern = $this->getRegexFromGlob($replacementPath);

                    // read values from path
                    preg_match_all(
                        str_replace('\\', '/', $pattern),
                        str_replace('\\', '/', $folder),
                        $matches
                    );

                    // remove full match
                    unset($matches[0]);
                    $matches = array_merge(...$matches);

                    // fill namespace and add path
                    $newDefinitions[sprintf($nameSpace, ...$matches)][] = $folder;
                }
            }

            unset($psr4Definitions[$nameSpace]);
        }

        $psr4Definitions = array_merge_recursive($psr4Definitions, $newDefinitions);

        return $psr4Definitions;
    }
}
