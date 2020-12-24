<?php

require_once __DIR__ . '/vendor/autoload.php';

use customs\CustomsDeclareClient\Application;
use customs\CustomsTradePostFactory;

$ioc_con_app = new Application();

//---------------------------------------------------------
//出口总分单申报
//---------------------------------------------------------
// $declareConfig = [
//     'OpType'       => '1',
//     'appStatus'    => '1',
//     'DeclEntNo'    => '1101180326',
//     'DeclEntName'  => '物流企业',
//     'DeclEntDxpid' => 'Dxp',
//     'MessageId'    => '4CDE1CFD-EDED-46B1-946C-B8022E42FC94',
// ];

// $goods_info1['head'] = [
//     'customs_code'     => 'test',
//     'cop_no'           => 'test',
//     'agent_code'       => 'test',
//     'agent_name'       => 'test',
//     'loct_no'          => 'test',
//     'traf_mode'        => 'test',
//     'traf_name'        => 'test',
//     'voyage_no'        => 'test',
//     'bill_no'          => 'test',
//     'domestic_traf_no' => 'test',
//     'gross_weight'     => 'test',
//     'EHSEntNo'         => 'test',
//     'EHSEntName'       => 'test',
//     'msg_count'        => 'test',
//     'msg_seq_no'       => 'test',
//     'note'             => 'test',
// ];

// $goods_info1['list'] = [
//     [
//         'total_package_no' => '22',
//         'logistics_no'     => '33',
//         'note'             => '44',
//     ],
//     [
//         'total_package_no' => '22',
//         'logistics_no'     => '33',
//         'note'             => '44',
//     ],
//     [
//         'total_package_no' => '22',
//         'logistics_no'     => '33',
//         'note'             => '44',
//     ],
// ];

// $declareParams[] = $goods_info1;
// $declareParams[] = $goods_info1;
// $declareParams[] = $goods_info1;

// $key = file_get_contents('E:\KK\Job\控制中心\private_key.pem');

// $httpBase = [
//     'Sender'   => 'TEST17',
//     'Receiver' => [
//         'TEST17',
//     ],
//     'MessageId' => 'WWWW',
//     'FileName'  => '4CDE1CFD-EDED-46B1-946C-B8022E42FC94',
// ];

//出口电子订单申报
// $customsTradePostFactory = new CustomsTradePostFactory();
// $TotalDeclareList        = $customsTradePostFactory->getInstance('TotalDeclareListExportService', $ioc_con_app);
// $test_xml                = $TotalDeclareList->generateXmlPost($declareConfig, $declareParams);
// $test_xml2               = $TotalDeclareList->generateHttpDoc($declareConfig, $declareParams, $httpBase, $key);

// var_dump($test_xml2);
// die();

//---------------------------------------------------------
//汇总申请单
//---------------------------------------------------------
// $declareConfig = [
//     'OpType'       => '1',
//     'appStatus'    => '1',
//     'DeclEntNo'    => '1101180326',
//     'DeclEntName'  => '物流企业',
//     'DeclEntDxpid' => 'Dxp',
//     'MessageId'    => '4CDE1CFD-EDED-46B1-946C-B8022E42FC94',
// ];

// $goods_info1['head'] = [
//     'CustomsCode'   => 'test',
//     'EntEListNo'    => 'test',
//     'DeclEntNo'     => 'test',
//     'DeclEntName'   => 'test',
//     'EBEntNo'       => 'test',
//     'EBEntName'     => 'test',
//     'DeclAgentCode' => 'test',
//     'DeclAgentName' => 'test',
//     'SummaryFlag'   => 'test',
//     'ItemNameFlag'  => 'test',
//     'MsgCount'      => 'test',
//     'MsgSeqNo'      => 'test',
// ];

// $goods_info1['list'] = [
//     [
//         'InvtNo' => '2',
//     ],
//     [
//         'InvtNo' => '3',
//     ],
//     [
//         'InvtNo' => '4',
//     ],
// ];

