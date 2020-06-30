Feature: GuaranteedInvoiceInitialTransactionHappyPath
  As a guest user
  I want to make an initial transaction with GuaranteedInvoice
  And to see that initial transaction was successful

  Background:
    Given I initialize shop system

  @woocommerce @test_group_1
  Scenario Outline: initial transaction
    And I activate "GuaranteedInvoice" payment action <payment_action> in configuration
    And I prepare checkout with purchase sum "100" in shop system as "registered customer"
    And I see "Wirecard Guaranteed Invoice by Wirecard"
    And I start "GuaranteedInvoice" payment
    When I fill "GuaranteedInvoice" fields in the shop
    And I place the order and continue "GuaranteedInvoice" payment
    Then I see successful payment
    And I see "GuaranteedInvoice" transaction type <transaction_type> in transaction table

    Examples:
      | payment_action                                     | transaction_type |
      | "reserve"                                          | "authorization"  |
