<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 海关直连总署 - CEB报文 - 进口清单申报.
 */
class ChecklistCrossImportService
{
    /**
     * @var ChecklistCrossImport
     */
    private $_checklistCrossImportClient;

    public function __construct(Application $app)
    {
        $this->_checklistCrossImportClient = $app['checklist_cross_import'];

    }

    /**
     * 获取对应服务的报文类型messageType.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function getMessageType()
    {
        return $this->_checklistCrossImportClient->messageType;
    }

    /**
     * 进口清单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function generateXmlPost(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_checklistCrossImportClient->generateXmlPost($declareConfig, $declareParams);
    }
}
