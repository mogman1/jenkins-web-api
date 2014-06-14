<?php

use mogman1\Jenkins\Server\MockServer;
use mogman1\Jenkins\Jenkins;
use mogman1\Jenkins\Exception\InvalidApiObjectException;
use mogman1\Jenkins\ApiObject\View;
use mogman1\Jenkins\JsonData;

class ViewTest extends PHPUnit_Framework_TestCase {
  /**
   * @var Jenkins
   */
  private $jenkins;

  /**
   * @var MockServer
   */
  private $mockServer;

  public function setUp() {
    $this->mockServer = new MockServer();
    $this->jenkins = new Jenkins($this->mockServer);
  }

  public function testFactoryThrowsExceptionsWithMissingElements() {
    try {
      $view = View::factory($this->jenkins, new JsonData(array('name' => "foo")));
      $this->fail("No InvalidApiObjectException thrown");
    } catch (InvalidApiObjectException $e) {
      $this->assertEquals("'url' is required, but not found in build data", $e->getMessage());
    }

    try {
      $view = View::factory($this->jenkins, new JsonData(array('url' => "/")));
      $this->fail("No InvalidApiObjectException thrown");
    } catch (InvalidApiObjectException $e) {
      $this->assertEquals("'name' is required, but not found in build data", $e->getMessage());
    }
  }

  public function testFactory() {
    $view = View::factory($this->jenkins, new JsonData(array('name' => "Foo", 'url' => "/")));
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\View', $view);
    $this->assertEquals("Foo", $view->name);
    $this->assertEquals("/", $view->url);
  }

  public function testMultiFactory() {
    $views = View::multiFactory($this->jenkins, array(array('name' => "Foo", 'url' => "/"), array('name' => "Foo2", 'url' => "/foo2")));
    $this->assertEquals(2, count($views));
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\View', $views[0]);
    $this->assertEquals("Foo", $views[0]->name);
    $this->assertEquals("/", $views[0]->url);

    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\View', $views[1]);
    $this->assertEquals("Foo2", $views[1]->name);
    $this->assertEquals("/foo2", $views[1]->url);
  }

  public function testUpdateThrowsException() {
    try {
      $view = new View($this->jenkins, "/", "test");
      $view->update();
      $this->fail("No RuntimeException thrown");
    } catch (\RuntimeException $e) {
      $this->assertEquals("update not implemented for views", $e->getMessage());
    }
  }
}
