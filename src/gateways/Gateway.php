<?php
/**
 * Pin Payments plugin for Craft CMS 3.x
 *
 * Pin Payments for Craft Commerce
 *
 * @link      https://cloudgray.com.au/
 * @copyright Copyright (c) 2021 Cloud Gray Pty Ltd
 */

namespace cloudgrayau\pinpayments\gateways;

use Craft;
use craft\commerce\errors\PaymentException;
use cloudgrayau\pinpayments\PinPaymentsAsset;
use cloudgrayau\pinpayments\models\PinPaymentForm;
use craft\commerce\models\payments\BasePaymentForm;
use craft\commerce\omnipay\base\CreditCardGateway;
use craft\web\View;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Pin\Message\Response;
use Omnipay\Omnipay;
use Omnipay\Pin\Gateway as OmnipayGateway;

/**
 * Class Gateway
 *
 * @author    Cloud Gray Pty Ltd
 * @package   PinPayments
 * @since     1.0.1
 *
 */
class Gateway extends CreditCardGateway
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public $publishableKey;

    /**
     * @var string
     */
    public $apiKey;
    
    /**
     * @var boolean
     */
    public $pinJs = true;
    

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('commerce', 'Pin Payments');
    }

    /**
     * @inheritdoc
     */
    public function getPaymentConfirmationFormHtml(array $params): string
    {
        return $this->_displayFormHtml($params, 'pinpayments/confirmationForm');
    }


    /**
     * @inheritdoc
     */
    public function getPaymentFormHtml(array $params)
    {
        return $this->_displayFormHtml($params, 'pinpayments/paymentForm');
    }

    /**
     * @inheritdoc
     */
    public function getPaymentFormModel(): BasePaymentForm
    {
        return new PinPaymentForm();
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('pinpayments/gatewaySettings', ['gateway' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function populateRequest(array &$request, BasePaymentForm $paymentForm = null)
    {
        /** @var EwayPaymentForm $paymentForm */
        if ($paymentForm) {
            $request['encryptedCardNumber'] = $paymentForm->encryptedCardNumber ?? null;
            $request['encryptedCardCvv'] = $paymentForm->encryptedCardCvv ?? null;
            $request['cardReference'] = $paymentForm->cardReference ?? null;
        }
    }
    
    public function supportsPaymentSources(): bool
    {
        return false;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createGateway(): AbstractGateway
    {
        $gateway = static::createOmnipayGateway($this->getGatewayClassName());
        $gateway->setSecretKey(Craft::parseEnv($this->apiKey));
        return $gateway;
    }

    /**
     * @inheritdoc
     */
    protected function extractCardReference(ResponseInterface $response): string
    {
        /** @var RapidResponse $response */
        if ($response->getCode() !== 'A2000') {
            throw new PaymentException($response->getMessage());
        }

        return $response->getCardReference();
    }


    /**
     * @inheritdoc
     */
    protected function extractPaymentSourceDescription(ResponseInterface $response): string
    {
        $data = $response->getData();

        return Craft::t('commerce-pin', 'Payment card {masked}', ['masked' => $data['Customer']['CardDetails']['Number']]);
    }

    /**
     * @inheritdoc
     */
    protected function getGatewayClassName()
    {
        return '\\'.OmnipayGateway::class;
    }

    // Private Methods
    // =========================================================================

    /**
     * Display a payment form from HTML based on params and template path
     *
     * @param array  $params   Parameters to use
     * @param string $template Template to use
     *
     * @return string
     * @throws \Throwable if unable to render the template
     */
    private function _displayFormHtml($params, $template): string
    {
        $defaults = [
            'gateway' => $this,
            'paymentForm' => $this->getPaymentFormModel()
        ];

        $params = array_merge($defaults, $params);

        $view = Craft::$app->getView();

        $previousMode = $view->getTemplateMode();
        $view->setTemplateMode(View::TEMPLATE_MODE_CP);

        if ($this->pinJs){
          $view->registerJsFile('https://cdn.pinpayments.com/pin.v2.js');
        }
        $view->registerAssetBundle(PinPaymentsAsset::class);

        $html = Craft::$app->getView()->renderTemplate($template, $params);
        $view->setTemplateMode($previousMode);

        return $html;
    }
}
