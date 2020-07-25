<?php

namespace customs\CustomsDeclareClient\CebMessage\CancelDeclareExport;

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
    public $messageType = 'CEB605Message';

    /**
     * @var Application
     */
    protected $credentialValidate;

    //操作类型
    private $opType;

    //报文发送时间
    private $sendTime;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->credentialValidate = $app['credential'];
    }

    /**
     * 撤销申请单.
     */
    public function generateXmlPost($declareConfig, $declareParams)
    {
        $rule = [
            'MessageId' => 'require|max:36',

            'OpType' => 'require|max:1',

            'EBEntNo'   => 'require|max:18',
            'EBEntName' => 'require|max:100',

            'DeclEntNo'    => 'require|max:18',
            'DeclEntName'  => 'require|max:100',
            'DeclEntDxpid' => 'require|max:30',

            'CustomsCode' => 'require|max:4',
        ];

        $this->credentialValidate->setRule($rule);

        if (!$this->credentialValidate->check($declareConfig)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }

        $this->sendTime = date('YmdHis', time());
        $this->opType   = $declareConfig['OpType'];

        //根节点生成--父类
        $this->setRootNode($declareConfig['MessageId']);

        foreach ($declareParams as $key => $value) {
            //一个报文有多个申报订单
            $InvtCancel = $this->dom->createElement('ceb:InvtCancel');
            $this->nodeLink['root_node']->appendchild($InvtCancel);
            $this->nodeLink['InvtCancel'] = $InvtCancel;

            $InvtCancelEle = [
                'ceb:guid'        => $declareConfig['MessageId'],
                'ceb:appType'     => $this->opType,
                'ceb:appTime'     => $this->sendTime,
                'ceb:appStatus'   => '2',
                'ceb:customsCode' => $declareConfig['CustomsCode'],
                'ceb:copNo'       => $value['EntEListNo'],
                'ceb:invtNo'      => $value['InvtNo'],
                'ceb:reason'      => $value['Reason'],
                'ceb:agentCode'   => $declareConfig['DeclEntNo'],
                'ceb:agentName'   => $declareConfig['DeclEntName'],
                'ceb:ebcCode'     => $declareConfig['EBEntNo'],
                'ceb:ebcName'     => $declareConfig['EBEntName'],
                'ceb:note'        => empty($value['note']) ? '' : $value['note'],
            ];

            $this->dom = $this->createEle($InvtCancelEle, $this->dom, $InvtCancel);

            //验证数据
            $this->checkInfo($InvtCancelEle);
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
     * 定义验证器来校验订单信息
     * .
     */
    public function checkInfo($invt_cancel_ele)
    {
        $this->credentialValidate->setRule([
            
            'ceb:copNo'  => 'require|max:30',
            'ceb:invtNo' => 'require|max:18',
            'ceb:reason' => 'require|max:1000',

            'ceb:ebcCode' => 'require|max:18',
            'ceb:ebcName' => 'require|max:100',
        ]);

        if (!$this->credentialValidate->check($invt_cancel_ele)) {
            throw new ClientError('报文订单数据: ' . $this->credentialValidate->getError());
        }

        return true;
    }
}
