<?php


namespace Enl\MosesClient\Tests;


use Enl\MosesClient\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $client = Client::factory('string');

        $this->assertInstanceOf('Enl\MosesClient\Client', $client);
    }
}
