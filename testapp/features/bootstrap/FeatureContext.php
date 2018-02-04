<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext,
    Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateAutoload,
    Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareTrait,
    Gorghoa\ScenarioStateBehatExtension\StoreInterface,
    Gorghoa\ScenarioStateBehatExtension\TestApp\Gorilla,
    Gorghoa\ScenarioStateBehatExtension\TestApp\Bonobo,
    Gorghoa\ScenarioStateBehatExtension\TestApp\Banana;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class FeatureContext implements ScenarioStateAwareContext
{
    /**
     * Trait which provided the context with a store to provide and access
     * items during the lifecycle of a scenario.
     */
    use ScenarioStateAwareTrait;
    
    /**
     * @BeforeSuite
     */
    public static function setUpSuite()
    {
        require_once __DIR__.'/../../autoload.php';
    }

    /**
     * @Given there is a bonobo :bonoboStoreKey
     * @Given there is a bonobo :bonoboStoreKey named :name
     */
    public function thereIsABonobo($bonoboStoreKey, $name = null)
    {
        $bonobo = new Bonobo();

        if ($name != null) {
            $bonobo->setName($name);
        }

        $this->store->add($bonoboStoreKey, $bonobo);
    }

    /**
     * @Given there is a :color banana :bananaStoreKey
     * @Given there is a banana :bananaStoreKey
     */
    public function thereIsABanana($color = null, $bananaStoreKey)
    {
        $banana = new Banana();

        if ($color != null) {
            $banana->setColor($color);
        }

        $this->store->add($bananaStoreKey, $banana);
    }

    /**
     * @Given there is a gorilla :gorillaStoreKey
     * @Given there is a gorilla :gorillaStoreKey named :name
     */
    public function thereIsAGorilla($gorillaStoreKey, $name = null)
    {
        $gorilla = new Gorilla();

        if ($name != null) {
            $gorilla->setName($name);
        }

        $this->store->add($gorillaStoreKey, $gorilla);
    }

    /**
     * @ScenarioStateAutoload("bonobo")
     * @ScenarioStateAutoload("banana")
     *
     * @When bonobo :bonobo takes banana :banana
     */
    public function bonoboTakesBanana($bonobo, $banana)
    {
        $bonobo->setBanana($banana);
    }

    /**
     * @ScenarioStateAutoload("bonobo")
     *
     * @Then bonobo :bonobo should have a banana
     */
    public function bonoboShouldHaveBanana($bonobo)
    {
        \PHPUnit\Framework\Assert::assertInstanceOf(Banana::class, $bonobo->getBanana());
    }

    /**
     * @ScenarioStateAutoload("bonobo")
     *
     * @Then bonobo :bonobo should not have a banana
     */
    public function bonoboShouldNotHaveBanana($bonobo)
    {
        \PHPUnit\Framework\Assert::assertNull($bonobo->getBanana());
    }

    /**
     * @ScenarioStateAutoload("bonobo")
     * @ScenarioStateAutoload("banana")
     *
     * @When bonobo :bonobo throw away banana :banana
     */
    public function bonoboThrowAwayBanana($bonobo, $banana)
    {
        if ($bonobo->getBanana() === $banana) {
            $bonobo->throwBananaAway();
        }
    }


}
