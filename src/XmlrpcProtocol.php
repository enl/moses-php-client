<?php


namespace Enl\MosesClient;


use Comodojo\Exception\XmlrpcException;
use Comodojo\Xmlrpc\XmlrpcDecoder;
use Comodojo\Xmlrpc\XmlrpcEncoder;

class XmlrpcProtocol
{
    /**
     * @var XmlrpcDecoder
     */
    private $decoder;

    /**
     * @return XmlrpcDecoder
     */
    private function getDecoder()
    {
        return $this->decoder ?: $this->decoder = new XmlrpcDecoder();
    }

    /**
     * Decodes response string into array
     *
     * @param $response
     * @return array
     * @throws XmlrpcException
     */
    public function decode($response)
    {
        return $this->getDecoder()->decodeResponse($response);
    }

    /**
     * @var XmlrpcEncoder
     */
    private $encoder;

    /**
     * @return XmlrpcEncoder
     */
    private function getEncoder()
    {
        return $this->encoder ?: $this->encoder = new XmlrpcEncoder();
    }

    /**
     * Encodes xmlrpc call into xml string
     *
     * @param $method
     * @param array $parameters
     * @return string
     * @throws XmlrpcException
     */
    public function encode($method, array $parameters = [])
    {
        return $this->getEncoder()->encodeCall($method, $parameters);
    }
}
