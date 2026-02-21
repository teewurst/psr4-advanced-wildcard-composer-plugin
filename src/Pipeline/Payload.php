<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline;

/**
 * Class Payload
 *
 * Payload to be passed though the handlers of Pipeline
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class Payload
{

    /** @var array */
    private $par4Definitions = [];
    /** @var array */
    private $devPar4Definitions = [];
    /** @var array */
    private $filesDefinitions = [];
    /** @var array */
    private $devFilesDefinitions = [];
    /** @var array */
    private $advancedWildcards = [];
    /** @var array */
    private $devAdvancedWildcards = [];

    /**
     * @param array $psr4Definitions
     * @return void
     */
    public function setPsr4Definitions(array $psr4Definitions): void
    {
        $this->par4Definitions = $psr4Definitions;
    }

    /**
     * Contains all current psr4 definitions
     *
     * @return array
     */
    public function getPsr4Definitions(): array
    {
        return $this->par4Definitions;
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

    /**
     * @param array $filesDefinitions
     * @return void
     */
    public function setFilesDefinitions(array $filesDefinitions): void
    {
        $this->filesDefinitions = $filesDefinitions;
    }

    /**
     * Returns all file autoload definitions
     *
     * @return array
     */
    public function getFilesDefinitions(): array
    {
        return $this->filesDefinitions;
    }

    /**
     * @param array $devFilesDefinitions
     * @return void
     */
    public function setDevFilesDefinitions(array $devFilesDefinitions): void
    {
        $this->devFilesDefinitions = $devFilesDefinitions;
    }

    /**
     * Returns all dev file autoload definitions
     *
     * @return array
     */
    public function getDevFilesDefinitions(): array
    {
        return $this->devFilesDefinitions;
    }

    /**
     * @param array $devPar4Definitions
     * @return void
     */
    public function setDevPsr4Definitions(array $devPar4Definitions): void
    {
        $this->devPar4Definitions = $devPar4Definitions;
    }

    /**
     * @return array
     */
    public function getDevPsr4Definitions(): array
    {
        return $this->devPar4Definitions;
    }

    /**
     * @param array $devAdvancedWildcards
     * @return void
     */
    public function setDevAdvancedWildcards(array $devAdvancedWildcards): void
    {
        $this->devAdvancedWildcards = $devAdvancedWildcards;
    }

    /**
     * Returns field DevAdvancedWildcards
     *
     * @return array
     */
    public function getDevAdvancedWildcards(): array
    {
        return $this->devAdvancedWildcards;
    }
}
