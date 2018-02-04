<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Annotation;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @Annotation
 * @Target("METHOD")
 */
class ScenarioStateAutosave
{

    /**
     * Argument name.
     *
     * @var string
     */
    public $argument;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (!isset($options['value']) || empty(trim($options['value']))) {
            throw new \InvalidArgumentException(
                'You must provide the function argument you wish to autosave '.
                'to the store in ScenarioStateAutosave annotation'
            );
        }

        $this->argument = $options['value'];
    }

    /**
     * @return string
     */
    public function getArgument()
    {
        return $this->argument;
    }
}
