<?php

namespace Utils\Tests;

use \Utils\CurlClient;

class CurlClientTest extends \PHPUnit_Framework_TestCase {
    use \InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

    public static function setUpBeforeClass() {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
    }

    public static function tearDownAfterClass() {
        static::tearDownHttpMockAfterClass();
    }

    public function setUp() {
        $this->setUpHttpMock();
    }

    public function tearDown() {
        $this->tearDownHttpMock();
    }

    public function testSimpleRequest()
    {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/foo')
            ->then()
                ->body('mocked body')
            ->end();
        $this->http->setUp();

        $curlClient = new CurlClient('http://localhost:8082/foo');
        $response = $curlClient->get();
        $this->assertEquals('mocked body', $response);
    }

}
