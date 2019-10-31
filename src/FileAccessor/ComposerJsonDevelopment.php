<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\FileAccessor;

/**
 * Class ComposerJsonDevelopment
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\FileAccessor
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class ComposerJsonDevelopment
{

    private const DEFAULT_PATH_TO_COMPOSER_JSON_FILE = '../composer.json';
    private const DEFAULT_PATH_TO_COMPOSER_DEVELOPMENT_JSON_FILE = '../composer.development.json';

    /** @var string */
    private $vendorPath;
    /** @var array */
    private $psr4Definitions;
    /** @var string */
    private $relative_composer_json_path;
    /** @var string */
    private $relative_composer_development_json_path;

    /**
     * ComposerJsonDevelopment constructor.
     *
     * @param string $vendorPath                              Path to vendor folder
     * @param string $relative_composer_json_path             Relative Path from vendor to composer.json (unit test)
     * @param string $relative_composer_development_json_path Relative Path to new composer.development.json (unit test)
     */
    public function __construct(
        string $vendorPath,
        string $relative_composer_json_path = self::DEFAULT_PATH_TO_COMPOSER_JSON_FILE,
        string $relative_composer_development_json_path = self::DEFAULT_PATH_TO_COMPOSER_DEVELOPMENT_JSON_FILE
    ) {
        $this->vendorPath = ltrim($vendorPath, ['/', '\\']);
        $this->relative_composer_json_path = $relative_composer_json_path;
        $this->relative_composer_development_json_path = $relative_composer_development_json_path;
    }

    public function setDefinitons(array $psr4Definitions)
    {
        $this->psr4Definitions = $psr4Definitions;
    }

    public function persist()
    {
        $contents = json_decode(
            file_get_contents(
                $this->vendorPath . DIRECTORY_SEPARATOR . $this->relative_composer_json_path
            ),
            true
        );
        $contents['autoload']['psr-4'] = $this->psr4Definitions;

        file_put_contents(
            $this->vendorPath . DIRECTORY_SEPARATOR . $this->relative_composer_development_json_path,
            json_encode(
                $contents,
                JSON_PRETTY_PRINT
            )
        );
    }
}
