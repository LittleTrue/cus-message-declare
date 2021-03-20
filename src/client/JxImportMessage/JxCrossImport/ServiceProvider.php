<?php
namespace customs\CustomsDeclareClient\JxImportMessage\JxCrossImport;

use customs\CustomsDeclareClient\JxImportMessage\JxCrossImport\Client;
use Pimple\Container;
use pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['jx_import_order'] = function ($app) {
            return new Client($app);
        };
    }
}