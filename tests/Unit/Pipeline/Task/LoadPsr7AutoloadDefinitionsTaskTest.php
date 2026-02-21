<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Pipeline\Task;

use Composer\Composer;
use Composer\Package\RootPackage;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\LoadPsr7AutoloadDefinitionsTask;
use PHPUnit\Framework\TestCase;
use teewurst\Prs4AdvancedWildcardComposer\Plugin;

class LoadPsr7AutoloadDefinitionsTaskTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @return void
     */
    public function checkIfTaskLoadsDefinitionsIntoPayload()
    {
        $array = [
            'psr-4' => [
                'Namespace' => 'files'
            ]
        ];

        $extra = [
            Plugin::NAME => [
                'autoload-dev' => [
                    'psr-4' => [
                        'My' => 'folder'
                    ]
                ],
                'autoload' => [
                    'psr-4' => [
                        'My' => 'folder'
                    ]
                ],
            ]
        ];

        $expected = [
            'My' => 'folder',
            'Namespace' => 'files'
        ];

        $arrayWithFiles = array_merge($array, ['files' => []]);
        $package = $this->prophesize(RootPackage::class);
        $package->getDevAutoload()->willReturn($arrayWithFiles);
        $package->getAutoload()->willReturn($arrayWithFiles);
        $package->getExtra()->willReturn($extra);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($package->reveal());

        $arrayWithFiles = [
            'psr-4' => [
                'Namespace' => 'files'
            ],
            'files' => []
        ];

        $payload = $this->prophesize(Payload::class);
        $payload->setPsr4Definitions($expected);
        $payload->setDevPsr4Definitions($expected);
        $payload->setFilesDefinitions([]);
        $payload->setDevFilesDefinitions([]);

        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->handle($payload->reveal())->shouldBeCalled()->willReturn($payload->reveal());

        $task = new LoadPsr7AutoloadDefinitionsTask($composer->reveal());
        $task($payload->reveal(), $pipeline->reveal());
    }

    /**
     * @test
     * @return void
     */
    public function checkIfInterruptPipelineOnEmptyPsr4Definition()
    {
        $package = $this->prophesize(RootPackage::class);
        $package->getExtra()->willReturn([]);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($package->reveal());

        $payload = $this->prophesize(Payload::class);
        $payload->setPsr4Definitions(Argument::any())->shouldNotBeCalled();
        $payload->setDevPsr4Definitions(Argument::any())->shouldNotBeCalled();
        $payload->setFilesDefinitions(Argument::any())->shouldNotBeCalled();
        $payload->setDevFilesDefinitions(Argument::any())->shouldNotBeCalled();

        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->handle($payload->reveal())->shouldNotBeCalled()->willReturn($payload->reveal());

        $task = new LoadPsr7AutoloadDefinitionsTask($composer->reveal());
        $task($payload->reveal(), $pipeline->reveal());
    }
}
