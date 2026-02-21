<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task;

use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;

/**
 * Class FilterForRegexKeys
 *
 * Read all Keys and filter those without advanced wildcards
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class FilterAndValidateWildcardsTask implements TaskInterface
{

    /**
     * Read all Keys and filter those without advanced wildcards
     *
     * @param Payload  $payload
     * @param Pipeline $pipeline
     *
     * @return Payload
     */
    public function __invoke(Payload $payload, Pipeline $pipeline): Payload
    {
        $psr4Definitions = $payload->getPsr4Definitions();
        $advancedWildcards = $this->findWildcards($psr4Definitions);

        $devPsr4Definitions = $payload->getDevPsr4Definitions();
        $devAdvancedWildcards = $this->findWildcards($devPsr4Definitions);

        $hasFileWildcards = $this->hasFileWildcards($payload->getFilesDefinitions())
            || $this->hasFileWildcards($payload->getDevFilesDefinitions());

        // if empty interrupt pipe (no psr-4 wildcards and no file wildcards)
        if (!$devAdvancedWildcards && !$advancedWildcards && !$hasFileWildcards) {
            return $payload;
        }

        $payload->setAdvancedWildcards($advancedWildcards);
        $payload->setDevAdvancedWildcards($devAdvancedWildcards);

        return $pipeline->handle($payload);
    }

    /**
     * @param string $className
     *
     * @return int
     */
    private function countStringReplacements(string $className): int
    {
        return preg_match_all('/%s|%\d\$s/', $className, $nameMatches);
    }

    private function validate(string $folder, int $nameMatches): bool
    {
        $count = preg_match_all('/{[^}]*}/', $folder);
        return $count === $nameMatches;
    }

    /**
     * @param array $psr4Definitions
     *
     * @return array
     */
    private function findWildcards(array $psr4Definitions): array
    {
        $advancedWildcards = [];

        foreach ($psr4Definitions as $nameSpace => $folders) {

            // check if there are any advanced wildcards
            if (!$countStringReplacements = $this->countStringReplacements($nameSpace)) {
                continue;
            }

            if (!is_array($folders)) {
                $folders = [$folders];
            }

            // check each folder of namespace, if it fits the replacement count
            foreach ($folders as $folder) {

                if (!$this->validate($folder, $countStringReplacements)) {
                    // ignored something because invalid
                    continue 2;
                }
            }

            // MESSAGE Found something
            $advancedWildcards[] = $nameSpace;
        }

        return $advancedWildcards;
    }

    /**
     * Check if any file path contains wildcard pattern
     *
     * @param array $files
     * @return bool
     */
    private function hasFileWildcards(array $files): bool
    {
        foreach ($files as $file) {
            if (strpos($file, '{') !== false) {
                return true;
            }
        }
        return false;
    }
}
