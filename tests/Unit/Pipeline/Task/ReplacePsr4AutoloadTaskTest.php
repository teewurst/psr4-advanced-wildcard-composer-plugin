<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Pipeline\Task;

use Composer\Composer;
use Composer\Package\RootPackage;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\ReplacePsr4AutoloadTask;
use PHPUnit\Framework\TestCase;

class ReplacePsr4AutoloadTaskTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function checkIfContentIsReplacedCorrectly()
    {
        $autoload = [
            'psr-0' => ['key' => 'value'],
            'psr-4' => ['Old\\Namespace\\' => 'files']
        ];

        $replacementArray = [
            'New\\Namespace\\' => 'content'
        ];

        $targetArray = [
            'psr-0' => ['key' => 'value'],
            'psr-4' => ['New\\Namespace\\' => 'content']
        ];

        $package = $this->prophesize(RootPackage::class);
        $package->getAutoload()->willReturn($autoload);
        $package->getDevAutoload()->willReturn($autoload);
        $package->setAutoload($targetArray)->shouldBeCalled();
        $package->setDevAutoload($targetArray)->shouldBeCalled();

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($package->reveal());

        $payload = $this->prophesize(Payload::class);
        $payload->getPsr4Definitions()->willReturn($replacementArray);
        $payload->getDevPsr4Definitions()->willReturn($replacementArray);

        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->handle($payload->reveal())->willReturn($payload->reveal());

        $task = new ReplacePsr4AutoloadTask($composer->reveal());
        $task($payload->reveal(), $pipeline->reveal());
    }
}
