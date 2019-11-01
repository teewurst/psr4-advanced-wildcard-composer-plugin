<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\FileAccessor;

use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\ComposerDevelopmentJson;
use PHPUnit\Framework\TestCase;

class ComposerDevelopmentJsonTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        @unlink(__DIR__ . '/files/composer.development.json');
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        @unlink(__DIR__ . '/files/composer.development.json');
        parent::tearDownAfterClass();
    }

    /**
     * @test
     * @return void
     */
    public function checkIfCreatesCopyOfComposerJsonFileButWithOwnPsr4Definitions()
    {
        $composerDev = new ComposerDevelopmentJson(__DIR__ . '/files/vendor');

        $composerDev->setDefinitons([
            '\\New\\NameSpace' => 'best/file/ever'
        ]);
        $composerDev->persist();

        $fileContents = json_decode(file_get_contents(__DIR__ . '/files/composer.development.json'), true);
        self::assertSame('project', $fileContents['type'] ?? '');
        self::assertSame('best/file/ever', $fileContents['autoload']['psr-4']['\\New\\NameSpace'] ?? '');
    }
}
