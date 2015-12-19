<?php

namespace Enl\MosesClient;

use Enl\MosesClient\Exception\TransportException;

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
     *
     * @return static
     */
    public static function factory($baseUrl)
    {
        return new static(new Transport($baseUrl));
    }

    /**
     * @param string $text  Text to translate
     * @param bool   $align Should server return alignment information? false, by default
     *
     * The client will receive a map containing the same two keys,
     * where the value associated with the text key is the translated text,
     * and the align key (if present) maps to a list of maps.
     *
     * The alignment gives the segmentation in target order,
     * with each list element specifying the target start position (tgt-start),
     * source start position (src-start) and source end position (src-end).
     *
     * @return array|string
     *
     * @throws TransportException
     */
    public function translate($text, $align = false)
    {
        $options = [$align ? ['text' => $text, 'align' => $align] : ['text' => $text]];

        $response = $this->transport->call('translate', $options);

        return $align ? $response[0] : $response[0]['text'];
    }
}
