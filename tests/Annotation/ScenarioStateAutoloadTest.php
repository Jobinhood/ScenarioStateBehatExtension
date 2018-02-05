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
 */
class ScenarioStateAutoloadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getArguments
     *
     * @param array  $options
     * @param string $argument
     */
    public function testWithValue(array $options, $argument)
    {
        $annotation = new ScenarioStateAutoload($options);
        $this->assertEquals($argument, $annotation->getArgument());
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return [
            [
                ['value' => '@foo'],
                '@foo'
            ]
        ];
    }
}
