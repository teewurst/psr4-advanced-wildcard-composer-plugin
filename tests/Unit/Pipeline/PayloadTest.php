<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Pipeline;

use Prophecy\PhpUnit\ProphecyTrait;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Payload;
use PHPUnit\Framework\TestCase;

/**
 * Class PayloadTest
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\tests\Unit\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class PayloadTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @return void
     */
    public function checkIfGetterAndSetterWorkProperly()
    {
        $payload = new Payload();

        self::assertSame([], $payload->getAdvancedWildcards());
        self::assertSame([], $payload->getDevAdvancedWildcards());
        self::assertSame([], $payload->getDevPsr4Definitions());
        self::assertSame([], $payload->getPsr4Definitions());
        self::assertSame([], $payload->getFilesDefinitions());
        self::assertSame([], $payload->getDevFilesDefinitions());

        $array = ['some'];

        $payload->setAdvancedWildcards($array);
        $payload->setDevAdvancedWildcards($array);
        $payload->setDevPsr4Definitions($array);
        $payload->setPsr4Definitions($array);
        $payload->setFilesDefinitions($array);
        $payload->setDevFilesDefinitions($array);

        self::assertSame($array, $payload->getAdvancedWildcards());
        self::assertSame($array, $payload->getDevAdvancedWildcards());
        self::assertSame($array, $payload->getDevPsr4Definitions());
        self::assertSame($array, $payload->getPsr4Definitions());
        self::assertSame($array, $payload->getFilesDefinitions());
        self::assertSame($array, $payload->getDevFilesDefinitions());
    }
}
