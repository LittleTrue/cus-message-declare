<?php
namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 江西综服  - 进口清单报文
 */
class JxImportListService
{
    /**
     * @var JxImportList
     */
    private $_jxImportListClient;

    public function __construct(Application $app)
    {
        $this->_jxImportListClient = $app['jx_import_list'];
    }

    /**
     * 获取对应服务的报文业务类型businessType
     * 
     * @throws ClientError
     * @throws \Exception
     */
    public function getBusinessType()
    {
        return $this->_jxImportListClient->businessType;
    }

    /**
     * 进口清单申报
     * 
     * @throws ClientError
     * @throws \Exception
     */
    public function generateXmlPost(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }
        return $this->_jxImportListClient->generateXmlPost($declareConfig, $declareParams);
    }
    
}