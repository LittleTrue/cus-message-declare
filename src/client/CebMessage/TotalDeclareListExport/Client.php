<?php

namespace customs\CustomsDeclareClient\CebMessage\TotalDeclareListExport;

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
    public $messageType = 'CEB607Message';

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
     * 出口清单总分单.
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
            $WayBill = $this->dom->createElement('ceb:WayBill');
            $this->nodeLink['root_node']->appendchild($WayBill);
            $this->nodeLink['WayBill'] = $WayBill;

            //一个清单一个清单头
            $WayBillHead = $this->dom->createElement('ceb:WayBillHead');
            $this->nodeLink['WayBill']->appendchild($WayBillHead);

            $WayBillHeadEle = [
                'ceb:guid'           => $declareConfig['MessageId'],
                'ceb:appType'        => $declareConfig['OpType'],
                'ceb:appTime'        => $this->sendTime,
                'ceb:appStatus'      => $declareConfig['appStatus'],
                'ceb:customsCode'    => $head['customs_code'],
                'ceb:copNo'          => $head['cop_no'],
                'ceb:preNo'          => isset($head['preNo']) ? $head['preNo'] : '',
                'ceb:agentCode'      => $head['agent_code'],
                'ceb:agentName'      => $head['agent_name'],
                'ceb:loctNo'         => empty($head['loct_no']) ? '' : $head['loct_no'],
                'ceb:trafMode'       => $head['traf_mode'],
                'ceb:trafName'       => $head['traf_name'],
                'ceb:voyageNo'       => $head['voyage_no'],
                'ceb:billNo'         => $head['bill_no'],
                'ceb:domesticTrafNo' => empty($head['domestic_traf_no']) ? '' : $head['domestic_traf_no'],
                'ceb:grossWeight'    => $head['gross_weight'],
                'ceb:logisticsCode'  => $head['EHSEntNo'],
                'ceb:logisticsName'  => $head['EHSEntName'],
                'ceb:msgCount'       => $head['msg_count'],
                'ceb:msgSeqNo'       => $head['msg_seq_no'],
                'ceb:note'           => empty($head['note']) ? '' : $head['note'],
            ];

            if (1 == $declareConfig['OpType']) {
                //增加变更/删除申报字段
                unset($InventoryHeadEle['ceb:preNo']);
            }

            $this->dom = $this->createEle($WayBillHeadEle, $this->dom, $WayBillHead);

            foreach ($list as $k => $v) {
                $WayBillList = $this->dom->createElement('ceb:WayBillList');
                $this->nodeLink['WayBill']->appendchild($WayBillList);

                $WayBillListEle = [
                    'ceb:gnum'           => $k + 1,
                    'ceb:totalPackageNo' => $v['total_package_no'],
                    'ceb:logisticsNo'    => $v['logistics_no'],
                    'ceb:note'           => empty($v['note']) ? '' : $v['note'],
                ];

                $this->dom = $this->createEle($WayBillListEle, $this->dom, $WayBillList);

                $WayBillListEle_arr[] = $WayBillListEle;
            }

            //验证数据
            $this->checkInfo($WayBillHeadEle, $WayBillListEle_arr);
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
    public function checkInfo($WayBillHeadEle, $WayBillListEle_arr)
    {
        $head_rules = [
            'ceb:customsCode'   => 'require|max:4',
            'ceb:copNo'         => 'require|max:30',
            'ceb:agentCode'     => 'require|max:18',
            'ceb:agentName'     => 'require|max:100',
            'ceb:trafMode'      => 'require|max:1',
            'ceb:trafName'      => 'require|max:100',
            'ceb:voyageNo'      => 'require|max:32',
            'ceb:billNo'        => 'require|max:37',
            'ceb:grossWeight'   => 'require|number',
            'ceb:logisticsCode' => 'require|max:18',
            'ceb:logisticsName' => 'require|max:100',
            'ceb:msgCount'      => 'require|number',
            'ceb:msgSeqNo'      => 'require|number',
        ];

        $list_rules = [
            'ceb:gnum'        => 'require|number',
            'ceb:logisticsNo' => 'require|max:80',
        ];

        $this->credentialValidate->setRule($head_rules);

        if (!$this->credentialValidate->check($WayBillHeadEle)) {
            throw new ClientError('报文数据错误: ' . $this->credentialValidate->getError());
        }

        $this->credentialValidate->setRule($list_rules);

        foreach ($WayBillListEle_arr as $key => $value) {
            if (!$this->credentialValidate->check($value)) {
                throw new ClientError('报文数据错误: ' . $this->credentialValidate->getError());
            }
        }

        return true;
    }
}
