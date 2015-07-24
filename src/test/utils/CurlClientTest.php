<?php
require 'vendor/autoload.php';

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
}
