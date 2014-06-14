<?php
use mogman1\Jenkins\Jenkins;
use mogman1\Jenkins\Server\MockServer;
use mogman1\Jenkins\JsonData;
use mogman1\Jenkins\Exception\InvalidApiObjectException;
use mogman1\Jenkins\ApiObject\Job;
use mogman1\Jenkins\Exception\JenkinsConnectionException;

class JobTest extends PHPUnit_Framework_TestCase {
  protected $mockServer;
  protected $jenkins;

  public function setUp() {
    $this->mockServer = new MockServer();
    $this->jenkins = new Jenkins($this->mockServer);
  }

  protected function getTestData($file) {
    return include(__DIR__."/_testData/Job/$file.php");
  }

  public function testConstruct() {
    $job = new Job($this->jenkins, "testJob");

    $this->assertEquals("testJob", $job->name);
    $this->assertEquals("/job/testJob", $job->url);
  }

  public function testFactoryThrowsExceptionWithNoJobName() {
    try {
      $data = new JsonData(Array());
      $job = Job::factory($this->jenkins, $data);
      $this->fail("InvalidApiObjectException not thrown");
    } catch (InvalidApiObjectException $e) {
      $this->assertEquals("'name' is required, but not found in build data", $e->getMessage());
    }
  }

  public function testFactoryCreatesJobWithDefaults() {
    $job = Job::factory($this->jenkins, new JsonData(Array('name' => "testJob", 'url' => "/job/testJob")));
    $this->assertEquals(array(), $job->actions);
    $this->assertTrue($job->buildable);
    $this->assertEquals(array(), $job->builds);
    $this->assertEquals("gray", $job->color);
    $this->assertFalse($job->concurrentBuild);
    $this->assertEquals("", $job->description);
    $this->assertEquals("", $job->displayName);
    $this->assertNull($job->displayNameOrNull);
    $this->assertEquals(array(), $job->downstreamProjects);
    $this->assertEquals(array(), $job->healthReport);
    $this->assertFalse($job->inQueue);
    $this->assertFalse($job->keepDependencies);
    $this->assertEquals("testJob", $job->name);
    $this->assertEquals(0, $job->nextBuildNumber);
    $this->assertEquals(array(), $job->property);
    $this->assertNull($job->queueItem);
    $this->assertEquals(array(), $job->scm);
    $this->assertEquals(array(), $job->upstreamProjects);
    $this->assertEquals("/job/testJob", $job->url);
    $this->assertNull($job->firstBuild);
    $this->assertNull($job->lastBuild);
    $this->assertNull($job->lastCompletedBuild);
    $this->assertNull($job->lastFailedBuild);
    $this->assertNull($job->lastStableBuild);
    $this->assertNull($job->lastSuccessfulBuild);
    $this->assertNull($job->lastUnstableBuild);
    $this->assertNull($job->lastUnsuccessfulBuild);
  }

  public function testMultiFactory() {
    $jobs = Job::multiFactory($this->jenkins, Array(
      Array('name' => "test1", 'url' => "/job/test1"),
      Array('name' => "test2", 'url' => "/job/test2"),
      Array('name' => "test3", 'url' => "/job/test3")));
    $this->assertEquals(3, count($jobs));
    $this->assertEquals("/job/test1", $jobs[0]->url);
    $this->assertEquals("/job/test2", $jobs[1]->url);
    $this->assertEquals("/job/test3", $jobs[2]->url);
  }

  public function testMockedJob() {
    $this->mockServer->queueResponse($this->getTestData(__FUNCTION__));
    $job = new Job($this->jenkins, "jenkins-web-api");
    $job->update();

    $this->assertEquals(1, count($job->actions));
    $this->assertEquals("next_release", $job->actions[0]['parameterDefinitions'][0]['defaultParameterValue']['value']);
    $this->assertTrue($job->buildable);
    $this->assertEquals(2, count($job->builds));
    $this->assertEquals(440, $job->builds[0]->number);
    $this->assertEquals(439, $job->builds[1]->number);
    $this->assertEquals("potato", $job->color);
    $this->assertTrue($job->concurrentBuild);
    $this->assertEquals("Jello world", $job->description);
    $this->assertEquals("jenkins-web-api", $job->displayName);
    $this->assertNull($job->displayNameOrNull);
    $this->assertEquals(array(), $job->downstreamProjects);
    $this->assertEquals(2, count($job->healthReport));
    $this->assertEquals("Number of checkstyle violations is 17,186", $job->healthReport[0]['description']);
    $this->assertEquals("Build stability: All recent builds failed.", $job->healthReport[1]['description']);
    $this->assertTrue($job->inQueue);
    $this->assertTrue($job->keepDependencies);
    $this->assertEquals("jenkins-web-api", $job->name);
    $this->assertEquals(441, $job->nextBuildNumber);
    $this->assertEquals(1, count($job->property));
    $this->assertEquals("next_release", $job->property[0]['parameterDefinitions'][0]['defaultParameterValue']['value']);
    $this->assertNull($job->queueItem);
    $this->assertEquals(array(), $job->scm);
    $this->assertEquals(array(), $job->upstreamProjects);
    $this->assertEquals("/job/jenkins-web-api", $job->url);

    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Build', $job->firstBuild);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Build', $job->lastBuild);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Build', $job->lastCompletedBuild);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Build', $job->lastFailedBuild);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Build', $job->lastStableBuild);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Build', $job->lastSuccessfulBuild);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Build', $job->lastUnstableBuild);
    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\Build', $job->lastUnsuccessfulBuild);
  }

  public function testTriggerBuild() {
    $this->mockServer->queueResponse("HTTP/1.1 201 Created\r\nLocation: http://jenkins/queue/item/9\r\n\r\n");
    $this->mockServer->queueResponse($this->getTestData(__FUNCTION__));
    $job = new Job($this->jenkins, "jenkins-web-api");
    $queueItem = $job->triggerBuild();

    $this->assertInstanceOf('mogman1\Jenkins\ApiObject\QueueItem', $queueItem);
    $this->assertEquals(1402643909517, $queueItem->inQueueSince);
    $this->assertEquals("/queue/item/54", $queueItem->url);
  }

  public function testTriggerBuildThrowsExceptionOnBadResponses() {
    $this->mockServer->queueResponse("HTTP/1.1 404 Not Found\r\n\r\n");
    $job = new Job($this->jenkins, "jenkins-web-api");
    try {
      $queueItem = $job->triggerBuild();
      $this->fail("No JenkinsConnectionException thrown on invalid build trigger");
    } catch (JenkinsConnectionException $e) {
      $this->assertEquals("Error triggering job build for jenkins-web-api", $e->getMessage());
    }

    $this->mockServer->queueResponse("HTTP/1.1 201 Created\r\nLocation: http://jenkins/queue/item/9\r\n\r\n");
    $this->mockServer->queueResponse("HTTP/1.1 404 Not Found\r\n\r\n");
      try {
      $queueItem = $job->triggerBuild();
      $this->fail("No JenkinsConnectionException thrown on failure to fetch QueueItem");
    } catch (JenkinsConnectionException $e) {
      $this->assertEquals("Error fetching queue item for triggered job [jenkins-web-api] /queue/item/9", $e->getMessage());
    }
  }
}
