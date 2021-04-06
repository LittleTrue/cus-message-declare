<?php

namespace customs\CustomsDeclareClient\GzWindowKjMessage\OrderCross;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\BaseClient;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 客户端 -- 由于业务后续修改为走总署版, 目前暂时不集成, 等待业务需要再进行集成.
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
    public function generateXmlPost(array $infos, string $gz)
    {
        //TODO
    }
}
