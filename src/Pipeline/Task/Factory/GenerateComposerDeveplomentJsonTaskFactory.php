<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory;

use Composer\Script\Event;
use teewurst\Prs4AdvancedWildcardComposer\Di\Container;
use teewurst\Prs4AdvancedWildcardComposer\Di\FactoryInterface;
use teewurst\Prs4AdvancedWildcardComposer\FileAccessor\ComposerDevelopmentJson;
use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\GenerateComposerDeveplomentJsonTask;

/**
 * Class GenerateComposerDeveplomentJsonTaskFactory
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\Factory
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class GenerateComposerDeveplomentJsonTaskFactory implements FactoryInterface
{

    /**
     * Creates an Instance of GenerateComposerDeveplomentJsonTask
     *
     * @param Container $container
     * @param string    $name
     *
     * @return GenerateComposerDeveplomentJsonTask
     */
    public function __invoke(Container $container, string $name): object
    {
        /** @var Event $event */
        $event = $container->get(Event::class);
        return new GenerateComposerDeveplomentJsonTask(
            $container->get(ComposerDevelopmentJson::class),
            $event->isDevMode()
        );
    }
}
