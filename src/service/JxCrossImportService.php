<?php
namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 江西综服  - 进口订单报文
 */
class JxCrossImportService
{
    /**
     * @var JxImportOrder
     */
    private $_jxImportOrderClient;

    public function __construct(Application $app)
    {
        $this->_jxImportOrderClient = $app['jx_import_order'];
    }

    /**
     * 获取对应服务的报文业务类型businessType
     * 
     * @throws ClientError
     * @throws \Exception
     */
    public function getBusinessType()
    {
        return $this->_jxImportOrderClient->businessType;
    }

    /**
     * 进口订单申报
     * 
     * @throws ClientError
     * @throws \Exception
     */
    public function generateXmlPost(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }
        return $this->_jxImportOrderClient->generateXmlPost($declareConfig, $declareParams);
    }
    
}