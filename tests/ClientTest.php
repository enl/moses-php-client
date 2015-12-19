<?php


namespace Enl\MosesClient\Tests;

use Enl\MosesClient\Exception\TransportException;
use Mockery as m;
use Enl\MosesClient\Client;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ClientTest extends MockeryTestCase
{
    /**
     * @test
     */
    public function testFactory()
    {
        $client = Client::factory('string');

        $this->assertInstanceOf('Enl\MosesClient\Client', $client);
    }

    /**
     * @test
     * @expectedException \Enl\MosesClient\Exception\TransportException
     */
    public function shouldThrowException()
    {
        $transport = m::mock('Enl\MosesClient\Transport');
        $transport->shouldReceive('call')->withAnyArgs()->andThrow(new TransportException());

        $client = new Client($transport);
        $client->translate('text to translate');
    }

    public function options()
    {
        return [
            ['text to translate', false],
            ['text to translate', true],
        ];
    }

    /**
     * @param $text
     * @param $align
     * @dataProvider options
     * @test
     */
    public function shouldPassArgumentsToTransport($text, $align)
    {
        $expected_args = $align ? [ 'text' => $text, 'align' => $align ] : ['text' => $text];

        $transport = m::mock('Enl\MosesClient\Transport');
        $transport->shouldReceive('call')->withArgs(['translate', [$expected_args]])->once();

        $client = new Client($transport);
        $client->translate($text, $align);
    }

    public function text()
    {
        return [
            ['text to translate', 'translated text']
        ];
    }

    /**
     * @param $text
     * @param $translated
     * @test
     * @dataProvider text
     */
    public function shouldReturnText($text, $translated)
    {
        $transport = m::mock('Enl\MosesClient\Transport');
        $transport->shouldReceive('call')->once()->withArgs([
            'translate',
            m::any()
        ])->andReturn([['text' => $translated]]);

        $client = new Client($transport);

        $actual = $client->translate($text, false);
        $this->assertInternalType('string', $actual);
        $this->assertEquals($translated, $actual);
    }

    /**
     * @param $text
     * @param $translated
     * @test
     * @dataProvider text
     */
    public function shouldReturnArray($text, $translated)
    {
        $transport = m::mock('Enl\MosesClient\Transport');
        $transport->shouldReceive('call')->once()->withArgs([
            'translate',
            m::any()
        ])->andReturn([['text' => $translated]]);

        $client = new Client($transport);

        $actual = $client->translate($text, true);
        $this->assertInternalType('array', $actual);
        $this->assertEquals($translated, $actual['text']);
    }
}
