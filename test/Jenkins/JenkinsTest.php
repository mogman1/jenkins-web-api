<?php

use mogman1\Jenkins\Jenkins;
use mogman1\Jenkins\Server\MockServer;
use mogman1\Jenkins\Server\Http;
use mogman1\Jenkins\Exception\JenkinsConnectionException;

/**
 * ServerImp test case.
 */
class JenkinsTest extends PHPUnit_Framework_TestCase {

  /**
   * @var MockServer
   */
  private $Server;

  /**
   * @var Jenkins
   */
  private $jenkins;

  /**
   * Prepares the environment before running a test.
   */
  protected function setUp() {
    parent::setUp ();

    $this->Server = new MockServer();
    $this->jenkins = new Jenkins($this->Server);
  }

  protected function getTestData($file) {
    return include(__DIR__."/_testData/Jenkins/$file.php");
  }

  public function testGetNodeInfo() {
    $this->Server->queueResponse($this->getTestData(__FUNCTION__));
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Node', $this->jenkins->getNodeInfo());
  }

  public function testGetJob() {
    $this->Server->queueResponse($this->getTestData(__FUNCTION__));
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Job', $this->jenkins->getJob("jenkins-web-api"));
  }

  public function testGetServerUrl() {
    $this->assertEquals("", $this->jenkins->getServerUrl());
    $jenkins = new Jenkins(new Http("http://blat", "foo", "bar"));
    $this->assertEquals("http://blat", $jenkins->getServerUrl());
  }

  public function testGetThrowsJenkinsConnectionExceptionOnInvalidResponse() {
    $this->Server->queueResponse("gonna blow chunks");
    try {
      $this->jenkins->get("/");
      $this->fail("No JenkinsConnectionException thrown");
    } catch (JenkinsConnectionException $e) { }
  }

  public function testGetThrowsExceptionOnHttpResponseError() {
    $this->Server->queueResponse("HTTP/1.1 205 Kaboom Fool\r\n\r\nhi");
    $this->Server->queueResponse("HTTP/1.1 404 Not Found\r\n\r\nhi");
    $this->Server->queueResponse("HTTP/1.1 200 OK\r\n\r\nhi");
    $this->Server->queueResponse("HTTP/1.1 201 Created\r\n\r\nhi");

    try {
      $this->jenkins->get("/");
      $this->fail("No JenkinsConnectionException thrown");
    } catch (JenkinsConnectionException $e) {}

    try {
      $this->jenkins->get("/");
      $this->fail("No JenkinsConnectionException thrown");
    } catch (JenkinsConnectionException $e) {}

    $resp = $this->jenkins->get("/");
    $resp2 = $this->jenkins->get("/");

    $this->assertInstanceOf('mogman1\Jenkins\Server\HttpResponse', $resp);
    $this->assertInstanceOf('mogman1\Jenkins\Server\HttpResponse', $resp2);
  }
}
