Feature: Monkey gathering bananas

  Scenario: Monkey gives a banana to another monkey
    Given there is a bonobo "@bonobo" named "Bun"
    And there is a "red" banana "@banana"
    And there is a gorilla "@gorilla" named "GorillaMax"

    When bonobo "@bonobo" takes banana "@banana"
    Then bonobo "@bonobo" should have a banana

    When bonobo "@bonobo" throw away banana "@banana"
    Then bonobo "@bonobo" should not have a banana

  #Scenario Outline: Monkey gives a banana to another monkey
    #Given there is a bonobo "@bonobo" named "Bun"
    #And there is a "red" banana "@banana"
    #And there is a gorilla "@gorilla" named "GorillaMax"
    #And bonobo "@bonobo" gives banana "@banana" to gorilla "@gorilla"

    #And there are the following 3 gorillas:
      #| storeKey  | name |
      #| @gorilla1 | Max  |
      #| @gorilla2 | Oax  |
      #| @gorilla3 | Rax  |
    #Then the gorilla is named <gorilla>

    #Examples:
      #| @gorilla1 |
      #| @gorilla2 |
      #| @gorilla3 |
