<?php


namespace Enl\MosesClient {

    use Enl\MosesClient\Tests\TransportTest;

    function curl_exec() {
        return TransportTest::$curl_response;
    }

    function curl_error() {
        return TransportTest::$curl_error;
    }

    function curl_getinfo() {
        return TransportTest::$curl_status;
    }
}

namespace Enl\MosesClient\Tests {

    use Comodojo\Exception\XmlrpcException;
    use Enl\MosesClient\Transport;
    use Mockery as m;
    use Mockery\Adapter\Phpunit\MockeryTestCase;

    /**
     * Class TransportTest
     *
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class TransportTest extends MockeryTestCase
    {
        private $transport;

        public static $curl_response;
        public static $curl_error;
        public static $curl_status;


        protected function setUp()
        {
            parent::setUp();

            $this->transport = new Transport('');
        }

        private function mockSuccessfulCurlRequest($response)
        {
            self::$curl_response = $response;
            self::$curl_error = null;
            self::$curl_status = 200;
        }

        private function mockFailedRequest($message, $status)
        {
            self::$curl_response = false;
            self::$curl_error = $message;
            self::$curl_status = $status;
        }

        /**
         * @param $method
         * @param $params
         * @param $result
         * @return m\MockInterface
         */
        protected function mockEncoder($method, $params, $result)
        {
            $mock = m::mock('overload:Comodojo\Xmlrpc\XmlrpcEncoder');
            if ($method !== null) {
                $mock->shouldReceive('encodeCall')->withArgs([$method, $params])->andReturn($result);
            }
            else {
                $mock->shouldReceive('encodeCall')->andThrow(new XmlrpcException());
            }

            return $mock;
        }

        protected function mockDecoder($response, $result)
        {
            $mock = m::mock('overload:Comodojo\Xmlrpc\XmlrpcDecoder');
            if ($response !== null) {
                $mock->shouldReceive('decodeResponse')->with($response)->andReturn($result);
            }
            else {
                $mock->shouldReceive('decodeResponse')->andThrow(new XmlrpcException());
            }

            return $mock;
        }

        public function failureStatusProvider()
        {
            return [
                ['Not found', 404],
                ['Timeout', 502]
            ];
        }

        /**
         * @param $message
         * @param $status
         * @test
         * @dataProvider failureStatusProvider
         */
        public function shouldThrowExceptionOnCurlFailure($message, $status)
        {
            $this->mockFailedRequest($message, $status);
            $this->mockEncoder('translate', ['text' => 'test'], '<text>test</text>');

            $this->setExpectedException('Enl\MosesClient\Exception\TransportException', $message);
            $this->transport->call('translate', ['text' => 'test']);
        }

        /**
         * @test
         * @expectedException \Enl\MosesClient\Exception\TransportException
         * @expectedExceptionMessage XML parsing error
         */
        public function shouldThrowExceptionOnEncodingFailure()
        {
            $this->mockEncoder(null, null, null);

            $this->transport->call('translate', ['text' => 'test']);
        }

        /**
         * @test
         * @expectedException \Enl\MosesClient\Exception\TransportException
         * @expectedExceptionMessage XML parsing error
         */
        public function shouldThrowExceptionOnDecodingFailure()
        {
            $this->mockEncoder('translate', ['text' => 'test'], '<text>test</text>');
            $this->mockSuccessfulCurlRequest('<text>translatedtext</text>');
            $this->mockDecoder(null, null);

            $this->transport->call('translate', ['text' => 'test']);
        }

        /**
         * @test
         */
        public function shouldReturnDecodedResponse()
        {
            $this->mockEncoder('translate', ['text' => 'test'], '<text>test</text>');
            $this->mockSuccessfulCurlRequest('<text>translatedtext</text>');
            $this->mockDecoder('<text>translatedtext</text>', [['text' => 'translatedtext']]);

            $response = $this->transport->call('translate', ['text' => 'test']);

            $this->assertInternalType('array', $response);
            $struct = $response[0];
            $this->assertArrayHasKey('text', $struct);
            $this->assertEquals('translatedtext', $struct['text']);
        }


    }
}