// $declareParams[] = $goods_info1;
// $declareParams[] = $goods_info1;
// $declareParams[] = $goods_info1;

// $key = file_get_contents('E:\KK\Job\控制中心\private_key.pem');

// $httpBase = [
//     'Sender'   => 'TEST17',
//     'Receiver' => [
//         'TEST17',
//     ],
//     'MessageId' => 'WWWW',
//     'FileName'  => '4CDE1CFD-EDED-46B1-946C-B8022E42FC94',
// ];

// //出口电子订单申报
// $customsTradePostFactory = new CustomsTradePostFactory();
// $SummaryBill             = $customsTradePostFactory->getInstance('SummaryBillExportService', $ioc_con_app);
// // $test_xml                = $SummaryBill->generateXmlPost($declareConfig, $declareParams);
// $test_xml2               = $SummaryBill->generateHttpDoc($declareConfig, $declareParams, $httpBase, $key);

// var_dump($test_xml2);
// die();

//---------------------------------------------------------
//运单申报
//---------------------------------------------------------
// $declareConfig = [
//     'MessageID'    => '3B732E4365548724E050C20A91812E69',
//     'FunctionCode' => '0',
//     'MessageType'  => '511',
//     'SenderID'     => '1608',
//     'ReceiverID'   => 'EMS',
//     'SendTime'     => date('Y-m-d H:i:s', time()),
//     'Version'      => '1.0',
// ];

// $freight = [
//     'appType'            => '1',
//     'appTime'            => '20201016204530',
//     'appStatus'          => '2',
//     'logisticsCode'      => '460118Z054',
//     'logisticsName'      => '中国邮政速递物流股份有限公司海南省分公司',
//     'logisticsNo'        => '',
//     'billNo'             => '*',
//     'freight'            => '0',
//     'insuredFee'         => '0',
//     'currency'           => '142',
//     'weight'             => '0',
//     'packNo'             => '1',
//     'goodsInfo'          => '',
//     'consignee'          => 'test',
//     'consigneeAddress'   => 'test',
//     'consigneeTelephone' => 'test',
//     'note'               => 'test',
//     'orderNo'            => 'test',
//     'ebpCode'            => 'test',
//     'KzInfo'             => [
//         'shipper'           => 'test',
//         'shipperAddress'    => 'test',
//         'shipperTelephone'  => 'test',
//         'consigneeProvince' => 'test',
//         'consigneeCity'     => 'test',
//         'consigneeCounty'   => 'test',
//         'mailType'          => '9',
//     ]
// ];

// $freight_arr[] = $freight;
// $freight_arr[] = $freight;

// $declareParams = $freight_arr;
// $httpBase = [
//     'Sender'   => 'TEST17',
//     'Receiver' => [
//         'TEST17',
//     ],
//     'MessageId' => 'WWWW',
//     'FileName'  => '4CDE1CFD-EDED-46B1-946C-B8022E42FC94',
// ];

// //出口电子订单申报
// $customsTradePostFactory = new CustomsTradePostFactory();
// $obj = $customsTradePostFactory->getInstance('EmsLogisticsDeclareService', $ioc_con_app);
// try {
//     $test_xml = $obj->generateXmlPost($declareConfig, $declareParams);var_dump($test_xml);
//     die();
// } catch (Exception $e) {
//     // var_dump($e);
// }
// $test_xml2 = $SummaryBill->generateHttpDoc($declareConfig, $declareParams, $httpBase, $key);

// // var_dump($test_xml2);
// die();

//---------------------------------------------------------
//运单申报
//---------------------------------------------------------
// $declareConfig = [
//     'MessageID'    => '3B732E4365548724E050C20A91812E69',
//     'FunctionCode' => '0',
//     'MessageType'  => '511',
//     'SenderID'     => '1608',
//     'ReceiverID'   => 'EMS',
//     'SendTime'     => date('Y-m-d H:i:s', time()),
//     'Version'      => '1.0',
//     'opt_type'      => 'logistics_check'
// ];

// $freight_nos = ['123','456'];

