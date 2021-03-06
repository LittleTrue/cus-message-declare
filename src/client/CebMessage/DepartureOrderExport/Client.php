<?php

namespace customs\CustomsDeclareClient\CebMessage\DepartureOrderExport;

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
    public $messageType = 'CEB509Message';

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
     * 出口离境单.
     */
    public function generateXmlPost(array $declareConfig, array $declareParams)
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
            $Departure = $this->dom->createElement('ceb:Departure');
            $this->nodeLink['root_node']->appendchild($Departure);
            $this->nodeLink['Departure'] = $Departure;

            //一个清单一个清单头
            $DepartureHead = $this->dom->createElement('ceb:DepartureHead');
            $this->nodeLink['Departure']->appendchild($DepartureHead);

            $DepartureHeadEle = [
                'ceb:guid'          => $declareConfig['MessageId'],
                'ceb:appType'       => $this->opType,
                'ceb:appTime'       => $this->sendTime,
                'ceb:appStatus'     => '2',
                'ceb:customsCode'   => $declareConfig['CustomsCode'],
                'ceb:copNo'         => $head['EntEListNo'],
                'ceb:logisticsCode' => $declareConfig['EHSEntNo'],
                'ceb:logisticsName' => $declareConfig['EHSEntName'],
                'ceb:trafMode'      => $head['TrafMode'],
                'ceb:trafName'      => empty($head['TrafName']) ? '' : $head['TrafName'],
                'ceb:voyageNo'      => empty($head['VoyageNo']) ? '' : $head['VoyageNo'],
                'ceb:billNo'        => empty($head['BillNo']) ? '' : $head['BillNo'],
                'ceb:leaveTime'     => $this->sendTime,
                'ceb:msgCount'      => $head['MsgCount'],
                'ceb:msgSeqNo'      => $head['MsgSeqNo'],
                'ceb:note'          => empty($head['note']) ? '' : $head['note'],
            ];

            $this->dom = $this->createEle($DepartureHeadEle, $this->dom, $DepartureHead);

            foreach ($list as $key => $value) {
                $DepartureList = $this->dom->createElement('ceb:DepartureList');
                $this->nodeLink['Departure']->appendchild($DepartureList);

                $DepartureListEle = [
                    'ceb:gnum'           => $key + 1,
                    'ceb:totalPackageNo' => empty($value['TotalPackageNo']) ? '' : $value['TotalPackageNo'],
                    'ceb:logisticsNo'    => $value['EntWaybillNo'],
                    'ceb:note'           => empty($value['note']) ? '' : $value['note'],
                ];

                $this->dom = $this->createEle($DepartureListEle, $this->dom, $DepartureList);

                $DepartureListEle_arr[] = $DepartureListEle;
            }

            //验证数据
            $this->checkInfo($DepartureHeadEle, $DepartureListEle_arr);
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
    public function checkInfo($DepartureHeadEle, $DepartureListEle_arr)
    {
        $head_rules = [
            'ceb:copNo'    => 'require|max:20',
            'ceb:trafMode' => 'require|max:20',
            'ceb:msgCount' => 'require|max:20',
            'ceb:msgSeqNo' => 'require|max:20',
        ];

        $list_rules = [
            'ceb:gnum'        => 'require|max:20',
            'ceb:logisticsNo' => 'require|max:20',
        ];

        $this->credentialValidate->setRule($head_rules);

        if (!$this->credentialValidate->check($DepartureHeadEle)) {
            throw new ClientError('报文清单数据: ' . $this->credentialValidate->getError());
        }

        $this->credentialValidate->setRule($list_rules);

        foreach ($DepartureListEle_arr as $key => $value) {

            if (!$this->credentialValidate->check($value)) {
                throw new ClientError('报文清单数据: ' . $this->credentialValidate->getError());
            }
        }

        return true;
    }
}
