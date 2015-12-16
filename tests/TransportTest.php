<?php


namespace Enl\MosesClient\Tests;

use Comodojo\Exception\XmlrpcException;
use Enl\MosesClient\Transport;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class XmlrpcProtocolTest extends MockeryTestCase
{
    private $protocol;

    protected function setUp()
    {
        parent::setUp();

        $this->protocol = new Transport('');
    }


    /**
     * @test
     * @expectedException \Comodojo\Exception\XmlrpcException
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function encoderShouldRethrowException()
    {
        $this->mockFailing('Encoder');

        $this->protocol->encode('method', ['param' => 'value']);
    }

    /**
     * @test
     * @expectedException \Comodojo\Exception\XmlrpcException
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function decoderShouldRethrowException()
    {
        $this->mockFailing('Decoder');

        $this->protocol->decode('response');
    }



    private function mockFailing($type)
    {
        $mock = m::mock('overload:\Comodojo\Xmlrpc\Xmlrpc'.$type);

        $function = $type === 'Encoder' ? 'encodeCall' : 'decodeResponse';

        $mock->shouldReceive($function)->withAnyArgs()->andThrow(new XmlrpcException());

        return $mock;
    }


    /**
     * @param $method
     * @param $parameters
     * @test
     * @dataProvider parameters
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function shouldReturnResultsOfComodojoCalls($method, $parameters, $response)
    {
        $this->mockReturner($method === 'decode' ? 'Decoder' : 'Encoder', $parameters, $response);

        $actual = call_user_func_array([$this->protocol, $method], $parameters);

        $this->assertEquals($response, $actual);
    }

    public function parameters()
    {
        return [
            ['decode', ['test-test-test'], ['test' => 'test']],
            ['encode', ['test', ['param' => 'value']], 'testtesttes']
        ];
    }

    private function mockReturner($type, $parameters, $response)
    {
        $mock = m::mock('overload:\Comodojo\Xmlrpc\Xmlrpc'.$type);

        $function = $type === 'Encoder' ? 'encodeCall' : 'decodeResponse';

        $mock->shouldReceive($function)->withArgs($parameters)->andReturn($response);

        return $mock;
    }
}
