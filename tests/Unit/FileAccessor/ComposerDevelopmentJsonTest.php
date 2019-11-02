<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\FileAccessor;

use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\ComposerDevelopmentJson;
use PHPUnit\Framework\TestCase;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;

/**
 * Class ComposerDevelopmentJsonTest
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\tests\Unit\FileAccessor
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
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
        $definitionsArray = [
            '\\New\\NameSpace' => 'best/file/ever'
        ];

        $payload = $this->prophesize(Payload::class);
        $payload->getPsr4Definitions()->willReturn($definitionsArray);
        $payload->getDevPsr4Definitions()->willReturn($definitionsArray);

        $composerDev = new ComposerDevelopmentJson(__DIR__ . '/files/vendor');
        $composerDev->setPayload($payload->reveal());
        $composerDev->persist();

        $fileContents = json_decode(file_get_contents(__DIR__ . '/files/composer.development.json'), true);
        self::assertSame('project', $fileContents['type'] ?? '');
        self::assertSame('best/file/ever', $fileContents['autoload']['psr-4']['\\New\\NameSpace'] ?? '');
        self::assertSame('best/file/ever', $fileContents['autoload-dev']['psr-4']['\\New\\NameSpace'] ?? '');
    }
}
