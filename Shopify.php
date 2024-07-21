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

use Arikaim\Core\Extension\Module;

/**
 * Shopify api module
*/
class Shopify extends Module
{
    /**
     * Install module
     *
     * @return void
    */
    public function install()
    {        
        $this->installDriver('Arikaim\\Modules\\Shopify\\Drivers\\ShopifyApiDriver');
    } 
    
}
