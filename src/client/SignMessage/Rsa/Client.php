<?php

namespace customs\CustomsDeclareClient\SignMessage\Rsa;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\BaseClient;
use customs\CustomsDeclareClient\SignMessage\Rsa\XMLSecurityDSig;
use customs\CustomsDeclareClient\SignMessage\Rsa\XMLSecurityKey;

/**
 * 客户端.
 */
class Client extends BaseClient
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * 报文加签
     */
    public function sign($key,$xml)
    {
        $doc = new \DOMDocument();
        $doc->formatOutput = true;
        $doc->loadXML($xml);
        $objDSig = new XMLSecurityDSig('ds');
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        $objDSig->addReference(
            $doc,
            XMLSecurityDSig::SHA1,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature'],
            ['force_uri' => true]
        );

        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, ['type' => 'private']);
        // $private_path = $this->keyFolder;
        $objKey->loadKey($key, false);
      
        $objDSig->sign($objKey, $doc->documentElement);
    
        var_dump($doc->saveXML());
        die();
        // return $doc->saveHTML();
    }
}
