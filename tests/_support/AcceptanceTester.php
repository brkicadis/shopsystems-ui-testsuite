<?php


use Codeception\Actor;
use Step\Acceptance\PaymentMethod\CreditCardStep;
use Step\Acceptance\ShopSystem\PrestashopStep;
use Step\Acceptance\ShopSystem\WoocommerceStep;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */

define('CONFIG_FILE', getcwd() . DIRECTORY_SEPARATOR . 'config.json');
/**
 *
 */
define('DATA_FOLDER_PATH', getcwd() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . '_data' . DIRECTORY_SEPARATOR);
/**
 *
 */
define('CUSTOMER_DATA_FOLDER_PATH', DATA_FOLDER_PATH . 'Customer' . DIRECTORY_SEPARATOR);
/**
 *
 */
define('LOCATOR_FOLDER_PATH', DATA_FOLDER_PATH . 'Locator' . DIRECTORY_SEPARATOR);

/**
 *
 */
define('SHOP_SYSTEM_LOCATOR_FOLDER_PATH', LOCATOR_FOLDER_PATH . 'ShopSystem' . DIRECTORY_SEPARATOR);

/**
 *
 */
define('PAYMENT_METHOD_LOCATOR_FOLDER_PATH', LOCATOR_FOLDER_PATH . 'PaymentMethod' . DIRECTORY_SEPARATOR);

/**
 *
 */
define('PAYMENT_METHOD_DATA_FOLDER_PATH', DATA_FOLDER_PATH . 'PaymentMethodData' . DIRECTORY_SEPARATOR);
/**
 *
 */
define('PAYMENT_METHOD_CONFIG_FOLDER_PATH', DATA_FOLDER_PATH . 'PaymentMethodConfig' . DIRECTORY_SEPARATOR);
/**
 *
 */
define('CUSTOMER', 'customer');
/**
 *
 */
define('CREDIT_CARD', 'creditCard');
/**
 *
 */
define('PAY_PAL', 'payPal');

/**
 * Class AcceptanceTester
 */
class AcceptanceTester extends Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * @var Actor|PrestashopStep|WoocommerceStep
     */
    private $shopInstance;

    /**
     * @var
     */
    private $gateway;

    /**
     * @var Actor|CreditCardStep|
     */
    private $paymentMethod;

    /**
     * @var
     */
    private $configData;

    /**
     * @return mixed
     */
    public function getConfigData()
    {
        return $this->configData;
    }

    /**
     * @return mixed
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param mixed $gateway
     */
    public function setGateway($gateway): void
    {
        $this->gateway = $gateway;
    }


    /**
     * @return Actor|PrestashopStep|WoocommerceStep
     */
    private function getShopInstance()
    {
        return $this->shopInstance;
    }

    /**
     * @return Actor|CreditCardStep|
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param $paymentMethod
     * @return bool
     */
    public function isPaymentMethodSelected($paymentMethod): bool
    {
        return $this->paymentMethod === null;
    }

    /**
     * @Given I initialize shopsystem
     * @throws Exception
     */

    public function iInitializeShopsystem(): void
    {
        $this->configData = $this->getDataFromDataFile(CONFIG_FILE);
        $this->setGateway($this->configData->gateway);
        $this->selectShopInstance();
        //tell shop instance what gateway we are using
        $this->getShopInstance()->setGateway($this->getGateway());
        //tell which customer data to use and initialize customer config
        $this->getShopInstance()->setConfigObject(CUSTOMER, $this->configData->customer_data);
        $this->getShopInstance()->configureShopSystemCurrencyAndCountry($this->configData->currency, $this->configData->default_country);
    }

    /**
     *
     */
    private function selectShopInstance(): void
    {
        $shopInstanceMap = [
            'prestashop' => Step\Acceptance\ShopSystem\PrestashopStep::class,
            'woocommerce' => Step\Acceptance\ShopSystem\WoocommerceStep::class
        ];
        $usedShopEnvVariable = getenv('SHOP_SYSTEM');
        if ($usedShopEnvVariable) {
            $this->shopInstance = new $shopInstanceMap[$usedShopEnvVariable]($this->getScenario());
        }
    }

    /**
     * @param $paymentMethod
     */
    private function selectPaymentMethod($paymentMethod): void
    {
        $paymentMethodInstanceMap = [
            'CreditCard' => Step\Acceptance\PaymentMethod\CreditCardStep::class,
            'PayPal' => Step\Acceptance\PaymentMethod\PayPalStep::class
        ];
        $this->paymentMethod = new $paymentMethodInstanceMap[$paymentMethod]($this->getScenario());
        //tell which payment method data to use and initialize customer config
        $paymentMethodDataName = strtolower($paymentMethod . '_data');
        $this->getPaymentMethod()->setConfigObject(lcfirst($paymentMethod), $this->configData->$paymentMethodDataName);
    }

    /**
     * @Given I activate :paymentMethod payment action :paymentAction in configuration
     * @param $paymentMethod
     * @param $paymentAction
     * @throws Exception
     */
    public function iActivatePaymentActionInConfiguration($paymentMethod, $paymentAction): void
    {
        $this->getShopInstance()->configurePaymentMethodCredentials($paymentMethod, $paymentAction);
    }

    /**
     * @Given I prepare checkout with purchase sum :purchaseSum in shopsystem
     */
    public function iPrepareCheckoutWithPurchaseSumInShopsystem($purchaseSum): void
    {
        $this->getShopInstance()->fillBasket($purchaseSum);
        $this->getShopInstance()->goToCheckout();
        $this->getShopInstance()->fillCustomerDetails();
    }

    /**
     * @Then I see :text
     * @param $text
     */
    public function iSee($text): void
    {
        $this->see($text);
    }

    /**
     * @Then I start :paymentMethod payment
     * @param $paymentMethod
     * @throws Exception
     */
    public function iStartPayment($paymentMethod): void
    {
        $this->getShopInstance()->startPayment($paymentMethod);
    }


    /**
     * @Given I perform :paymentMethod payment actions in the shop
     * @param $paymentMethod
     * @throws Exception
     */
    public function iPerformPaymentActionsInTheShop($paymentMethod): void
    {
        $this->selectPaymentMethod($paymentMethod);
        $this->getPaymentMethod()->performPaymentActionsInTheShop();
        $this->getShopInstance()->proceedWithPayment($paymentMethod);
    }

    /**
     * @When I go through external flow
     * @throws Exception
     */
    public function iGoThroughExternalFlow(): void
    {
        $this->getPaymentMethod()->goThroughExternalFlow();
    }

    /**
     * @Then I see successful payment
     */
    public function iSeeSuccessfulPayment(): void
    {
        $this->getShopInstance()->validateSuccessPage();
    }

    /**
     * @Then I see :paymentMethod transaction type :paymentAction in transaction table
     * @param $paymentMethod
     * @param $paymentAction
     */
    public function iSeeTransactionTypeInTransactionTable($paymentMethod, $paymentAction): void
    {
        $this->getShopInstance()->validateTransactionInDatabase($paymentMethod, $paymentAction);
    }


}
