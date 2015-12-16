<?php

namespace Enl\MosesClient;

class Client
{
    /** @var string */
    private $base_url;

    /** @var XmlrpcProtocol */
    private $protocol;

    /**
     * @param XmlrpcProtocol $protocol
     * @param string $base_url Server address, i.e. 'http://moses-server.ltd:8080'
     */
    public function __construct(XmlrpcProtocol $protocol, $base_url)
    {
        $this->protocol = $protocol;
        $this->base_url = $base_url;
    }

    /**
     * @param string $text    Text to translate
     * @param array  $options . Possible options are:
     *                        * align. Moses specific parameter. Please consider to read its docs. By default, is false.
     *                        * report-all-factors. Moses specific parameter. Please consider to read its docs. By default, is false.
     *                        * return-text. Whether to return only translated text or the whole decoded response.
     * @return string|array
     */
    public function translate($text, array $options = [])
    {
        $options = array_merge([
            'align'              => false,
            'report-all-factors' => false,
            'return-text'        => true
        ], $options);

        $response = $this->curl('translate', [$this->createRequestParameters($text, $options)]);

        // Moses returns the only parameter so that we can delete wrapper
        $decoded = $this->protocol->decode($response)[0];

        return $options['return-text'] ? $decoded['text'] : $decoded;
    }

    /**
     * @param string $method
     * @param array  $params
     * @return string
     */
    protected function curl($method, $params)
    {
        $ch = curl_init($this->base_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->protocol->encode($method, $params));
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);

        return curl_exec($ch);
    }

    private function createRequestParameters($text, $options)
    {
        return [
            'text'               => $text,
            'align'              => $options['align'],
            'report-all-factors' => $options['report-all-factors']
        ];
    }
}
