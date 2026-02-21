<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task;

use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;

/**
 * Class ExpandWildcardFilesTask
 *
 * Expands wildcard patterns in autoload files (e.g. "app/Helpers/{*}.php") to actual file paths
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task
 */
class ExpandWildcardFilesTask implements TaskInterface
{
    /** @var string */
    private $projectRoot;
    /** @var ?callable */
    private $globCallback;

    /**
     * ExpandWildcardFilesTask constructor.
     *
     * @param string        $projectRoot  Path to project root (parent of vendor dir)
     * @param callable|null $globCallback Optional callback for glob (path) => array of matching paths
     */
    public function __construct(string $projectRoot, ?callable $globCallback = null)
    {
        $this->projectRoot = rtrim($projectRoot, '/\\');
        if ($globCallback === null) {
            $globCallback = function (string $path): array {
                $fullPath = str_replace(
                    ['/', '\\'],
                    DIRECTORY_SEPARATOR,
                    $this->projectRoot . DIRECTORY_SEPARATOR . $path
                );
                $matches = glob($fullPath, GLOB_BRACE);
                if ($matches === false) { // @codeCoverageIgnore
                    return []; // @codeCoverageIgnore
                }
                $result = [];
                foreach ($matches as $match) {
                    if (is_file($match)) {
                        $relative = str_replace($this->projectRoot . DIRECTORY_SEPARATOR, '', $match);
                        $result[] = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
                    }
                }
                return $result;
            };
        }
        $this->globCallback = $globCallback;
    }

    /**
     * Expands wildcard patterns in files arrays
     *
     * @param Payload  $payload
     * @param Pipeline $pipeline
     *
     * @return Payload
     */
    public function __invoke(Payload $payload, Pipeline $pipeline): Payload
    {
        $payload->setFilesDefinitions($this->expandFiles($payload->getFilesDefinitions()));
        $payload->setDevFilesDefinitions($this->expandFiles($payload->getDevFilesDefinitions()));

        return $pipeline->handle($payload);
    }

    /**
     * Expands wildcard patterns in a files array
     *
     * @param array $files
     * @return array
     */
    private function expandFiles(array $files): array
    {
        $result = [];
        foreach ($files as $file) {
            if (strpos($file, '{') !== false) {
                $expanded = ($this->globCallback)($file);
                $result = array_merge($result, $expanded);
            } else {
                $result[] = $file;
            }
        }
        return array_values(array_unique($result));
    }
}
