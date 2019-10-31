<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline;

/**
 * Class Payload
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class Payload
{

    /** @var array */
    private $fullPsr4Definitons;
    /** @var array */
    private $advancedWildcards;

    /**
     * @param array $psr4Definitions
     * @return void
     */
    public function setFullPsr4Definitions(array $psr4Definitions): void
    {
        $this->fullPsr4Definitons = $psr4Definitions;
    }

    /**
     * Contains all current psr4 definitions
     *
     * @return array
     */
    public function getFullPsr4Definitions(): array
    {
        return $this->fullPsr4Definitons;
    }

    /**
     * @param array $advancedWildcards
     * @return void
     */
    public function setAdvancedWildcards(array $advancedWildcards): void
    {
        $this->advancedWildcards = $advancedWildcards;
    }

    /**
     * Returns field AdvancedWildcards
     *
     * @return array
     */
    public function getAdvancedWildcards(): array
    {
        return $this->advancedWildcards;
    }
}
