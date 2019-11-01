<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Pipeline;

use teewurst\Prs4AdvancedWildcardComposer\Pipeline\Task\TaskInterface;

/**
 * Class Pipeline
 *
 * Works through multiple tasks, which are setup in Plugin.php
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class Pipeline
{
    /** @var TaskInterface[] */
    private $tasks = [];

    /**
     * Adds Tasks to worker
     *
     * @param TaskInterface $task
     * @return void
     */
    public function pipe(TaskInterface $task)
    {
        $this->tasks[] = $task;
    }

    /**
     * Starts handling of pipeline
     *
     * @param Payload  $payload
     * @return Payload
     */
    public function handle(Payload $payload)
    {
        $task = $this->next();

        if ($task === null) {
            return $payload;
        }

        return $task($payload, $this);
    }

    private function next(): ?TaskInterface
    {
        return array_shift($this->tasks);
    }
}
