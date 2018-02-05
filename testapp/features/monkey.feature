Feature: Monkey gathering bananas

  Scenario: A bonobo takes a banana and gives it to a gorilla
    Given there is a bonobo "@bonobo" named "Bun"
    And there is a "red" banana "@banana"
    And there is a gorilla "@gorilla" named "GorillaMax"

    When bonobo "@bonobo" takes banana "@banana"
    Then bonobo "@bonobo" should have a banana

    When bonobo "@bonobo" throw away banana "@banana"
    Then bonobo "@bonobo" should not have a banana
