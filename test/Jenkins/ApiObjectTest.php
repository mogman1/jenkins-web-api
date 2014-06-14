<?php

use mogman1\Jenkins\Server\Http;
use mogman1\Jenkins\Server\MockServer;
use mogman1\Jenkins\Jenkins;
use mogman1\Jenkins\ApiObject\Job;
use mogman1\Jenkins\JsonData;
use mogman1\Jenkins\Exception\InvalidApiObjectException;

class ApiObjectTest extends PHPUnit_Framework_TestCase {
  protected function getTestData($file) {
    return include(__DIR__."/_testData/ApiObject/$file.php");
  }

  public function testGetUrl() {
    $http = new Http("http://jenkins", "foo", "bar");
    $jenkins = new Jenkins($http);
    $apiObject = new Job($jenkins, "blat");

    $this->assertEquals("/job/blat", $apiObject->url);
    $this->assertEquals("http://jenkins/job/blat", $apiObject->getUrl());
  }

  public function testIsValid() {
    $http = new Http("http://jenkins", "foo", "bar");
    $jenkins = new Jenkins($http);
    $apiObject = new Job($jenkins, "boo");

    $this->assertTrue($apiObject->isValid());
    $apiObject->name = "";
    $this->assertFalse($apiObject->isValid());
    $apiObject->name = "fsda";
    $this->assertTrue($apiObject->isValid());
    $apiObject->url = "";
    $this->assertFalse($apiObject->isValid());
  }

  public function testUpdateProperties() {
    $mock = new MockServer();
    $apiObject = new Job(new Jenkins($mock), "blat");
    $apiObject->color = "fuschia";

    $this->assertEquals("fuschia", $apiObject->color);
    $apiObject->updateProperties(new JsonData(array('color' => "blue")));
    $this->assertEquals("blue", $apiObject->color);
  }

  public function testUpdatePropertiesThrowsExceptionWhenObjectIsInInvalidState() {
    $mock = new MockServer();
    $apiObject = new Job(new Jenkins($mock), "blat");
    $apiObject->color = "fuschia";
    $apiObject->url = "";

    $this->assertEquals("fuschia", $apiObject->color);
    $this->assertEquals("", $apiObject->url);
    try {
      $apiObject->updateProperties(new JsonData(array('color' => "blue")));
      $this->fail("no InvalidApiObjectException thrown");
    } catch (InvalidApiObjectException $e) {
      $this->assertEquals("API object in invalid state", $e->getMessage());
    }
  }

  public function testUpdate() {
    $mock = new MockServer();
    $mock->queueResponse($this->getTestData(__FUNCTION__));
    $apiObject = new Job(new Jenkins($mock), "blat");
    $apiObject->color = "fuschia";

    $this->assertEquals("fuschia", $apiObject->color);
    $apiObject->update();
    $this->assertEquals("blue", $apiObject->color);
  }
}
