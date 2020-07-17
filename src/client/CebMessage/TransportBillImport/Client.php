<?php

namespace customs\CustomsDeclareClient\CebMessage\TransportBillImport;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\BaseClient;
use customs\CustomsDeclareClient\Base\CebMessageBuild;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 客户端.
 */
class Client extends BaseClient
{
    use CebMessageBuild;

    //本报文编号
    public $messageType = 'CEB511Message';

    /**
     * @var Application
     */
    protected $credentialValidate;

    //报文发送时间
    private $sendTime;

    //操作类型
    private $opType;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->credentialValidate = $app['credential'];
    }

    /**
     * 运单申报.
     */
    public function transportDeclare($transportBase = [], $transportParams = [])
    {
        $rule = [
            'DeclEntNo'    => 'require|max:18',
            'DeclEntName'  => 'require|max:100',
            'DeclEntDxpid' => 'require|max:30',

            'MessageId' => 'require|max:36',

            'OpType' => 'require|max:1',
        ];

        $this->credentialValidate->setRule($rule);

        if (!$this->credentialValidate->check($transportBase)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }

        $this->sendTime = date('YmdHis', time());
        $this->opType   = $transportBase['op_type'];

        //根节点生成--父类
        $this->setRootNode($transportBase['MessageId']);

        //一个报文可以又多个订单
        foreach ($transportParams as $key => $value) {
            $Logistics = $this->dom->createElement('ceb:Logistics');
            $this->nodeLink['root_node']->appendchild($Logistics);
            $this->nodeLink['Logistics'] = $Logistics;

            $LogisticsHead = $this->dom->createElement('ceb:LogisticsHead');
            $this->nodeLink['Logistics']->appendchild($LogisticsHead);

            $LogisticsHeadEle = [
                'ceb:guid'      => $transportBase['MessageId'],
                'ceb:appType'   => $this->opType,
                'ceb:appTime'   => $this->sendTime,
                'ceb:appStatus' => 2,

                'ceb:logisticsCode' => $value['logistics_code'],
                'ceb:logisticsName' => $value['logistics_name'],
                'ceb:logisticsNo'   => $value['logistics_no'],

                'ceb:billNo'             => empty($value['bill_no']) ? '' : $value['bill_no'],
                'ceb:freight'            => $value['freight'],
                'ceb:insuredFee'         => $value['insured_fee'],
                'ceb:currency'           => $value['currency'],
                'ceb:weight'             => $value['weight'],
                'ceb:packNo'             => $value['pack_no'],
                'ceb:goodsInfo'          => empty($value['goods_info']) ? '' : $value['goods_info'],
                'ceb:consignee'          => $value['consignee'],
                'ceb:consigneeAddress'   => $value['consignee_address'],
                'ceb:consigneeTelephone' => $value['consignee_telephone'],
                'ceb:note'               => empty($value['note']) ? '' : $value['note'],
            ];

            $this->dom = $this->createEle($LogisticsHeadEle, $this->dom, $LogisticsHead);

            $LogisticsHeadEle_arr[] = $LogisticsHeadEle;
        }

        //验证数据
        $this->checkInfo($LogisticsHeadEle_arr);

        //统一传输实体结点实现--父类
        $BaseTransferEle = [
            'copCode' => $transportBase['DeclEntNo'],
            'copName' => $transportBase['DeclEntName'],
            'dxpMode' => 'DXP',
            'dxpId'   => $transportBase['DeclEntDxpid'],
            'note'    => '',
        ];

        $this->setBaseTransfer($BaseTransferEle);

        return $this->dom->saveXML();
    }

    /**
     * 定义验证器来校验清单和清单商品信息.
     */
    public function checkInfo($transportParams)
    {
        $rules = [
            'ceb:logisticsCode' => 'require|max:20',
            'ceb:logisticsName' => 'require',
            'ceb:logisticsNo'   => 'require|max:60',

            'ceb:freight'    => 'require|number',
            'ceb:insuredFee' => 'require|number',
            'ceb:currency'   => 'require',
            'ceb:weight'     => 'require|number',
            'ceb:packNo'     => 'require|number',

            'ceb:consignee'          => 'require|max:100',
            'ceb:consigneeAddress'   => 'require',
            'ceb:consigneeTelephone' => 'require',
        ];

        $this->credentialValidate->setRule($rules);

        if (!$this->credentialValidate->check($checklistInfo)) {
            throw new ClientError('运单数据: ' . $this->credentialValidate->getError());
        }

        return true;
    }
}
