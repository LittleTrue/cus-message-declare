<?php

namespace customs\CustomsDeclareClient\GzWindowKjMessage\HttpMessageDeclare;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\BaseClient;

/**
 * 单一窗口HTTP申报通路报文封装 客户端.
 */
class Client extends BaseClient
{
    /**
     * @var RsaSignClient 加签客户端依赖
     */
    private $_rsaClient;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->_rsaClient = $app['rsa'];
    }

    //生成按照单一窗口HTTP申报通路封装的报文
    public function generateHttpDoc($messageType, $xml, $baseConfig, $key = '')
    {
        $data              = base64_encode($xml);
        $ctime             = date('YmdHis');
        $doc               = new \DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;
        $guid              = $this->getUid($messageType, $baseConfig['Sender']);
        $root              = $doc->createElement('GzeportTransfer');
        $root->setAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $root->setAttribute('xmlns:n1', 'http://www.altova.com/samplexml/other-namespace');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $doc->appendChild($root);
        $head = [
            'Head' => [
                'MessageID'   => $guid,
                'MessageType' => $messageType,
                'Sender'      => $baseConfig['Sender'],
                'Receivers'   => [
                    'Receiver' => $baseConfig['Receiver'],
                ],
                'SendTime' => $ctime,
                'Version'  => '1.0',
                'FileName' => $baseConfig['FileName'],
            ],
            'Data' => $data,
        ];
        $obj                = ['obj' => $head];
        $xml                = $this->arrayToXml($obj);
        $doc1               = new \DOMDocument();
        $doc1->formatOutput = true;
        $xmlNode            = $doc1->loadXML($xml);
        $headNode           = $doc1->getElementsByTagName('Head')->item(0);
        $headDom            = $doc->importNode($headNode, true);
        $dataNode           = $doc1->getElementsByTagName('Data')->item(0);
        $dataDom            = $doc->importNode($dataNode, true);
        $doc->documentElement->appendChild($headDom);
        $doc->documentElement->appendChild($dataDom);

        if (!empty($key)) {
            return $this->_rsaClient->sign($key, $doc->saveXML());
        }
        return $doc->saveXML();
    }

    //生成HTTP申报报文messageID
    private function getUid($messageType, $sender)
    {
        return $messageType . '_' . $sender . '_' . date('YmdHis') . rand(10000, 99999);
    }

    /**
     * 数组转xml.
     */
    private function arrayToXml($arr)
    {
        $str = '';
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $vkey = key($v);
                if (is_numeric($vkey)) {
                    foreach ($v as $key => $val) {
                        $str .= "<{$k}>";
                        $str .= $this->arrayToXml($val);
                        $str .= "</{$k}>";
                    }
                } else {
                    $tmp = $this->arrayToXml($v);
                    $str .= "<{$k}>{$tmp}</{$k}>";
                }
            } else {
                $str .= "<{$k}>";
                $str .= $v;
                $str .= "</{$k}>";
            }
        }
        return $str;
    }
}
