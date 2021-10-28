<?php
/**
 * Pin Payments plugin for Craft CMS 3.x
 *
 * Pin Payments for Craft Commerce
 *
 * @link      https://cloudgray.com.au/
 * @copyright Copyright (c) 2021 Cloud Gray Pty Ltd
 */

namespace cloudgrayau\pinpayments\models;

use craft\commerce\models\payments\CreditCardPaymentForm;
use craft\commerce\models\PaymentSource;

/**
 * Class PinPaymentForm
 *
 * @author    Cloud Gray Pty Ltd
 * @package   PinPayments
 * @since     1.0.1
 *
 */
class PinPaymentForm extends CreditCardPaymentForm
{
    /**
     * @var string
     */
    public $encryptedCardNumber;

    /**
     * @var string
     */
    public $encryptedCardCvv;

    /**
     * @var string credit card reference
     */
    public $cardReference;

    /**
     * @inheritdoc
     */
    public function setAttributes($values, $safeOnly = true)
    {
        parent::setAttributes($values, $safeOnly);
    }

    /**
     * @inheritdoc
     */
    public function populateFromPaymentSource(PaymentSource $paymentSource)
    {
        $this->cardReference = $paymentSource->token;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        if (empty($this->cardReference)) {
            return [
                [['firstName', 'lastName', 'month', 'year', 'encryptedCardNumber', 'encryptedCardCvv'], 'required'],
                [['month'], 'integer', 'integerOnly' => true, 'min' => 1, 'max' => 12],
                [['year'], 'integer', 'integerOnly' => true, 'min' => date('Y'), 'max' => date('Y') + 12],
            ];
        }

        return [];
    }
}