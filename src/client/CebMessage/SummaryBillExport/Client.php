<?php

namespace customs\CustomsDeclareClient\CebMessage\SummaryBillExport;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\BaseClient;
use customs\CustomsDeclareClient\Base\CebMessageBuild;

/**
 * 客户端.
 */
class Client extends BaseClient
{
    use CebMessageBuild;

    //本报文编号
    public $messageType = 'CEB701Message';

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
     * 出口汇总单.
     */
    public function generateXmlPost(array $declareConfig, array $declareParams)
    {
        $rule = [
            'DeclEntNo'    => 'require|max:18',
            'DeclEntName'  => 'require|max:100',
            'DeclEntDxpid' => 'require|max:30',
            'MessageId'    => 'require|max:36',
            'OpType'       => 'require|max:1',
            'appStatus'    => 'require|max:1',
            // 'CustomsCode'  => 'require|max:4',
            // 'EBEntNo'      => 'require|max:18',
            // 'EBEntName'    => 'require|max:100',
            // 'DeclAgentCode' => 'require|max:18',
            // 'DeclAgentName' => 'require|max:100',
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
            $head = $value['head'];

            $list = $value['list'];

            //一个报文有多个申报订单
            $SummaryApply = $this->dom->createElement('ceb:SummaryApply');
            $this->nodeLink['root_node']->appendchild($SummaryApply);
            $this->nodeLink['SummaryApply'] = $SummaryApply;

            //一个清单一个清单头
            $SummaryApplyHead = $this->dom->createElement('ceb:SummaryApplyHead');
            $this->nodeLink['SummaryApply']->appendchild($SummaryApplyHead);

            $SummaryApplyHeadEle = [
                'ceb:guid'        => $declareConfig['MessageId'],
                'ceb:appType'     => $this->opType,
                'ceb:appTime'     => $this->sendTime,
                'ceb:appStatus'   => $declareConfig['appStatus'],
                'ceb:customsCode' => $head['CustomsCode'],
                'ceb:copNo'       => $head['EntEListNo'],
                'ceb:preNo'       => isset($head['preNo']) ? $head['preNo'] : '',
                'ceb:sumNo'       => isset($head['sumNo']) ? $head['sumNo'] : '',

                'ceb:agentCode'     => $head['DeclEntNo'],
                'ceb:agentName'     => $head['DeclEntName'],
                'ceb:ebcCode'       => $head['EBEntNo'],
                'ceb:ebcName'       => $head['EBEntName'],
                'ceb:declAgentCode' => $head['DeclAgentCode'],
                'ceb:declAgentName' => $head['DeclAgentName'],
                'ceb:summaryFlag'   => $head['SummaryFlag'],
                'ceb:itemNameFlag'  => $head['ItemNameFlag'],
                'ceb:msgCount'      => $head['MsgCount'],
                'ceb:msgSeqNo'      => $head['MsgSeqNo'],
            ];

            if (1 == $this->opType) {
                //增加变更申报字段
                unset($InventoryHeadEle['ceb:preNo'], $InventoryHeadEle['ceb:sumNo']);
            }

            $this->dom = $this->createEle($SummaryApplyHeadEle, $this->dom, $SummaryApplyHead);

            foreach ($list as $key => $value) {
                $SummaryApplyList = $this->dom->createElement('ceb:SummaryApplyList');
                $this->nodeLink['SummaryApply']->appendchild($SummaryApplyList);

                $SummaryApplyListEle = [
                    'ceb:invtNo' => $value['InvtNo'],
                ];

                $this->dom = $this->createEle($SummaryApplyListEle, $this->dom, $SummaryApplyList);

                $SummaryApplyListEle_arr[] = $SummaryApplyListEle;
            }

            //验证数据
            $this->checkInfo($SummaryApplyHeadEle, $SummaryApplyListEle_arr);
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
     * 定义验证器.
     */
    public function checkInfo($SummaryApplyHeadEle, $SummaryApplyListEle_arr)
    {
        $head_rules = [
            'ceb:copNo'         => 'require|max:20',
            'ceb:declAgentCode' => 'require|max:18',
            'ceb:declAgentName' => 'require|max:100',
            'ceb:summaryFlag'   => 'require|max:1',
            'ceb:itemNameFlag'  => 'require|max:1',
            'ceb:msgCount'      => 'require|max:20',
            'ceb:msgSeqNo'      => 'require|max:20',
            'ceb:customsCode'   => 'require|max:4',
            'ceb:agentCode'     => 'require|max:18',
            'ceb:agentName'     => 'require|max:100',
            'ceb:ebcCode'       => 'require|max:18',
            'ceb:ebcName'       => 'require|max:100',
        ];

        $list_rules = [
            'ceb:invtNo' => 'require|max:18',
        ];

        $this->credentialValidate->setRule($head_rules);

        if (!$this->credentialValidate->check($SummaryApplyHeadEle)) {
            throw new ClientError('报文数据错误: ' . $this->credentialValidate->getError());
        }

        $this->credentialValidate->setRule($list_rules);

        foreach ($SummaryApplyListEle_arr as $key => $value) {
            if (!$this->credentialValidate->check($value)) {
                throw new ClientError('报文数据错误: ' . $this->credentialValidate->getError());
            }
        }

        return true;
    }
}
