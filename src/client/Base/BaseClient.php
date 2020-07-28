<?php

namespace customs\CustomsDeclareClient\Base;

use customs\CustomsDeclareClient\Application;

/**
 * 底层请求.
 */
class BaseClient
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $json = [];

    /**
     * @var string
     */
    protected $language = 'zh-cn';

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * 获取特定位数时间戳.
     * @return int
     */
    public function getTimestamp($digits = 10)
    {
        $digits = $digits > 10 ? $digits : 10;

        $digits = $digits - 10;

        if ((!$digits) || (10 == $digits)) {
            return time();
        }

        return number_format(microtime(true), $digits, '', '') - 50000;
    }

    /**
     * 浮点数比较规则.
     * @return int
     */
    public function floatCmp($f1, $f2, $precision = 10)
    {
        $e = pow(10, $precision);
        $i1 = intval($f1 * $e);
        $i2 = intval($f2 * $e);
        return $i1 == $i2;
    }

    //生成http申报报文
    public function genarteDoc($messageType, $xml, $base)
    {
        $data = base64_encode($xml);
        $ctime = date('YmdHis');
        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;
        // $this->guid = $this->getUid($messageType);
        $guid = $this->getUid($messageType);
        $edi = $base['edi'];
        $root = $doc->createElement('GzeportTransfer');
        $root->setAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $root->setAttribute('xmlns:n1', 'http://www.altova.com/samplexml/other-namespace');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $doc->appendChild($root);
        $head = [
            'Head' => [
                'MessageID'   => $guid,
                'MessageType' => $messageType,
                'Sender'      => $edi,
                'Receivers'   => [
                    'Receiver' => $base['Receiver'],
                ],
                'SendTime' => $ctime,
                'Version'  => '1.0',
                'FileName' => $base['FileName'],
            ],
            'Data' => $data,
        ];
        $obj = ['obj' => $head];
        $xml = $this->arrayToXml($obj);
        $doc1 = new \DOMDocument();
        $doc1->formatOutput = true;
        $xmlNode = $doc1->loadXML($xml);
        $headNode = $doc1->getElementsByTagName('Head')->item(0);
        $headDom = $doc->importNode($headNode, true);
        $dataNode = $doc1->getElementsByTagName('Data')->item(0);
        $dataDom = $doc->importNode($dataNode, true);
        $doc->documentElement->appendChild($headDom);
        $doc->documentElement->appendChild($dataDom);
        return $doc->saveXML();
    }

    /**
     * 数组转xml
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

    //生车messageID
    private function getUid($messageType)
    {
        $edi = $this->edi;
        return $messageType . '_' . $edi . '_' . date('YmdHis') . rand(10000, 99999);
    }
}
