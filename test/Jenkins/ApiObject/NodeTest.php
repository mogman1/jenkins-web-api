<?php
use mogman1\Jenkins\Jenkins;
use mogman1\Jenkins\Server\MockServer;
use mogman1\Jenkins\ApiObject\Node;
use mogman1\Jenkins\JsonData;

class NodeTest extends PHPUnit_Framework_TestCase {
  protected $mockServer;
  protected $jenkins;

  public function setUp() {
    $this->mockServer = new MockServer();
    $this->jenkins = new Jenkins($this->mockServer);
  }

  protected function getTestData($file) {
    return include(__DIR__."/_testData/Node/$file.php");
  }

  public function testFactoryCreatesBuildWithDefaults() {
    $node = Node::factory($this->jenkins, new JsonData(array()));
    $this->assertEquals(array(), $node->assignedLabels);
    $this->assertEquals("", $node->description);
    $this->assertEquals(array(), $node->jobs);
    $this->assertEquals("UNKNOWN", $node->mode);
    $this->assertEquals("", $node->nodeName);
    $this->assertEquals("", $node->nodeDescription);
    $this->assertEquals(0, $node->numExecutors);
    $this->assertEquals(array(), $node->overallLoad);
    $this->assertNull($node->primaryView);
    $this->assertFalse($node->quietingDown);
    $this->assertEquals("0", $node->slaveAgentPort);
    $this->assertEquals(array(), $node->unlabeledLoad);
    $this->assertFalse($node->useCrumbs);
    $this->assertFalse($node->useSecurity);
    $this->assertEquals(array(), $node->views);
  }

  public function testMockedNode() {
    $this->mockServer->queueResponse($this->getTestData(__FUNCTION__));
    $node = new Node($this->jenkins);
    $node->update();

    $this->assertEquals(array(), $node->assignedLabels);
    $this->assertEquals("hello world", $node->description);
    $this->assertEquals(2, count($node->jobs));
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Job', $node->jobs[0]);
    $this->assertEquals("jenkins-web-api", $node->jobs[0]->name);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Job', $node->jobs[1]);
    $this->assertEquals("foo", $node->jobs[1]->name);
    $this->assertEquals("NORMAL", $node->mode);
    $this->assertEquals("blah", $node->nodeName);
    $this->assertEquals("the master Jenkins node", $node->nodeDescription);
    $this->assertEquals(2, $node->numExecutors);
    $this->assertEquals(array(), $node->overallLoad);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\View', $node->primaryView);
    $this->assertTrue($node->quietingDown);
    $this->assertEquals("10", $node->slaveAgentPort);
    $this->assertEquals(array(), $node->unlabeledLoad);
    $this->assertTrue($node->useCrumbs);
    $this->assertTrue($node->useSecurity);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\View', $node->views[0]);
    $this->assertEquals("All", $node->views[0]->name);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\View', $node->views[1]);
    $this->assertEquals("All2", $node->views[1]->name);
  }
}
