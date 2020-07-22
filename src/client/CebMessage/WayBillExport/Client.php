<?php

namespace customs\CustomsDeclareClient\CebMessage\WayBillExport;

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
    public function declare(array $declareConfig, array $declareParams)
    {
        $rule = [
            'DeclEntNo'    => 'require|max:18',
            'DeclEntName'  => 'require|max:100',
            'DeclEntDxpid' => 'require|max:30',

            'MessageId' => 'require|max:36',

            'OpType' => 'require|max:1',

            'CustomsCode' => 'require|max:4',

            'EHSEntNo'   => 'require|max:18',
            'EHSEntName' => 'require|max:100',
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
                'ceb:appType'        => $this->opType,
                'ceb:appTime'        => $this->sendTime,
                'ceb:appStatus'      => '2',
                'ceb:customsCode'    => $declareConfig['CustomsCode'],
                'ceb:copNo'          => $head['EntEListNo'],
                'ceb:agentCode'      => $declareConfig['DeclEntNo'],
                'ceb:agentName'      => $declareConfig['DeclEntName'],
                'ceb:loctNo'         => empty($head['LoctNo']) ? '' : $head['LoctNo'],
                'ceb:trafMode'       => $head['TrafMode'],
                'ceb:trafName'       => $head['TrafName'],
                'ceb:voyageNo'       => $head['VoyageNo'],
                'ceb:billNo'         => $head['BillNo'],
                'ceb:domesticTrafNo' => empty($head['DomesticTrafNo']) ? '' : $head['DomesticTrafNo'],
                'ceb:grossWeight'    => $head['GrossWeight'],
                'ceb:logisticsCode'  => $declareConfig['EHSEntNo'],
                'ceb:logisticsName'  => $declareConfig['EHSEntName'],
                'ceb:msgCount'       => $head['MsgCount'],
                'ceb:msgSeqNo'       => $head['MsgSeqNo'],
                'ceb:note'           => empty($head['note']) ? '' : $head['note'],
            ];

            $this->dom = $this->createEle($WayBillHeadEle, $this->dom, $WayBillHead);

            foreach ($list as $key => $value) {
                $WayBillList = $this->dom->createElement('ceb:WayBillList');
                $this->nodeLink['WayBill']->appendchild($WayBillList);

                $WayBillListEle = [
                    'ceb:gnum'           => $key + 1,
                    'ceb:totalPackageNo' => empty($value['TotalPackageNo']) ? '' : $value['TotalPackageNo'],
                    'ceb:logisticsNo'    => $value['EntWaybillNo'],
                    'ceb:note'           => empty($value['note']) ? '' : $value['note'],
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
            'ceb:copNo'       => 'require|max:20',
            'ceb:trafMode'    => 'require|max:20',
            'ceb:trafName'    => 'require|max:20',
            'ceb:voyageNo'    => 'require|max:20',
            'ceb:billNo'      => 'require|max:20',
            'ceb:grossWeight' => 'require|max:20',
            'ceb:msgCount'    => 'require|max:20',
            'ceb:msgSeqNo'    => 'require|max:20',
        ];

        $list_rules = [
            'ceb:gnum'        => 'require|max:20',
            'ceb:logisticsNo' => 'require|max:20',
        ];

        $this->credentialValidate->setRule($head_rules);

        if (!$this->credentialValidate->check($WayBillHeadEle)) {
            throw new ClientError('报文清单数据: ' . $this->credentialValidate->getError());
        }

        $this->credentialValidate->setRule($list_rules);

        foreach ($WayBillListEle_arr as $key => $value) {
            if (!$this->credentialValidate->check($value)) {
                throw new ClientError('报文清单数据: ' . $this->credentialValidate->getError());
            }
        }

        return true;
    }
}
