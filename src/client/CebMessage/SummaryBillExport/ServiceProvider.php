<?php

namespace customs\CustomsDeclareClient\CebMessage\SummaryBillExport;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['summary_bill_export'] = function ($app) {
            return new Client($app);
        };
    }
}