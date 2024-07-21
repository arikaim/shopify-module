<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Shopify\Drivers;

use Shopify\Context;

use Arikaim\Core\Driver\Traits\Driver;
use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Arikaim\Modules\Shopify\OauthSessionStorage;

/**
 * Shopify api driver class
 */
class ShopifyApiDriver implements DriverInterface
{   
    use Driver;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDriverParams(
            'shopify.api',
            'api',
            'Shopify Api',
            'Shopify Api driver'
        );      
    }

    /**
     * Initialize driver
     *
     * @return void
     */
    public function initDriver($properties)
    {
        $config = $properties->getValues();         

        Context::initialize(
            apiKey: $config['apiKey'], 
            apiSecretKey: $config['apiSecret'],
            scopes: $config['scopes'],
            hostName: $config['shop'],
            sessionStorage: new OauthSessionStorage(),
            apiVersion: $config['apiVersion'],
            isEmbeddedApp: $config['isEmbeddedApp'],
            isPrivateApp: $config['isPrivateApp'],
        );

    }

    /**
     * Create driver config properties array
     *
     * @param Arikaim\Core\Collection\Properties $properties
     * @return void
     */
    public function createDriverConfig($properties)
    {
        $properties->property('apiKey',function($property) {
            $property
                ->title('Api Key')
                ->type('text')
                ->readonly(false)
                ->value('')
                ->default('');
        });   
      
        $properties->property('apiSecret',function($property) {
            $property
                ->title('Client Secret')
                ->type('text')
                ->readonly(false)
                ->value('')
                ->default('');
        }); 
       
        $properties->property('shop',function($property) {
            $property
                ->title('Shop host')
                ->type('text')
                ->readonly(false)
                ->required(true);
               
        }); 

        $properties->property('apiVersion',function($property) {
            $property
                ->title('Api version')
                ->type('text')
                ->readonly(false)
                ->value('2023-04')
                ->default('2023-04');
        }); 

        $properties->property('isEmbeddedApp',function($property) {
            $property
                ->title('Embedded App')
                ->type('boolean')
                ->readonly(false)
                ->value(true)
                ->default(true);
        }); 

        $properties->property('isPrivateApp',function($property) {
            $property
                ->title('Private App')
                ->type('boolean')
                ->readonly(false)
                ->value(false)
                ->default(false);
        }); 

        $properties->property('scopes',function($property) {
            $property
                ->title('Scopes (access)')
                ->type('text-area')
                ->readonly(false)
                ->value('read_customers,read_metaobjects,read_products')
                ->default('read_customers,read_metaobjects,read_products');
        }); 
    }
}
