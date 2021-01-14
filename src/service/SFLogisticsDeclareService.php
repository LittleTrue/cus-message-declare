<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 *  顺丰进口运单申报.
 */
class SFLogisticsDeclareService
{
    /**
     * @var SFLogisticsDeclare
     */
    private $_sfLogisticsDeclare;

    /**
     * @var HttpMessageDeclare
     */
    private $_httpMessageDeclareClient;

    public function __construct(Application $app)
    {
        $this->_sfLogisticsDeclare      = $app['sf_logistics_declare'];
        $this->_httpMessageDeclareClient = $app['http_message_declare'];
    }

    /**
     * 获取对应服务的报文类型messageType.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function getMessageType()
    {
        return $this->_sfLogisticsDeclare->messageType;
    }

    /**
     * 进口运单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function generateFormPost(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_sfLogisticsDeclare->generateFormPost($declareConfig, $declareParams);
    }

    /**
     * 生成按照单一窗口HTTP申报通路封装的报文.
     */
    public function generateHttpDoc(array $declareConfig, array $declareParams, array $httpBase, $key = '')
    {
        // if (empty($declareConfig) || empty($declareParams)) {
        //     throw new ClientError('参数缺失', 1000001);
        // }

        // if (empty($httpBase)) {
        //     throw new ClientError('参数缺失', 1000001);
        // }

        // $xml_data = $this->_transportBillExport->generateFormPost($declareConfig, $declareParams);

        // return $this->_httpMessageDeclareClient->generateHttpDoc($this->_transportBillExport->messageType, $xml_data, $httpBase, $key);
    }
}
