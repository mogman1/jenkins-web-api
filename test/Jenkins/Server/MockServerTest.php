<?php
use mogman1\Jenkins\Server\MockServer;

class MockServerTest extends PHPUnit_Framework_TestCase {
  public function testGetServerUrl() {
    $mock = new MockServer();
    $this->assertEquals("", $mock->getServerUrl());
  }

  public function testGetAndSetResponse() {
    $mock = new MockServer();
    $mock->queueResponse("boogity invalid response");
    $mock->setResponse("HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n\r\n{\"hi\": \"mom\"}");
    $resp = $mock->get("/blah", array('foo' => "anything can go here"));

    $this->assertInstanceOf('mogman1\Jenkins\Server\HttpResponse', $resp);
    $this->assertEquals("200", $resp->getStatusCode());
    $this->assertEquals('{"hi": "mom"}', $resp->getBody());
  }

  public function testQueueingMultipleResponses() {
    $mock = new MockServer();
    $mock->queueResponse("HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n\r\nresponse1");
    $mock->queueResponse("HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n\r\nresponse2");
    $mock->queueResponse("HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n\r\nresponse3");

    $this->assertEquals("response1", $mock->get("/")->getBody());
    $this->assertEquals("response2", $mock->get("/")->getBody());
    $this->assertEquals("response3", $mock->get("/")->getBody());
  }
}
