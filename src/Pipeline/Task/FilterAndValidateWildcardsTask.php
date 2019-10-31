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
        $psr4Definitions = $payload->getFullPsr4Definitions();
        $advancedWildcards = [];

        foreach ($psr4Definitions as $nameSpace => $folders) {

            // check if there are any advanced wildcards
            if (!$countStringReplacements = $this->countStringReplacements($nameSpace)) {
                continue;
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

        // if empty interrupt pipe
        if (count($advancedWildcards) === 0) {
            return $payload;
        }

        $payload->setAdvancedWildcards($advancedWildcards);
        $payload->setFullPsr4Definitions($psr4Definitions);

        return $pipeline->handle($payload);
    }

    /**
     * @param $className
     *
     * @return int
     */
    private function countStringReplacements($className): int
    {
        return preg_match_all('/%s|%\d\$s/', $className, $nameMatches);
    }

    private function validate(string $folder, int $nameMatches): bool
    {
        $count = preg_match_all('/{[^}]*}/', $folder);
        return $count === $nameMatches;
    }
}
