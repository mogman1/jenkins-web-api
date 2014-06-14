<?php
use mogman1\Jenkins\Jenkins;
use mogman1\Jenkins\Server\MockServer;
use mogman1\Jenkins\ApiObject\Build;
use mogman1\Jenkins\JsonData;
use mogman1\Jenkins\Exception\InvalidApiObjectException;
class BuildTest extends PHPUnit_Framework_TestCase {
  protected $mockServer;
  protected $jenkins;

  public function setUp() {
    $this->mockServer = new MockServer();
    $this->jenkins = new Jenkins($this->mockServer);
  }

  protected function getTestData($file) {
    return include(__DIR__."/_testData/Build/$file.php");
  }

  public function testConstruct() {
    $build = new Build($this->jenkins, "testJob", 42);

    $this->assertEquals(42, $build->number);
    $this->assertEquals("/job/testJob/42", $build->url);
  }

  public function testFactoryThrowsExceptionWithEmptyJobName() {
    try {
      $data = new JsonData(Array());
      $build = Build::factory($this->jenkins, "", $data);
      $this->fail("InvalidApiObjectException not thrown");
    } catch (InvalidApiObjectException $e) {
      $this->assertEquals("'jobName' is required, but nothing passed in", $e->getMessage());
    }
  }

  public function testFactoryThrowsExceptionWithNoNumber() {
    try {
      $data = new JsonData(Array());
      $build = Build::factory($this->jenkins, "foo", $data);
      $this->fail("InvalidApiObjectException not thrown");
    } catch (InvalidApiObjectException $e) {
      $this->assertEquals("'number' is required, but not found in build data", $e->getMessage());
    }
  }

  public function testFactoryCreatesBuildWithDefaults() {
    $build = Build::factory($this->jenkins, "foo", new JsonData(Array('number' => 42)));
    $this->assertEquals(array(), $build->actions);
    $this->assertEquals(array(), $build->artifacts);
    $this->assertFalse($build->building);
    $this->assertEquals("", $build->builtOn);
    $this->assertEquals(array(), $build->changeSet);
    $this->assertEquals(array(), $build->culprits);
    $this->assertEquals("", $build->description);
    $this->assertEquals(0, $build->duration);
    $this->assertEquals(0, $build->estimatedDuration);
    $this->assertEquals("", $build->executor);
    $this->assertEquals("", $build->fullDisplayName);
    $this->assertEquals("", $build->id);
    $this->assertFalse($build->keepLog);
    $this->assertEquals("UNKNOWN", $build->result);
    $this->assertEquals(0, $build->timestamp);
  }

  public function testMultiFactory() {
    $builds = Build::multiFactory($this->jenkins, "foo", Array(Array('number' => 42), Array('number' => 24), Array('number' => "314")));
    $this->assertEquals(3, count($builds));
    $this->assertEquals("/job/foo/42", $builds[0]->url);
    $this->assertEquals("/job/foo/24", $builds[1]->url);
    $this->assertEquals("/job/foo/314", $builds[2]->url);
  }

  public function testGetUrlFromJobNameAndNumber() {
    $this->assertEquals("/job/Potato/13", Build::getUrlFromJobNameAndNumber("Potato", 13));
  }

  public function testMockedBuild() {
    $this->mockServer->queueResponse($this->getTestData(__FUNCTION__));
    $build = new Build($this->jenkins, "jenkins-web-api", "123");
    $build->update();

    $actions = array(array('causes' => array(array('shortDescription' => "Started by an SCM change"))),
                     array("failCount" => 0, "skipCount" => 0, "totalCount" => 214, "urlName" => "testReport"));
    $this->assertEquals($actions, $build->actions);
    $this->assertEquals(array(), $build->artifacts);
    $this->assertTrue($build->building);
    $this->assertEquals("urmom", $build->builtOn);
    $this->assertEquals(1, count($build->changeSet['items']));
    $this->assertTrue(isset($build->changeSet['items'][0]['timestamp']));
    $this->assertEquals(2, count($build->culprits));
    $this->assertEquals("shaun.carlson", $build->culprits[0]['fullName']);
    $this->assertEquals("Shaun Carlson", $build->culprits[1]['fullName']);
    $this->assertEquals("message!", $build->description);
    $this->assertEquals(1095396, $build->duration);
    $this->assertEquals(555548, $build->estimatedDuration);
    $this->assertEquals("", $build->executor);
    $this->assertEquals("jenkins-web-api #123", $build->fullDisplayName);
    $this->assertEquals("2013-09-08_00-23-08", $build->id);
    $this->assertTrue($build->keepLog);
    $this->assertEquals("UNSTABLE", $build->result);
    $this->assertEquals(1378599788000, $build->timestamp);
  }
}
