<?php

namespace Enl\MosesClient;

use Comodojo\Xmlrpc\XmlrpcDecoder;
use Comodojo\Xmlrpc\XmlrpcEncoder;

class Client
{
    /** @var string */
    private $base_url;

    /**
     * @var XmlrpcEncoder
     */
    private $encoder;

    /**
     * @var XmlrpcDecoder
     */
    private $decoder;

    /**
     * @param string $base_url Server address, i.e. 'http://moses-server.ltd:8080'
     */
    public function __construct($base_url)
    {
        $this->base_url = $base_url;
    }

    /**
     * @return XmlrpcEncoder
     */
    public function getEncoder()
    {
        if (is_null($this->encoder)) {
            $this->encoder = new XmlrpcEncoder();
        }

        return $this->encoder;
    }

    /**
     * @return XmlrpcDecoder
     */
    public function getDecoder()
    {
        if (is_null($this->decoder)) {
            $this->decoder = new XmlrpcDecoder();
        }

        return $this->decoder;
    }

    /**
     * @param string $text Text to translate
     * @param bool $align Refer to Moses docs what does it mean
     * @param bool $reportAllFactors Refer to Moses docs what does it mean
     * @param bool $returnOnlyText If set function will parse response and return just translated text. Otherwise, the whole data structure as is.
     * @return string|array
     * @throws \Comodojo\Exception\XmlrpcException
     * @throws \Exception
     */
    public function translate($text, $align = false, $reportAllFactors = false, $returnOnlyText = true)
    {
        $response = $this->curl('translate', [[
            'text' => $text,
            'align' => $align,
            'report-all-factors' => $reportAllFactors
        ]]);

        // Moses returns the only parameter so that we can delete wrapper
        $decoded = $this->getDecoder()->decodeResponse($response)[0];

        return $returnOnlyText ? $decoded['text'] : $decoded;
    }

    /**
     * @param string $method
     * @param array $params
     * @return string
     */
    protected function curl($method, $params)
    {
        $ch = curl_init($this->base_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getEncoder()->encodeCall($method, $params));

        return curl_exec($ch);
    }
}