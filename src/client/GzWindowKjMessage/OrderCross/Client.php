<?php

namespace customs\CustomsDeclareClient\GzWindowKjMessage\OrderCross;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\BaseClient;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 客户端.
 */
class Client extends BaseClient
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * 进出口订单申报.
     *
     * @throws ClientError
     */
    public function declare(array $infos, string $gz)
    {
        //TODO
    }
}