// $declareParams = $freight_nos;
// // $httpBase = [
// //     'Sender'   => 'TEST17',
// //     'Receiver' => [
// //         'TEST17',
// //     ],
// //     'MessageId' => 'WWWW',
// //     'FileName'  => '4CDE1CFD-EDED-46B1-946C-B8022E42FC94',
// // ];

// //出口电子订单申报
// $customsTradePostFactory = new CustomsTradePostFactory();
// $obj = $customsTradePostFactory->getInstance('EmsLogisticsDeclareService', $ioc_con_app);
// try {
//     $test_xml = $obj->generateXmlPost($declareConfig, $declareParams);var_dump($test_xml);die();
//     die();
// } catch (Exception $e) {
//     // var_dump($e);
// }
// $test_xml2 = $SummaryBill->generateHttpDoc($declareConfig, $declareParams, $httpBase, $key);

// var_dump($test_xml2);

// die();
// $declareConfig = [
//     'MessageID'    => '3B732E4365548724E050C20A91812E69',
//     'FunctionCode' => '0',
//     'MessageType'  => '511',
//     'SenderID'     => '1608',
//     'ReceiverID'   => 'EMS',
//     'SendTime'     => date('Y-m-d H:i:s', time()),
//     'Version'      => '1.0',
//     'opt_type'      => 'logistics_check'
// ];

// $freight_nos = ['123','456'];

// $declareParams = $freight_nos;
// // $httpBase = [
// //     'Sender'   => 'TEST17',
// //     'Receiver' => [
// //         'TEST17',
// //     ],
// //     'MessageId' => 'WWWW',
// //     'FileName'  => '4CDE1CFD-EDED-46B1-946C-B8022E42FC94',
// // ];

//出口电子订单申报
// $ioc_con_app = new Application();

// $customsTradePostFactory = new CustomsTradePostFactory();
// $obj = $customsTradePostFactory->getInstance('ChecklistCrossService', $ioc_con_app);//var_dump($obj);
// try {
//     $declareConfig = [
//         'PreNo'      => 'max:50',
//         'CusEListNo' => 'max:18',
//         'EBPEntNo'   => 'require|max:50',
//         'EBPEntName' => 'require|max:100',
//         'EBEntNo'   => 'require|max:18',
//         'EBEntName' => 'require|max:100',
//         'EHSEntNo'   => 'require|max:18',
//         'EHSEntName' => 'require|max:100',
//         'DanBaoEntNo' => 'require|max:30',
//         'CustomsCode' => 'require|max:4',
//         'IEPort'      => 'require|max:4',
//         'TradeMode' => 'require|max:4',
//         'SvPCode'   => 'require|max:10',
//         'DeclEntNo'    => 'require|max:18',
//         'DeclEntName'  => 'require|max:100',
//         'DeclEntDxpid' => 'require|max:30',
//         'MessageId' => 'require|max:36',
//         'OpType' => 'require|max:1',
//     ];

//     $declareParams = [
//         'ss'=> 's'
//     ];

//     $test_xml = $obj->generateXmlPost($declareConfig, $declareParams);var_dump($test_xml);die();
//     die();
// } catch (Exception $e) {
//     // var_dump($e);
// }

//---------------------------------------------------------
//顺丰运单申报
//---------------------------------------------------------
// $declareConfig = [
//     'head'     => 'OSMS_1',
//     'opt_type' => 'logistics_declare',
// ];

// $declareParams = [
//     'reference_no1'   => '2012229135144211',
//     'is_gen_bill_no'    => 1,

//     'j_contact'       => 'CustomerService',
//     'j_tel'           => '9516168888',
//     'j_country'      => 'CN',
//     'j_province'      => '海南省',
//     'j_city'       => 'US',
//     'j_address'       => '寄方',
//     'j_post_code'     => '98888',

//     'd_contact'       => '李顺丰',
//     'd_tel'           => '13312908888',
//     'd_country'       => 'CN',
//     'd_province'      => '海南省',
//     'd_city'       => 'US',
//     'd_post_code'     => '518000',
//     'd_address'       => '到方',

