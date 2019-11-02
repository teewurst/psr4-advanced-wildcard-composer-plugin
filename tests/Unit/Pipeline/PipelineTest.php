<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Pipeline;

use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;
use PHPUnit\Framework\TestCase;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\TaskInterface;

/**
 * Class PipelineTest
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class PipelineTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function checkIfPipelineSimplyReturnPayloadIfEmpty()
    {
        $payload = $this->prophesize(Payload::class);

        $pipeline = new Pipeline();
        $return = $pipeline->handle($payload->reveal());

        self::assertSame($payload->reveal(), $return);
    }

    /**
     * @test
     * @return void
     */
    public function checkIfPipelineExecutesHandle()
    {
        $payload = $this->prophesize(Payload::class);

        $pipeline = new Pipeline();

        $task = $this->prophesize(TaskInterface::class);
        $task->__invoke($payload->reveal(), $pipeline)
             ->shouldBeCalledTimes(2)
             ->will(function ($args) {
                 return $args[1]->handle($args[0]);
             });

        $pipeline->pipe($task->reveal());
        $pipeline->pipe($task->reveal());

        $return = $pipeline->handle($payload->reveal());

        self::assertSame($payload->reveal(), $return);
    }
}
