<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 *  广州单一窗口 - KJ报文 - 进出口入仓单申报.
 */
class InboundCrossService
{
    /**
     * @var InboundCross
     */
    private $_inboundCrossClient;

    public function __construct(Application $app)
    {
        $this->_inboundCrossClient = $app['inbound_cross'];
    }

    /**
     * 获取对应服务的报文类型messageType.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function getMessageType()
    {
        return $this->_inboundCrossClient->messageType;
    }

    /**
     * 进出口入仓单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function generateXmlPost(array $infos)
    {
        if (empty($infos)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_inboundCrossClient->generateXmlPost($infos);
    }
}
