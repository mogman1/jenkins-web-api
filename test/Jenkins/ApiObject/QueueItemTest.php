<?php
use mogman1\Jenkins\Jenkins;
use mogman1\Jenkins\Server\MockServer;
use mogman1\Jenkins\JsonData;
use mogman1\Jenkins\ApiObject\QueueItem;
use mogman1\Jenkins\Exception\InvalidApiObjectException;

class QueueItemTest extends PHPUnit_Framework_TestCase {
  protected $mockServer;
  protected $jenkins;

  public function setUp() {
    $this->mockServer = new MockServer();
    $this->jenkins = new Jenkins($this->mockServer);
  }

  protected function getTestData($file) {
    return include(__DIR__."/_testData/QueueItem/$file.php");
  }

  public function testParseParamsString() {
    $qi = QueueItem::factory($this->jenkins, new JsonData(array('id' => "5", 'params' => "\nbranch=next_release\nbuild_target=build")));
    $this->assertEquals(array('branch' => "next_release", 'build_target' => "build"), $qi->params);

    $qi = QueueItem::factory($this->jenkins, new JsonData(array('id' => "5", 'params' => "\nbranch\nbuild_target=build")));
    $this->assertEquals(array('branch' => "", 'build_target' => "build"), $qi->params);

    $qi = QueueItem::factory($this->jenkins, new JsonData(array('id' => "5", 'params' => "\nbranch=next=release\nbuild_target=build")));
    $this->assertEquals(array('branch' => "next=release", 'build_target' => "build"), $qi->params);
  }

  public function testFactoryThrowsExceptionWithNoUrl() {
    try {
      $qi = QueueItem::factory($this->jenkins, new JsonData(array()));
      $this->fail("No InvalidApiObjectException thrown");
    } catch (InvalidApiObjectException $e) {
      $this->assertEquals("'id' is required, but not found in build data", $e->getMessage());
    }
  }

  public function testFactoryCreatesBuildWithDefaults() {
    $qi = QueueItem::factory($this->jenkins, new JsonData(array('id' => "42")));
    $this->assertEquals(array(), $qi->actions);
    $this->assertFalse($qi->blocked);
    $this->assertFalse($qi->buildable);
    $this->assertFalse($qi->cancelled);
    $this->assertEquals(42, $qi->id);
    $this->assertEquals(0, $qi->inQueueSince);
    $this->assertEquals(array(), $qi->params);
    $this->assertFalse($qi->stuck);
    $this->assertNull($qi->task);
    $this->assertEquals(0, $qi->timestamp);
    $this->assertEquals("", $qi->why);
    $this->assertEquals("/queue/item/42", $qi->url);
  }

  public function testUpdatingQueueItem() {
    $data = $this->getTestData(__FUNCTION__);
    $this->mockServer->queueResponse($data['firstCall']);
    $this->mockServer->queueResponse($data['secondCall']);
    $qi = QueueItem::factory($this->jenkins, new JsonData(array('id' => "55")));

    $qi->update();
    $this->assertEquals(2, count($qi->actions));
    $this->assertEquals("Bob the Builder", $qi->actions[1]['causes'][0]['userName']);
    $this->assertTrue($qi->blocked);
    $this->assertTrue($qi->buildable);
    $this->assertFalse($qi->cancelled);
    $this->assertEquals(55, $qi->id);
    $this->assertEquals(1402689326588, $qi->inQueueSince);
    $this->assertEquals(array('branch' => "next_release", 'build_target' => "build"), $qi->params);
    $this->assertTrue($qi->stuck);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Job', $qi->task);
    $this->assertEquals("jenkins-web-api", $qi->task->name);
    $this->assertEquals(1402689331588, $qi->timestamp);
    $this->assertEquals("In the quiet period. Expires in 4.7 sec", $qi->why);
    $this->assertEquals("/queue/item/55", $qi->url);

    $qi->update();
    $this->assertEquals(2, count($qi->actions));
    $this->assertEquals("Bob the Builder", $qi->actions[1]['causes'][0]['userName']);
    $this->assertFalse($qi->blocked);
    $this->assertFalse($qi->buildable);
    $this->assertTrue($qi->cancelled);
    $this->assertEquals(55, $qi->id);
    $this->assertEquals(1402689326588, $qi->inQueueSince);
    $this->assertEquals(array('branch' => "next_release", 'build_target' => "build"), $qi->params);
    $this->assertFalse($qi->stuck);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Job', $qi->task);
    $this->assertEquals("jenkins-web-api", $qi->task->name);
    $this->assertEquals(0, $qi->timestamp); //timestamp gets replaced with executable later on, I don't know why
    $this->assertEquals("", $qi->why);
    $this->assertEquals("/queue/item/55", $qi->url);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Build', $qi->executable);
    $this->assertEquals("442", $qi->executable->number);
  }
}
