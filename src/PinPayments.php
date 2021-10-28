<?php
/**
 * Pin Payments plugin for Craft CMS 3.x
 *
 * Pin Payments for Craft Commerce
 *
 * @link      https://cloudgray.com.au/
 * @copyright Copyright (c) 2021 Cloud Gray Pty Ltd
 */

namespace cloudgrayau\pinpayments;

use craft\commerce\services\Gateways;
use cloudgrayau\pinpayments\gateways\Gateway;
use craft\events\RegisterComponentTypesEvent;
use yii\base\Event;

/**
 * Class PinPayments
 *
 * @author    Cloud Gray Pty Ltd
 * @package   PinPayments
 * @since     1.0.1
 *
 */
class PinPayments extends \craft\base\Plugin
{
  
    public $schemaVersion = '1.0.0';

    public function init()
    {
        parent::init();

        Event::on(Gateways::class, Gateways::EVENT_REGISTER_GATEWAY_TYPES,  function(RegisterComponentTypesEvent $event) {
            $event->types[] = Gateway::class;
        });
    }

}
