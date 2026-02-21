<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Pipeline\Task;

use Prophecy\PhpUnit\ProphecyTrait;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\ExpandWildcardFilesTask;
use PHPUnit\Framework\TestCase;

class ExpandWildcardFilesTaskTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @dataProvider expansionCases
     *
     * @param array $expectedFiles
     * @param array $expectedDevFiles
     * @param array $inputFiles
     * @param array $inputDevFiles
     * @param array $globMap Mapping of wildcard path => expanded paths
     */
    public function checkIfWildcardFilesAreExpanded(
        array $expectedFiles,
        array $expectedDevFiles,
        array $inputFiles,
        array $inputDevFiles,
        array $globMap
    ) {
        $payload = new Payload();
        $payload->setFilesDefinitions($inputFiles);
        $payload->setDevFilesDefinitions($inputDevFiles);

        $globCallback = function (string $path) use ($globMap) {
            return $globMap[$path] ?? [];
        };

        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->handle(\Prophecy\Argument::type(Payload::class))->willReturn($payload);

        $task = new ExpandWildcardFilesTask('/project/root', $globCallback);
        $task($payload, $pipeline->reveal());

        self::assertSame($expectedFiles, $payload->getFilesDefinitions());
        self::assertSame($expectedDevFiles, $payload->getDevFilesDefinitions());
    }

    public function expansionCases(): array
    {
        return [
            'expand single wildcard pattern' => [
                ['app/Helpers/helper1.php', 'app/Helpers/helper2.php', 'app/Helpers/helper3.php'],
                [],
                ['app/Helpers/{*}.php'],
                [],
                [
                    'app/Helpers/{*}.php' => [
                        'app/Helpers/helper1.php',
                        'app/Helpers/helper2.php',
                        'app/Helpers/helper3.php'
                    ]
                ]
            ],
            'leave non-wildcard files unchanged' => [
                ['app/Helpers/explicit.php'],
                [],
                ['app/Helpers/explicit.php'],
                [],
                []
            ],
            'mix wildcard and explicit files' => [
                [
                    'app/Helpers/helper1.php',
                    'app/Helpers/helper2.php',
                    'app/Config/settings.php'
                ],
                [],
                ['app/Helpers/{*}.php', 'app/Config/settings.php'],
                [],
                [
                    'app/Helpers/{*}.php' => [
                        'app/Helpers/helper1.php',
                        'app/Helpers/helper2.php'
                    ]
                ]
            ],
            'expand dev files' => [
                [],
                ['tests/bootstrap.php', 'tests/helper1.php', 'tests/helper2.php'],
                [],
                ['tests/bootstrap.php', 'tests/{*}.php'],
                [
                    'tests/{*}.php' => ['tests/helper1.php', 'tests/helper2.php']
                ]
            ],
            'empty arrays remain empty' => [
                [],
                [],
                [],
                [],
                []
            ],
            'multiple wildcard patterns' => [
                [
                    'app/Helpers/helper1.php',
                    'app/Helpers/helper2.php',
                    'app/Config/prod.php',
                    'app/Config/dev.php'
                ],
                [],
                ['app/Helpers/{*}.php', 'app/Config/{*}.php'],
                [],
                [
                    'app/Helpers/{*}.php' => ['app/Helpers/helper1.php', 'app/Helpers/helper2.php'],
                    'app/Config/{*}.php' => ['app/Config/prod.php', 'app/Config/dev.php']
                ]
            ]
        ];
    }

    /**
     * @test
     */
    public function checkIfPipelineHandleIsCalled()
    {
        $payload = new Payload();
        $payload->setFilesDefinitions(['app/foo.php']);
        $payload->setDevFilesDefinitions([]);

        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->handle(\Prophecy\Argument::type(Payload::class))
            ->shouldBeCalled()
            ->willReturn($payload);

        $task = new ExpandWildcardFilesTask('/project', function () {
            return [];
        });
        $task($payload, $pipeline->reveal());
    }

    /**
     * @test
     */
    public function checkIfDefaultGlobCallbackExpandsWildcardsWithRealFiles()
    {
        $tempDir = sys_get_temp_dir() . '/expand-wildcard-test-' . uniqid();
        $helpersDir = $tempDir . DIRECTORY_SEPARATOR . 'Helpers';
        mkdir($helpersDir, 0755, true);
        file_put_contents($helpersDir . DIRECTORY_SEPARATOR . 'helper1.php', '<?php');
        file_put_contents($helpersDir . DIRECTORY_SEPARATOR . 'helper2.php', '<?php');

        try {
            $payload = new Payload();
            $payload->setFilesDefinitions(['Helpers/{*}.php']);
            $payload->setDevFilesDefinitions([]);

            $pipeline = $this->prophesize(Pipeline::class);
            $pipeline->handle(\Prophecy\Argument::type(Payload::class))->willReturn($payload);

            $task = new ExpandWildcardFilesTask($tempDir, null);
            $task($payload, $pipeline->reveal());

            $files = $payload->getFilesDefinitions();
            self::assertCount(2, $files);
            self::assertContains('Helpers/helper1.php', $files);
            self::assertContains('Helpers/helper2.php', $files);
        } finally {
            @unlink($helpersDir . DIRECTORY_SEPARATOR . 'helper1.php');
            @unlink($helpersDir . DIRECTORY_SEPARATOR . 'helper2.php');
            @rmdir($helpersDir);
            @rmdir($tempDir);
        }
    }

    /**
     * @test
     */
    public function checkIfDefaultGlobCallbackReturnsEmptyWhenNoMatches()
    {
        $tempDir = sys_get_temp_dir() . '/expand-wildcard-test-' . uniqid();
        mkdir($tempDir, 0755, true);

        try {
            $payload = new Payload();
            $payload->setFilesDefinitions(['NonExistent/{*}.php']);
            $payload->setDevFilesDefinitions([]);

            $pipeline = $this->prophesize(Pipeline::class);
            $pipeline->handle(\Prophecy\Argument::type(Payload::class))->willReturn($payload);

            $task = new ExpandWildcardFilesTask($tempDir, null);
            $task($payload, $pipeline->reveal());

            self::assertSame([], $payload->getFilesDefinitions());
        } finally {
            @rmdir($tempDir);
        }
    }

    /**
     * @test
     * Covers the default glob callback path when a wildcard pattern matches no files
     */
    public function checkIfDefaultGlobCallbackReturnsEmptyWhenGlobReturnsFalse()
    {
        $tempDir = sys_get_temp_dir() . '/expand-wildcard-test-' . uniqid();
        mkdir($tempDir, 0755, true);

        try {
            $payload = new Payload();
            $payload->setDevFilesDefinitions([]);
            $payload->setFilesDefinitions(['{nonexistent_dir_abc123}/*.php']);

            $pipeline = $this->prophesize(Pipeline::class);
            $pipeline->handle(\Prophecy\Argument::type(Payload::class))->willReturn($payload);

            $task = new ExpandWildcardFilesTask($tempDir, null);
            $task($payload, $pipeline->reveal());

            $files = $payload->getFilesDefinitions();
            self::assertIsArray($files);
            self::assertEmpty($files);
        } finally {
            @rmdir($tempDir);
        }
    }
}
