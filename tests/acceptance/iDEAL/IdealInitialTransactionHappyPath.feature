Feature: iDealInitialTransaction
  As a guest user
  I want to make an initial transaction with iDeal
  And to see that initial transaction was successful

  Background:
    Given I initialize shop system

  @woocommerce
  Scenario Outline: initial transaction
    And I activate "iDEAL" payment action <payment_action> in configuration
    And I prepare checkout with purchase sum "100" in shop system as "guest customer"
    And I see "Wirecard iDEAL"
    And I start "iDEAL" payment
    When I fill "iDEAL" fields in the shop
    And I place the order and continue "iDEAL" payment
    When I perform "iDEAL" actions outside of the shop
    Then I see successful payment
    And I see "iDEAL" transaction type <transaction_type> in transaction table

    Examples:
      | payment_action                                     | transaction_type |
      | "debit"                                                    | "debit" |
