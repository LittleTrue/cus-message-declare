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
$declareConfig = [
    'OpType'       => '1',
    'appStatus'    => '1',
    'DeclEntNo'    => '1101180326',
    'DeclEntName'  => '物流企业',
    'DeclEntDxpid' => 'Dxp',
    'MessageId'    => '4CDE1CFD-EDED-46B1-946C-B8022E42FC94',
];

$goods_info1['head'] = [
    'CustomsCode'   => 'test',
    'EntEListNo'    => 'test',
    'DeclEntNo'     => 'test',
    'DeclEntName'   => 'test',
    'EBEntNo'       => 'test',
    'EBEntName'     => 'test',
    'DeclAgentCode' => 'test',
    'DeclAgentName' => 'test',
    'SummaryFlag'   => 'test',
    'ItemNameFlag'  => 'test',
    'MsgCount'      => 'test',
    'MsgSeqNo'      => 'test',
];

$goods_info1['list'] = [
    [
        'InvtNo' => '2',
    ],
    [
        'InvtNo' => '3',
    ],
    [
        'InvtNo' => '4',
    ],
];

$declareParams[] = $goods_info1;
$declareParams[] = $goods_info1;
$declareParams[] = $goods_info1;

$key = file_get_contents('E:\KK\Job\控制中心\private_key.pem');

$httpBase = [
    'Sender'   => 'TEST17',
    'Receiver' => [
        'TEST17',
    ],
    'MessageId' => 'WWWW',
    'FileName'  => '4CDE1CFD-EDED-46B1-946C-B8022E42FC94',
];

//出口电子订单申报
$customsTradePostFactory = new CustomsTradePostFactory();
$SummaryBill             = $customsTradePostFactory->getInstance('SummaryBillExportService', $ioc_con_app);
// $test_xml                = $SummaryBill->generateXmlPost($declareConfig, $declareParams);
$test_xml2               = $SummaryBill->generateHttpDoc($declareConfig, $declareParams, $httpBase, $key);

var_dump($test_xml2);
die();