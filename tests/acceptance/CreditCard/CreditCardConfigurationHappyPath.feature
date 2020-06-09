Feature: CreditCardConfigurationHappyPath
  As admin user
  I want to check that the credit card configuration page is working properly

  Background:
    Given I initialize shop system

  @woocommerce @test
  Scenario Outline: initial transaction Non 3DS
    Given I deactivate "CreditCard" payment method in configuration
    When I go into the configuration mask as "admin user" and activate "CreditCard" method
    And I fill fields with "CreditCard" data for payment action <payment_action> and transaction type <transaction_type>
#    And I see "Wirecard Credit Card"
#    And I start "CreditCard" payment
#    And I place the order and continue "CreditCard" payment
#    When I fill "CreditCard" fields in the shop
#    Then I see successful payment
#    And I see "CreditCard" transaction type <transaction_type> in transaction table

    Examples:
      | payment_action  | amount | transaction_type |
      |    "reserve"    |  "20"  |  "authorization" |
      |      "pay"      |  "20"  |    "purchase"    |
