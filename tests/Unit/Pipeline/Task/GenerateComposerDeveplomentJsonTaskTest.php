<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Pipeline\Task;

use Prophecy\Argument;
use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\ComposerDevelopmentJson;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\GenerateComposerDeveplomentJsonTask;
use PHPUnit\Framework\TestCase;

class GenerateComposerDeveplomentJsonTaskTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function checkIfHandlerIsCalledIfNoDevelopmentMode()
    {
        $composerFileAccessor = $this->prophesize(ComposerDevelopmentJson::class);
        $composerFileAccessor->setDefinitons(Argument::any())->shouldNotBeCalled();
        $composerFileAccessor->persist()->shouldNotBeCalled();

        $payload = $this->prophesize(Payload::class);
        $payload->getPsr4Definitions()->shouldNotBeCalled();

        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->handle($payload->reveal())->willReturn($payload->reveal());

        $task = new GenerateComposerDeveplomentJsonTask($composerFileAccessor->reveal(), false);
        $task($payload->reveal(), $pipeline->reveal());
    }

    /**
     * @test
     * @return void
     */
    public function checkIfFileIsCreatedOfDelevelopmentMode()
    {
        $definitions = [];

        $composerFileAccessor = $this->prophesize(ComposerDevelopmentJson::class);
        $composerFileAccessor->setDefinitons($definitions)->shouldBeCalled()->hasReturnVoid();
        $composerFileAccessor->persist()->shouldBeCalled()->hasReturnVoid();

        $payload = $this->prophesize(Payload::class);
        $payload->getPsr4Definitions()->willReturn($definitions);

        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->handle($payload->reveal())->willReturn($payload->reveal());

        $task = new GenerateComposerDeveplomentJsonTask($composerFileAccessor->reveal(), true);
        $task($payload->reveal(), $pipeline->reveal());
    }
}
