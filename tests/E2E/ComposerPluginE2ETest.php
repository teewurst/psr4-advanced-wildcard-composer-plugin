<?php

declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\E2E;

use PHPUnit\Framework\TestCase;

/**
 * E2E test: runs real Composer in tests/E2E/project (path repo to current repo root).
 * The plugin under test is the current working tree – i.e. the branch/commit you have checked out.
 *
 * Run only via: composer test:e2e  (or phpunit --testsuite E2E)
 * Not run with the default composer test.
 */
class ComposerPluginE2ETest extends TestCase
{
    private string $projectDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->projectDir = __DIR__ . DIRECTORY_SEPARATOR . 'project';
        if (!is_dir($this->projectDir) || !is_file($this->projectDir . DIRECTORY_SEPARATOR . 'composer.json')) {
            self::markTestSkipped('E2E project not found at ' . $this->projectDir);
        }
    }

    protected function tearDown(): void
    {
        $this->cleanupE2EProject();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function composer_install_runs_plugin_and_expands_file_wildcards_in_generated_autoload(): void
    {
        $cmd = sprintf(
            'composer install --no-interaction --no-progress --working-dir=%s 2>&1',
            escapeshellarg($this->projectDir)
        );
        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);
        $outputStr = implode("\n", $output);

        self::assertSame(0, $returnCode, 'Composer install should succeed: ' . $outputStr);

        $devJsonPath = $this->projectDir . DIRECTORY_SEPARATOR . 'composer.development.json';
        self::assertFileExists($devJsonPath, 'Plugin should generate composer.development.json');

        $data = json_decode(file_get_contents($devJsonPath), true);
        self::assertIsArray($data, 'composer.development.json must be valid JSON');

        $autoloadFiles = $data['autoload']['files'] ?? [];
        self::assertContains('app/Helpers/one.php', $autoloadFiles);
        self::assertContains('app/Helpers/two.php', $autoloadFiles);
        self::assertCount(2, $autoloadFiles);

        $autoloadDevFiles = $data['autoload-dev']['files'] ?? [];
        self::assertContains('tests/helpers/bootstrap_test.php', $autoloadDevFiles);
        self::assertContains('tests/helpers/helper_test.php', $autoloadDevFiles);
        self::assertCount(2, $autoloadDevFiles);

        $psr4 = $data['autoload']['psr-4'] ?? [];
        self::assertArrayHasKey('My\\Namespace\\DomainA\\ModuleX\\', $psr4, 'Generated autoload.psr-4 should contain expanded wildcard namespace DomainA\\ModuleX');
        self::assertArrayHasKey('My\\Namespace\\DomainB\\ModuleY\\', $psr4, 'Generated autoload.psr-4 should contain expanded wildcard namespace DomainB\\ModuleY');
        self::assertSame('src/module/DomainA/ModuleX/src', $psr4['My\\Namespace\\DomainA\\ModuleX\\'] ?? null);
        self::assertSame('src/module/DomainB/ModuleY/src', $psr4['My\\Namespace\\DomainB\\ModuleY\\'] ?? null);

        $psr4Dev = $data['autoload-dev']['psr-4'] ?? [];
        self::assertArrayHasKey('My\\Namespace\\Test\\Integration\\', $psr4Dev, 'Generated autoload-dev.psr-4 should contain expanded test namespace Integration');
        self::assertArrayHasKey('My\\Namespace\\Test\\Unit\\', $psr4Dev, 'Generated autoload-dev.psr-4 should contain expanded test namespace Unit');
        self::assertSame('test/Integration/tests', $psr4Dev['My\\Namespace\\Test\\Integration\\'] ?? null);
        self::assertSame('test/Unit/tests', $psr4Dev['My\\Namespace\\Test\\Unit\\'] ?? null);
    }

    private function cleanupE2EProject(): void
    {
        $vendorDir = $this->projectDir . DIRECTORY_SEPARATOR . 'vendor';
        if (is_dir($vendorDir)) {
            $this->removeDirectory($vendorDir);
        }
        $paths = [
            $this->projectDir . DIRECTORY_SEPARATOR . 'composer.development.json',
            $this->projectDir . DIRECTORY_SEPARATOR . 'composer.lock',
        ];
        foreach ($paths as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}
