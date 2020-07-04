<?php

namespace customs\CustomsDeclareClient;

use customs\CustomsDeclareClient\Base\Config;
use Pimple\Container;

/**
 * Class Application.
 */
class Application extends Container
{
    /**
     * @var array
     */
    protected $providers = [
        Base\ServiceProvider::class,
        CebMessage\ChecklistCrossImport\ServiceProvider::class,
        CebMessage\OrderCrossImport\ServiceProvider::class,
        CebMessage\PayReceiveCrossExport\ServiceProvider::class,
        ChinaWindowMessage\OrderExpressImport\ServiceProvider::class,
        GzWindowKjMessage\ChecklistCross\ServiceProvider::class,
        GzWindowKjMessage\GoodsCross\ServiceProvider::class,
        GzWindowKjMessage\InboundCross\ServiceProvider::class,
        GzWindowKjMessage\OrderCross\ServiceProvider::class,
        GzWindowKjMessage\GoodsLoadCross\ServiceProvider::class,
    ];

    /**
     * Application constructor.
     */
    public function __construct(array $config = [])
    {
        parent::__construct();

        $this['config'] = function () use ($config) {
            return new Config($config);
        };

        $this->registerProviders();
    }

    /**
     * @param $id
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Register providers.
     */
    protected function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }
}
