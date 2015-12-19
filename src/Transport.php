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

    /**
     * @param $method
     * @param $parameters
     *
     * @return array
     *
     * @throws TransportException
     */
    public function call($method, $parameters)
    {
        try {
            $ch = curl_init($this->base_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encode($method, $parameters));
            curl_setopt($ch, CURLOPT_TIMEOUT, 600);

            $response = curl_exec($ch);

            if ($response === false) {
                $e = new TransportException(curl_error($ch), curl_getinfo($ch, CURLINFO_HTTP_CODE));
                curl_close($ch);

                throw  $e;
            }

            curl_close($ch);

            return $this->decode($response);
        } catch (XmlrpcException $e) {
            throw new TransportException('XML parsing error', 0, $e);
        }
    }

    /**
     * Decodes response string into array.
     *
     * @param $response
     *
     * @return array
     *
     * @throws XmlrpcException
     */
    protected function decode($response)
    {
        $this->decoder = $this->decoder ?: new XmlrpcDecoder();

        return $this->decoder->decodeResponse($response);
    }

    /**
     * Encodes xmlrpc call into xml string.
     *
     * @param $method
     * @param array $parameters
     *
     * @return string
     *
     * @throws XmlrpcException
     */
    protected function encode($method, array $parameters = [])
    {
        $this->encoder = $this->encoder ?: new XmlrpcEncoder();

        return $this->encoder->encodeCall($method, $parameters);
    }
}
