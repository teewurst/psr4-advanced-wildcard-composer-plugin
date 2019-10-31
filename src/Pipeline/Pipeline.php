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
    private $tasks;

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

        try {
            if (!is_callable($task)) {
                throw new \BadMethodCallException(
                    'Task ' . get_class($task) . ' is not callable or has invalid Parameter!',
                    0
                );
            }
            return $task($payload, $this);
        } catch (\ArgumentCountError $e) {
            throw new \BadMethodCallException(
                'Task ' . get_class($task) . ' has an invalid argument count! (Previous Exception is set)',
                0,
                $e
            );
        }
    }

    private function next(): TaskInterface
    {
        return array_shift($this->tasks);
    }
}
