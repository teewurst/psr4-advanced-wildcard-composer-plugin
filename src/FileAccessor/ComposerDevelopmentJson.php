<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\FileAccessor;

use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;

/**
 * Class ComposerJsonDevelopment
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\FileAccessor
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class ComposerDevelopmentJson
{

    const DEFAULT_PATH_TO_COMPOSER_JSON_FILE = '../composer.json';
    const DEFAULT_PATH_TO_COMPOSER_DEVELOPMENT_JSON_FILE = '../composer.development.json';

    /** @var string */
    private $vendorPath;
    /** @var Payload */
    private $payload;
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
        $this->vendorPath = rtrim($vendorPath, '/\\');
        $this->relative_composer_json_path = $relative_composer_json_path;
        $this->relative_composer_development_json_path = $relative_composer_development_json_path;
    }

    /**
     * Adds payload to be persisted
     *
     * @param Payload $payload
     *
     * @return void
     */
    public function setPayload(Payload $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Copys json file and replaces psr-4 contents
     *
     * @return void
     */
    public function persist()
    {
        $contents = json_decode(
            file_get_contents(
                $this->vendorPath . DIRECTORY_SEPARATOR . $this->relative_composer_json_path
            ),
            true
        );
        $contents['autoload']['psr-4'] = $this->cleanPSR4Definitions($this->payload->getPsr4Definitions());
        $contents['autoload-dev']['psr-4'] = $this->cleanPSR4Definitions($this->payload->getDevPsr4Definitions());
        $contents['autoload']['files'] = $this->payload->getFilesDefinitions();
        $contents['autoload-dev']['files'] = $this->payload->getDevFilesDefinitions();

        file_put_contents(
            $this->vendorPath . DIRECTORY_SEPARATOR . $this->relative_composer_development_json_path,
            json_encode(
                $contents,
                JSON_PRETTY_PRINT
            )
        );
    }

    private function cleanPSR4Definitions(array $psr4Definitions): array
    {
        array_walk_recursive(
            $psr4Definitions,
            function (&$value) {
                $rootPath = dirname($this->vendorPath);
                $value = str_replace($rootPath, '', $value);
                $value = trim($value, '\/');
            }
        );

        array_walk(
            $psr4Definitions,
            function (&$value) {
                if (is_array($value) && count($value) === 1) {
                    $value = $value[0];
                }
            }
        );

        return $psr4Definitions;
    }
}
