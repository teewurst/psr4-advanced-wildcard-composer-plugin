<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Pipeline\Task;

use Prophecy\Argument;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\FilterAndValidateWildcardsTask;
use PHPUnit\Framework\TestCase;

class FilterAndValidateWildcardsTaskTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function checkIfValidDefinitionsAreRecognizedCorrectly()
    {
        $definitionsArray = [
            'Namespace\\Content\\' => 'file/a',
            'Namespace\\%s\\'      => 'file/{*}/a',
            'Namespace\\%s\\%s'    => 'file/{*}/a',
            'Namespace\\%s\\a'     => 'file/{*}/{*}/a',
            'AMCE\\%s'             => ['file/{*}/a', 'file/{*}/b'],
            'AMCE\\%s\\a'          => ['file/{*}/{*}/a', 'file/{*}/b'],
            'AMCE\\%s\\%s\\a'      => ['file/{*}/{*}/a', 'file/{*}/b']
        ];

        $filteredDefinitions = [
            'Namespace\\%s\\',
            'AMCE\\%s'
        ];

        $payload = $this->prophesize(Payload::class);
        $payload->getPsr4Definitions()->willReturn($definitionsArray);
        $payload->getDevPsr4Definitions()->willReturn($definitionsArray);

        $payload->setAdvancedWildcards($filteredDefinitions)->shouldBeCalled()->hasReturnVoid();
        $payload->setDevAdvancedWildcards($filteredDefinitions)->shouldBeCalled()->hasReturnVoid();

        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->handle($payload->reveal())->willReturn($payload->reveal())->shouldBeCalled();

        $task = new FilterAndValidateWildcardsTask();
        $task($payload->reveal(), $pipeline->reveal());
    }

    /**
     * @test
     * @return void
     */
    public function checkIfEmptyResultLeadsToInterruption()
    {
        $definitionsArray = [
            'Namespace\\Content\\' => 'file/a',
            'Namespace\\%s\\%s'    => 'file/{*}/a',
            'Namespace\\%s\\a'     => 'file/{*}/{*}/a',
            'AMCE\\%s\\a'          => ['file/{*}/{*}/a', 'file/{*}/b'],
            'AMCE\\%s\\%s\\a'      => ['file/{*}/{*}/a', 'file/{*}/b']
        ];

        $payload = $this->prophesize(Payload::class);
        $payload->getPsr4Definitions()->willReturn($definitionsArray);
        $payload->getDevPsr4Definitions()->willReturn($definitionsArray);

        /** @noinspection PhpParamsInspection */
        $payload->setAdvancedWildcards(Argument::any())->shouldNotBeCalled();
        /** @noinspection PhpParamsInspection */
        $payload->setDevAdvancedWildcards(Argument::any())->shouldNotBeCalled();

        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->handle($payload->reveal())->shouldNotBeCalled();

        $task = new FilterAndValidateWildcardsTask();
        $task($payload->reveal(), $pipeline->reveal());
    }
}
