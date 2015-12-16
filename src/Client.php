<?php

namespace Enl\MosesClient;

class Client
{
    /** @var Transport */
    private $transport;

    /**
     * @param Transport $transport
     */
    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
    }

    /**
     * @param $baseUrl
     * @return static
     */
    public static function factory($baseUrl)
    {
        return new static(new Transport($baseUrl));
    }

    /**
     * @param string $text Text to translate
     * @param array $options Possible options are:
     *                        * align. Moses specific parameter. Please consider to read its docs. By default, is false.
     *                        * report-all-factors. Moses specific parameter. Please consider to read its docs. By default, is false.
     *                        * return-text. Whether to return only translated text or the whole decoded response.
     * @return string|array
     */
    public function translate($text, array $options = [])
    {
        $options = array_merge([
            'align' => false,
            'report-all-factors' => false,
            'return-text' => true
        ], $options);

        $response = $this->transport->call('translate', [$this->createRequestParameters($text, $options)]);

        return $options['return-text'] ? $response[0]['text'] : $response[0];
    }


    private function createRequestParameters($text, $options)
    {
        return [
            'text' => $text,
            'align' => $options['align'],
            'report-all-factors' => $options['report-all-factors']
        ];
    }
}
