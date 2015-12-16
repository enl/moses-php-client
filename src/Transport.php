<?php


namespace Enl\MosesClient;


use Comodojo\Exception\XmlrpcException;
use Comodojo\Xmlrpc\XmlrpcDecoder;
use Comodojo\Xmlrpc\XmlrpcEncoder;
use Enl\MosesClient\Exception\TransportException;

class Transport
{
    /**
     * @var XmlrpcDecoder
     */
    private $decoder;

    /**
     * @var XmlrpcEncoder
     */
    private $encoder;

    /**
     * @var string
     */
    private $base_url;

    public function __construct($base_url)
    {
        $this->base_url = $base_url;
    }


    public function call($method, $parameters)
    {
        $ch = curl_init($this->base_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encode($method, $parameters));
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);

        $result = curl_exec($ch);

        if ($result === false) {
            $e = new TransportException(curl_error($ch), curl_getinfo($ch, CURLINFO_HTTP_CODE));
            curl_close($ch);

            throw  $e;
        }


        curl_close($ch);
        return $this->decode($result);
    }

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
