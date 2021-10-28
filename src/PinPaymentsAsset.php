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

use craft\web\AssetBundle;

/**
 * @author    Cloud Gray Pty Ltd
 * @package   PinPayments
 * @since     1.0.1
 */
class PinPaymentsAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@cloudgrayau/pinpayments/resources";
        
        $this->js = [
            'js/pin.js',
        ];
        
        $this->css = [
            'css/pin.css',
        ];
        
        parent::init();
    }
}
