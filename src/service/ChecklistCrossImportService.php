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
     * @var BankPurchase
     */
    private $_checklistCrossImportClient;

    public function __construct(Application $app)
    {
        $this->_checklistCrossImportClient = $app['checklist_cross_import'];
    }

    /**
     * 进口清单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function declare(array $infos)
    {
        if (empty($infos)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_checklistCrossImportClient->declare($infos);
    }
}
