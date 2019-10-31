<?php
declare(strict_types=1);

namespace teewurst\Prs4AdvancedWildcardComposer\Di;

/**
 * Class Container
 *
 * Class to store and create Object dependend on
 *
 * @package teewurst\Prs4AdvancedWildcardComposer\Di
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class Container
{
    private $store = [];
    private $factories = [];

    /**
     * Container constructor.
     *
     * @param array $factories
     */
    public function __construct($factories = [])
    {
        $this->factories = $factories;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        if (!$this->has($key) && $this->factories[$key] ?? false) {
            $this->set($key, (new $this->factories[$key])($this, $key));
        }
        return $store[$key] ?? null;
    }

    /**
     * @param string $key
     * @param        $object
     * @return void
     */
    public function set(string $key, $object): void
    {
        $this->store[$key] = $object;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->store[$key] ?? false;
    }
}
