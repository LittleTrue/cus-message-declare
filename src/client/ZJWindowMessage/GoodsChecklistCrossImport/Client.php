<?php

namespace customs\CustomsDeclareClient\ZJWindowMessage\GoodsChecklistImport;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\BaseClient;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;
use customs\CustomsDeclareClient\Base\ZJWindowMessageBuild;

/**
 * 客户端.
 */
class Client extends BaseClient
{
    use ZJWindowMessageBuild;

    
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * 进出口清单申报.
     *
     * @throws ClientError
     */
    public function generateXmlPost(array $infos, string $gz)
    {
        //TODO
    }
}
