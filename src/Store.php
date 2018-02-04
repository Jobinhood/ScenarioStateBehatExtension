<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension;

use Gorghoa\ScenarioStateBehatExtension\Exception\MissingStateException;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class Store implements StoreInterface
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new MissingStateException("Missing item \"{$key}\" was requested from store.");
        }

        return $this->items[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value)
    {
        $this->items[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        unset($this->items[$key]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getKeys()
    {
        return array_keys($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        $storeState = array();
        foreach ($this->items as $storeKey => $value) {
            array_push(
                $storeKey,
                get_class($value)
            );
        }

        return $storeState;
    }

}
