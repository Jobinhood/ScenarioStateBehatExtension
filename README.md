# ScenarioStateBehatExtension

Proof of Concept - Originated from this [discussion](https://github.com/gorghoa/ScenarioStateBehatExtension/issues/36).

**Note:** This is a fork, all credits belong to the authors at [ScenarioStateBehatExtension](https://github.com/gorghoa/ScenarioStateBehatExtension) 

This fork is ready for you to play with and give [feedback](https://github.com/gorghoa/ScenarioStateBehatExtension/issues/36).

## When to use

Behat scenarios are all about state. First you put the system under test
to a special state through the `Given` steps. Then you continue to manipulate
your system through `When` steps and finally testing the resulting state via
the `Then` steps.

When testing a system like a single page app or a stateful website, the resulting state of our steps is handled by the
system itself (either by the browser, or by the php session, etc.).

But, when you are testing a stateless system, chiefly an API, then the resulting state of our steps is handled by no
one. This is the case for this extension.

## Installation

<kbd>1</kbd> Update `composer.json` to use this fork

```json
 "repositories": [
        {
            "type": "git",
            "url": "https://github.com/Jobinhood/ScenarioStateBehatExtension.git"
        }
    ],
    "require": {
        "gorghoa/scenariostate-behat-extension": "dev-develop as master"
    }
```

<kbd>2</kbd> Pull library

```bash
composer update gorghoa/scenariostate-behat-extension
```

<kbd>3</kbd>Then update your project's `behat.yml` config file by loading the extension:

```yaml
default:
    extensions:
        Gorghoa\ScenarioStateBehatExtension\ServiceContainer\ScenarioStateExtension: ~
```

## Usage

This behat extension will allow scenarios steps to retrieve and add objects to a store which can be shared accross your contexts.

It gives you the ability to share state accross Contexts in a scenario through connection to a store.

```gherkin
    Feature: Monkey gathering bananas

      Scenario: A bonobo takes a banana and gives it to a gorilla
        Given there is a bonobo "@bonobo" named "Bun"
        And there is a "red" banana "@banana"
        And there is a gorilla "@gorilla" named "GorillaMax"

        When bonobo "@bonobo" takes banana "@banana"
        Then bonobo "@bonobo" should have a banana

        When bonobo "@bonobo" throw away banana "@banana"
        Then bonobo "@bonobo" should not have a banana
```

This behat extension will store stuff for you and retrieve it on subsequent calls when needed. Here all storable items use 
the `@` notation, though you are free to use your own notation.

### Access to the store from your context classes

To share the store accross all other scenario's steps, your contexts need to implement the
`Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext` interface.

This interface declares one method to implement: `public function setStore(StoreInterface $store)`
which can be imported using `ScenarioStateAwareTrait`. This ScenarioState is responsible for storing your state.

```php
use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext;
use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareTrait;
use Gorghoa\ScenarioStateBehatExtension\StoreInterface;

class FeatureContext implements ScenarioStateAwareContext
{
    use ScenarioStateAwareTrait;
}
```

### Adding stuff to the store

Then you can add stuff to the store through `StoreInterface::add(string $key, mixed $value)`
method.

```php
/**
 * @Given there is a :color banana
 * @Given there is a :color banana :bananaStoreKey
 */
public function thereIsABanana($color = null, $bananaStoreKey = null)
{
    $banana = new Banana($color);

    if (null != $bananaStoreKey) {
        $this->store->add($bananaStoreKey, $banana);
    }
}
```

This stored `$banana` in the store. Your `.feature` would be like:

```gherkin
# Will store the banana in the store under the "@banana" store key
Given there is a "yellow" banana "@banana"

# Won't store anything
Given there is a "yellow" banana
```


### Getting stuff from the store

To get stuff from the store, you can do it manually or via the `ScenarioStateAutoload` annotation.

#### 1. Manually

```php
/**
 * @When a bonobo eats banana :bananaStoreKey
 *
 * @param string $bananaStoreKey
 */
public function aBonoboEatsBanana($bananaStoreKey)
{
    $banana = $this->store->get($bananaStoreKey);

    // ...
}
```

```gherkin
Given there is a banana "@banana"
When a bonobo eats banana "@banana"
```

#### 2. The Lazy way

```php
/**
 * @Then banana :banana should be empty

 * @ScenarioStateAutoload("banana")
 *
 * @param string $bananaStoreKey
 */
public function bananaShouldBeEmpty(Banana $banana)
{
    // The banana is autoloaded from the store for you
    \PHPUnit\Framework\Assert::assertTrue($banana->isEmpty());
}
```

```gherkin
Given there is a banana "@banana"
When a bonobo eats banana "@banana"
Then banana "@banana" should be empty

```

Here, `@ScenarioStateAutoload("banana")` autoloaded the argument `$banana` from the store.

It automatically replaced the string "@banana" from your feature with the `Banana` instance stored in the store.

## There is a Test App

- [See the Test App](https://github.com/Jobinhood/ScenarioStateBehatExtension/tree/develop/testapp)

## Contribute

```
$ git clone git@github.com:Jobinhood/ScenarioStateBehatExtension.git
$ cd ScenarioStateBehatExtension && composer self-update && composer update

# Run Behat
$ ./vendor/bin/behat -c ./testapp/behat.yml

# Run PHPUnit Tests
$ ./vendor/bin/phpunit
```
