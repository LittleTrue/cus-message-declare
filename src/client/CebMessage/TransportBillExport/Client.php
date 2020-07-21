<?php

namespace customs\CustomsDeclareClient\CebMessage\TransportBillExport;

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
    public $messageType = 'CEB505Message';

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
     * 出口运单申报.
     */
    public function declare($declareConfig, $declareParams)
    {
        $rule = [
            'DeclEntNo'    => 'require|max:18',
            'DeclEntName'  => 'require|max:100',
            'DeclEntDxpid' => 'require|max:30',

            'MessageId' => 'require|max:36',

            'OpType' => 'require|max:1',

            'EHSEntNo'   => 'require|max:18',
            'EHSEntName' => 'require|max:100',
            // 'EBPEntNo'  => 'require|max:50',
            // 'EBPEntName' => 'require|max:100',
        ];

        $this->credentialValidate->setRule($rule);

        if (!$this->credentialValidate->check($declareConfig)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }

        $this->sendTime = date('YmdHis', time());
        $this->opType   = $declareConfig['OpType'];

        //根节点生成--父类
        $this->setRootNode($declareConfig['MessageId']);

        //一个报文可以又多个订单
        foreach ($declareParams as $key => $value) {
            $Logistics = $this->dom->createElement('ceb:Logistics');
            $this->nodeLink['root_node']->appendchild($Logistics);
            $this->nodeLink['Logistics'] = $Logistics;

            $LogisticsEle = [
                'ceb:guid'      => $declareConfig['MessageId'],
                'ceb:appType'   => $this->opType,
                'ceb:appTime'   => $this->sendTime,
                'ceb:appStatus' => 2,

                'ceb:logisticsCode' => $declareConfig['EHSEntNo'],
                'ceb:logisticsName' => $declareConfig['EHSEntName'],
                'ceb:logisticsNo'   => $value['EntWaybillNo'],

                'ceb:freight'      => $value['freight'],
                'ceb:insuredFee'   => $value['insured_fee'],
                'ceb:currency'     => $this->currency,
                'ceb:grossWeight'  => $value['weight'],
                'ceb:packNo'       => $value['pack_no'],
                'ceb:goodsInfo'    => empty($value['goods_info']) ? '' : $value['goods_info'],
                'ceb:ebcCode'      => empty($declareConfig['EBEntNo']) ? '' : $declareConfig['EBEntNo'],
                'ceb:ebcName'      => empty($declareConfig['EBEntName']) ? '' : $declareConfig['EBEntName'],
                'ceb:ebcTelephone' => empty($declareConfig['ebcTelephone']) ? '' : $declareConfig['ebcTelephone'],
                'ceb:note'         => empty($value['note']) ? '' : $value['note'],
            ];

            $this->dom = $this->createEle($LogisticsEle, $this->dom, $Logistics);

            //验证数据
            $this->checkInfo($LogisticsEle);
        }

        //统一传输实体结点实现--父类
        $BaseTransferEle = [
            'copCode' => $declareConfig['DeclEntNo'],
            'copName' => $declareConfig['DeclEntName'],
            'dxpMode' => 'DXP',
            'dxpId'   => $declareConfig['DeclEntDxpid'],
            'note'    => '',
        ];

        $this->setBaseTransfer($BaseTransferEle);

        return $this->dom->saveXML();
    }

    /**
     * 定义验证器来校验清单和清单商品信息.
     */
    public function checkInfo($LogisticsEle)
    {
        $rules = [
            'ceb:logisticsCode' => 'require|max:20',
            'ceb:logisticsName' => 'require|max:100',
            'ceb:logisticsNo'   => 'require|max:80',

            'ceb:freight'      => 'require|number',
            'ceb:insuredFee'   => 'require|number',
            'ceb:currency'     => 'require',
            'ceb:grossWeight'  => 'require|number',
            'ceb:packNo'       => 'require|number',
            'ceb:goodsInfo'    => 'require'
        ];

        $this->credentialValidate->setRule($rules);

        if (!$this->credentialValidate->check($LogisticsEle)) {
            throw new ClientError('运单数据: ' . $this->credentialValidate->getError());
        }

        return true;
    }
}
