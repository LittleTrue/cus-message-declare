<?php
namespace customs\CustomsDeclareClient\JxImportMessage\JxImportList;

use customs\CustomsDeclareClient\JxImportMessage\JxImportList\Client;
use Pimple\Container;
use pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['jx_import_list'] = function ($app) {
            return new Client($app);
        };
    }
}