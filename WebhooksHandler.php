<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Shopify;

use Shopify\Webhooks\Handler;

/**
 *  Shopify webhooks handler
 */
class WebhooksHandler implements Handler
{
    /**
     *  Constructor
     */
    public function __construct()
    {
    }

    /**
     * Handle webhook
     * 
     * @param string $topic
     * @param string $shop
     * @param array $requestBody
     * @return void
     */
    public function handle(string $topic, string $shop, array $requestBody): void
    {
        global $arikaim;

        // trigger event
        $arikaim->get('event')->dispatch('shopify.webhook',[
            'topic'   => $topic,
            'shop'    => $shop,
            'request' => $requestBody
        ]);
    }
}
