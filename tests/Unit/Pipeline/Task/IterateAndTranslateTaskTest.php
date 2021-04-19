<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Pipeline\Task;

use Prophecy\PhpUnit\ProphecyTrait;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Pipeline;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\IterateAndTranslateTask;
use PHPUnit\Framework\TestCase;

class IterateAndTranslateTaskTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @dataProvider casesOfReplacement
     *
     * @param $expected
     * @param $definitions
     * @param $folder
     *
     * @return void
     */
    public function checkIfIterationAndTranslationWorksAsExpected($expected, $definitions, $folder, $wildCards)
    {
        $payload = $this->prophesize(Payload::class);
        $payload->setPsr4Definitions($expected)->shouldBeCalled()->hasReturnVoid();
        $payload->setDevPsr4Definitions($expected)->shouldBeCalled()->hasReturnVoid();

        $payload->getPsr4Definitions()->willReturn($definitions);
        $payload->getDevPsr4Definitions()->willReturn($definitions);

        $payload->getAdvancedWildcards()->willReturn($wildCards);
        $payload->getDevAdvancedWildcards()->willReturn($wildCards);

        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->handle($payload->reveal())->willReturn($payload->reveal());

        $function = function ($path) use ($folder) {
            return $folder[$path] ?? [];
        };



        $task = new IterateAndTranslateTask('path', $function);
        $task($payload->reveal(), $pipeline->reveal());
    }

    public function casesOfReplacement()
    {
        return [
            'find no folders' => [
                [
                    'Old\\Namespace\\' => 'folder'
                ],
                [
                    'Old\\Namespace\\' => 'folder',
                    'Any\\%s\\' => 'folder/{*}'
                ],
                [[]],
                [
                    'Any\\%s\\'
                ]
            ],
            'find folder according to namespace/folder' => [
                [
                    'Old\\Namespace\\' => 'folder',
                    'Any\\Mash\\' => ['folder/Mash'],
                    'Any\\Logic\\' => ['folder/Logic'],
                    'Any\\Content\\' => ['folder/Content'],
                    'Other\\Cheese\\Some\\Flower' => ['modules/Cheese/Some/Flower/src'],
                    'Other\\Cheese\\Some\\Tree' => ['modules/Cheese/Some/Tree/src'],
                    'Other\\Cake\\Some\\Three' => ['modules/Cake/Some/Three/src'],
                    'Other\\Cake\\Some\\Foreach' => ['modules/Cake/Some/Foreach/src'],
                ],
                [
                    'Old\\Namespace\\' => 'folder',
                    'Any\\%s\\' => 'folder/{*}',
                    'Other\\%s\\Some\\%s' => 'modules/{*}/Some/{*}/src'
                ],
                [
                    'folder/{*}' => [
                        'folder/Mash',
                        'folder/Logic',
                        'folder/Content'
                    ],
                    'modules/{*}/Some/{*}/src' => [
                        'modules/Cheese/Some/Flower/src',
                        'modules/Cheese/Some/Tree/src',
                        'modules/Cake/Some/Three/src',
                        'modules/Cake/Some/Foreach/src'
                    ]
                ],
                [
                    'Any\\%s\\',
                    'Other\\%s\\Some\\%s'
                ]
            ],
            'find folder with multiple folders' => [
                [
                    'Old\\Namespace\\' => 'folder',
                    'Any\\Mash\\' => ['folder/Mash', 'module/Mash'],
                    'Any\\Logic\\' => ['folder/Logic'],
                    'Any\\Other\\' => ['module/Other']
                ],
                [
                    'Old\\Namespace\\' => 'folder',
                    'Any\\%s\\' => [
                        'folder/{*}',
                        'module/{*}'
                    ]
                ],
                [
                    'folder/{*}' => [
                        'folder/Mash',
                        'folder/Logic'
                    ],
                    'module/{*}' => [
                        'module/Mash',
                        'module/Other'
                    ]
                ],
                [
                    'Any\\%s\\'
                ]
            ]
        ];
    }
}
