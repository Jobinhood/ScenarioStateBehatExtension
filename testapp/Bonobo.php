<?php

namespace Gorghoa\ScenarioStateBehatExtension\TestApp;

use Gorghoa\ScenarioStateBehatExtension\TestApp\Banana;

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class Bonobo
{
    /**
     * @var null|Banana
     */
    private $banana = null;

    /**
     * @var null|String
     */
    private $name = null;

    /**
     * @param Banana $banana
     */
    public function setBanana(Banana $banana)
    {
        $this->banana = $banana;
    }

    /**
     * @return Banana
     */
    public function getBanana()
    {
        return $this->banana;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function throwBananaAway()
    {
        $this->banana = null;
    }

}
