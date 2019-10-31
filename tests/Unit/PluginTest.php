<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\tests\Unit;

use PHPUnit\Framework\TestCase;
use teewurst\Prs4AdvancedWildcardComposer\Plugin;

class PluginTest extends TestCase
{

    public function checkIfThePluginHooksIntoAfterDumpAutoload()
    {
        $hooks = Plugin::getSubscribedEvents();

        self::assertSame(
            'translateAdvancedHooks', $hooks['post-autoload-dump']
        );
    }
}
