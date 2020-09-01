<?php

namespace customs\CustomsDeclareClient\CebMessage\PayReceiveCrossExport;

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
    public $messageType = 'CEB403Message';

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
     * 出口收款单报.
     *
     * @throws ClientError
     */
    public function generateXmlPost($declareConfig, $declareParams)
    {
        $rule = [
            'MessageId' => 'require|max:36',

            'OpType' => 'require|max:1',

            'EBEntNo'   => 'require|max:18',
            'EBEntName' => 'require|max:100',

            'payName' => 'require|max:100', //支付企业名称

            'DeclEntNo'    => 'require|max:18',
            'DeclEntName'  => 'require|max:100',
            'DeclEntDxpid' => 'require|max:30',
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
            $Receipts = $this->dom->createElement('ceb:Receipts');
            $this->nodeLink['root_node']->appendchild($Receipts);
            $this->nodeLink['Receipts'] = $Receipts;

            $ReceiptsEle = [
                'ceb:guid'      => $value['guid'],
                'ceb:appType'   => $this->opType,
                'ceb:appTime'   => $this->sendTime,
                'ceb:appStatus' => $declareConfig['appStatus'], ,

                'ceb:ebpCode' => empty($declareConfig['EBPEntNo']) ? '' : $declareConfig['EBPEntNo'],
                'ceb:ebpName' => empty($declareConfig['EBPEntName']) ? '' : $declareConfig['EBPEntName'],
                'ceb:ebcCode' => $declareConfig['EBEntNo'],
                'ceb:ebcName' => $declareConfig['EBEntName'],

                'ceb:orderNo'        => $value['order_no'],
                'ceb:payCode'        => empty($value['pay_code']) ? '' : $value['pay_code'],
                'ceb:payName'        => $value['pay_name'],
                'ceb:payNo'          => empty($value['pay_no']) ? '' : $value['pay_no'],
                'ceb:charge'         => $value['charge'],
                'ceb:currency'       => $this->currency,
                'ceb:accountingDate' => $value['accounting_date'],
                'ceb:note'           => empty($value['note']) ? '' : $value['note'],
            ];

            $this->dom = $this->createEle($ReceiptsEle, $this->dom, $Receipts);

            //验证数据
            $this->checkInfo($ReceiptsEle);
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
     * 定义验证器来校验收款单数据.
     */
    public function checkInfo($ReceiptsEle)
    {
        $rules = [
            'ceb:guid'           => 'require',
            'ceb:ebcCode'        => 'require|max:18',
            'ceb:ebcName'        => 'require|max:100',
            'ceb:orderNo'        => 'require|max:60',
            'ceb:payName'        => 'require',
            'ceb:charge'         => 'require',
            'ceb:currency'       => 'require',
            'ceb:accountingDate' => 'require',
        ];

        $this->credentialValidate->setRule($rules);

        if (!$this->credentialValidate->check($ReceiptsEle)) {
            throw new ClientError('运单数据: ' . $this->credentialValidate->getError());
        }

        return true;
    }
}