//     'tax_pay_type'    => '1',
//     'express_type'    => '101',
//     'parcel_quantity' => '1',
//     'pay_method'      => '1',
//     'custid'          => '0010002117',
//     'currency'        => 'USD',

//     'operate_type' => 1,
//     'Cargo'           => [
//         [
//             'name'              => 'JustaLeafOrganicTea;有机草本茶;芙蓉柠檬2oz(56g)',
//             'count'             => '2',
//             'unit'              => '件',
//             'amount'            => '5.88',
//             'source_area'       => 'USA',
//         ],
//         [
//             'name'              => 'JustaLeafOrganicTea;有机草本茶;芙蓉柠檬2oz(56g)',
//             'count'             => '2',
//             'unit'              => '件',
//             'amount'            => '5.88',
//             'source_area'       => 'USA',
//         ],
//     ],
// ];

// // //出口电子订单申报
// $customsTradePostFactory = new CustomsTradePostFactory();
// $obj                     = $customsTradePostFactory->getInstance('SFLogisticsDeclareService', $ioc_con_app);
// try {
//     $test_xml = $obj->generateXmlPost($declareConfig, $declareParams);
//     $sign = base64_encode(md5($test_xml . 'fc34c561a34f'));
//     // print_r($test_xml);die();
//     $ele = [
//         'validateStr' => $sign,
//         'customerCode' => 'OSMS_1'
//     ];
//     $r = sendOut('http://osms.sit.sf-express.com:2080/osms/services/OrderWebService?wsdl', 'apiOrderService', base64_encode($test_xml), $ele);
//     var_dump($r);
//     die();
// } catch (Exception $e) {
//     // var_dump($e);
// }
// $test_xml2 = $SummaryBill->generateHttpDoc($declareConfig, $declareParams, $httpBase, $key);

// // var_dump($test_xml2);
// die();


// 结果查询
$declareConfig = [
    'head'     => 'OSMS_1',
    'opt_type' => 'logistics_check',
];

$declareParams = [
    'orderid' => '2012229135144211'
];

// $httpBase      = [
//     'Sender'   => 'TEST17',
//     'Receiver' => [
//         'TEST17',
//     ],
//     'MessageId' => 'WWWW',
//     'FileName'  => '4CDE1CFD-EDED-46B1-946C-B8022E42FC94',
// ];

//出口电子订单申报
$customsTradePostFactory = new CustomsTradePostFactory();
$obj                     = $customsTradePostFactory->getInstance('SFLogisticsDeclareService', $ioc_con_app);
try {
    $test_xml = $obj->generateXmlPost($declareConfig, $declareParams);//print_r($test_xml);die();
    $sign = base64_encode(md5($test_xml . 'fc34c561a34f'));
    $ele = [
        'validateStr' => $sign,
        'customerCode' => 'OSMS_1'
    ];
    $r = sendOut('http://osms.sit.sf-express.com:2080/osms/services/OrderWebService?wsdl', 'apiOrderService', base64_encode($test_xml), $ele);;
    var_dump($r);
    die();
} catch (\Exception $e) {
    var_dump($e->getMessage());
}
// $test_xml2 = $SummaryBill->generateHttpDoc($declareConfig, $declareParams, $httpBase, $key);

// var_dump($test_xml2);
die();







//发送消息
function sendOut($url, $func, $data, $info)
{
    $soap = new \SoapClient($url);
// var_dump($soap);die();
    switch ($func) {
        // case 'queryMailTrack':
        //     $param = ['in0' => $info['user'], 'in1' => $info['pwd'], 'in2' => $info['mailNo']];
        //     $response = $soap->queryMailTrack($param);
        //     break;
        case 'apiOrderService':
            $param = ['data' => $data, 'validateStr' => $info['validateStr'], 'customerCode' => $info['customerCode']];
            // print_r($info['validateStr']);die();
            $response = $soap->sfexpressService($param);
        default:
            break;
    }
    return $response->Return;
}