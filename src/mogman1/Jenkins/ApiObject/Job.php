<?php

namespace mogman1\Jenkins\ApiObject;

use mogman1\Jenkins\ApiObject;
use mogman1\Jenkins\Jenkins;
use mogman1\Jenkins\JsonData;
use mogman1\Jenkins\Exception\InvalidApiObjectException;
use mogman1\Jenkins\Exception\JenkinsConnectionException;

class Job extends ApiObject {
  /**
   * I don't know, so far it just so happens to have my parameterDefinitions and nine other empty
   * objects in an array
   * TODO: Research
   *
   * @var array
   */
  public $actions;

  /**
   * Whether this job is buildable
   * @var bool
   */
  public $buildable;

  /**
   * An array of past builds
   * @var array[Build]
   */
  public $builds;

  /**
   * Indication of project health (red - failed, yellow - unstable, green - build OK, gray - unknown)
   * @var string
   */
  public $color;

  /**
   * If concurrent builds are allowed of this job
   * @see https://github.com/jenkinsci/jenkins/blob/master/core/src/main/java/hudson/model/AbstractProject.java
   * @var bool
   */
  public $concurrentBuild;

  /**
   * Description of state of project, could include HTML elements such as images indicating job condition
   * @var string
   */
  public $description;

  /**
   * Verbose name of job
   * @var string
   */
  public $displayName;

  /**
   * God only knows why this exists
   * TODO: Research
   * @var string|null
   */
  public $displayNameOrNull;

  /**
   * I don't know
   * TODO: Research
   * @var array
   */
  public $downstreamProjects;

  /**
   * First build of this job
   * @var Build
   */
  public $firstBuild;

  /**
   * Array of health statuses for job
   * @var array
   */
  public $healthReport;

  /**
   * Jenkins source has this always return false.
   * @see https://github.com/jenkinsci/jenkins/blob/master/core/src/main/java/hudson/model/Job.java isInQueue
   * @var bool
   */
  public $inQueue;

  /**
   * If true, it will keep all the build logs of dependency components.
   * @see https://github.com/jenkinsci/jenkins/blob/master/core/src/main/java/hudson/model/Job.java isKeepDependencies
   * @var bool
   */
  public $keepDependencies;

  /**
   * Last build of any result for this job
   * @var Build
   */
  public $lastBuild;

  /**
   * Last build that went to completion for this job (may not have been successful)
   * TODO: Research
   * @var Build
   */
  public $lastCompletedBuild;

  /**
   * Last build that failed for this job (unclear distinction with unsuccessful)
   * TODO: Research
   * @var Build
   */
  public $lastFailedBuild;

  /**
   * Last build that was found to be stable
   * @var Build
   */
  public $lastStableBuild;

  /**
   * Last build that was found to be successful (could be stable or unstable, but not failed)
   * TODO: Research
   * @var Build
   */
  public $lastSuccessfulBuild;

  /**
   * Last build that was found to be unstable
   * @var Build
   */
  public $lastUnstableBuild;

  /**
   * Last build that was found to be unsuccessful (unclear distinction with failed)
   * TODO: Research
   * @var Build
   */
  public $lastUnsuccessfulBuild;

  /**
   * Name of this job
   * @var string
   */
  public $name;

  /**
   * Jenkins' guess for what the next build number will be
   * @var int
   */
  public $nextBuildNumber;

  /**
   * Array of custom properties associated with this job (uncertain format)
   * TODO: Research
   * @var array
   */
  public $property;

  /**
   * From Jenkins source code: If this job is in the build queue, return its item.
   * However, the code always returns null.  So I don't know what the point of this is.
   * @see https://github.com/jenkinsci/jenkins/blob/master/core/src/main/java/hudson/model/Job.java getQueueItem
   * @var null
   */
  public $queueItem;

  /**
   * Presumably information about the SCM being used, but I've never seen anything here
   * TODO: Research
   * @var array
   */
  public $scm;

  /**
   * I don't know
   * TODO: Research
   * @var array
   */
  public $upstreamProjects;

  public function __construct(Jenkins $conn, $name) {
    parent::__construct($conn, Job::getUrlFromName($name));
    $this->name = $name;
  }

  /**
   * (non-PHPdoc)
   * @see \mogman1\Jenkins\ApiObject::isImpValid()
   */
  protected function isImpValid() {
    $valid = TRUE;
    if (!$this->name) $valid = FALSE;

    return $valid;
  }

  /**
   * Constructs a Job from data assumed to have come from a Jenkins API call
   *
   * @param Jenkins $conn
   * @param JsonData $data
   * @return \mogman1\Jenkins\Job
   */
  public static function factory(Jenkins $conn, JsonData $data) {
    $name = $data->get("name", "");

    if (!$name) throw new InvalidApiObjectException("'name' is required, but not found in build data");

    $job = new Job($conn, $name);
    $job->updateProperties($data);

    return $job;
  }

  public static function getUrlFromName($name) {
    return "/job/".str_replace(" ", "%20", $name); //spaces get converted to %20, but pluses remain pluses :-\
  }

  /**
   * Constructs an array of jobs from data, which is assumed to be an array of data that
   * would have come from a Jenkins API call
   *
   * @param Jenkins $conn
   * @param array $data
   * @return array[\mogman1\Jenkins\Job]
   */
  public static function multiFactory(Jenkins $conn, array $data) {
    $jobs = array();
    foreach ($data as $jobData) $jobs[] = Job::factory($conn, new JsonData($jobData));

    return $jobs;
  }

  /**
   * Triggers a new remote build of this job on Jenkins.  $buildParams is passed on to Jenkins, in
   * case your builds accept parameters.  Also, if your build requires authentication token to be
   * passed, make sure you include it as part of these parameters, e.g. Array('token' => "<secret>", ...)
   *
   * NOTE: Because Jenkins does not return the build number on a triggered build request, all
   * that can be returned is a QueueItem, which sits in Jenkins' build queue until it is picked
   * up to be processed.  Generally Jenkins leaves a build trigger in the queue for 5 seconds before
   * finally picking it up to process, or perhaps even longer if a build is already being processed
   * and no resources are available to pick yours up.
   *
   * You can use the QueueItem to engage in some shenanigans and poll Jenkins until your QueueItem
   * registers an $executable, which is going to be the build you want to monitor.   Alternatively,
   * you could do a refresh of this job and use $nextBuildNumber, which probably will be what your
   * build uses when it spins up, but there are no guarantees there.
   *
   * In other words, here there be dragons...
   *
   * @link https://wiki.jenkins-ci.org/display/JENKINS/Parameterized+Build
   * @link https://wiki.jenkins-ci.org/display/JENKINS/Remote+access+API
   * @link https://issues.jenkins-ci.org/browse/JENKINS-12827?focusedCommentId=201381&page=com.atlassian.jira.plugin.system.issuetabpanels:comment-tabpanel#comment-201381
   *
   * @throws JenkinsConnectionException if there's an issue connecting to Jenkins
   * @param array $buildParams Array of parameters to pass on.
   * @return QueueItem
   */
  public function triggerBuild(array $buildParams=array()) {
    try {
      $response = $this->conn->get($this->url."/buildWithParameters", $buildParams);
    } catch (JenkinsConnectionException $e) {
      throw new JenkinsConnectionException("Error triggering job build for ".$this->name, $e->getCode(), $e);
    }

    $queueUrl = $response->getHeader("Location");
    $path = parse_url($queueUrl, PHP_URL_PATH);
    try {
      $queueItem = QueueItem::factory($this->conn, $this->conn->get($path)->getJson());
    } catch (JenkinsConnectionException $e) {
      throw new JenkinsConnectionException("Error fetching queue item for triggered job [".$this->name."] $path", $e->getCode(), $e);
    }

    return $queueItem;
  }

  /**
   * (non-PHPdoc)
   * @see \mogman1\Jenkins\ApiObject::updateImpProperties()
   */
  protected function updateImpProperties(JsonData $data) {
    //url and name are not updated since, once they are set, they should never change

    //TODO: figure out what actions are
    $this->actions            = $data->get('actions', array());
    $this->buildable          = $data->get('buildable', TRUE);
    $this->builds             = Build::multiFactory($this->conn, $this->name, $data->get('builds', array()));
    $this->color              = $data->get('color', "gray");
    $this->concurrentBuild    = $data->get('concurrentBuild', FALSE);
    $this->description        = $data->get('description', "");
    $this->displayName        = $data->get('displayName', "");
    $this->displayNameOrNull  = $data->get('displayNameOrNull', NULL);
    $this->downstreamProjects = $data->get('downstreamProjects', array());

    //TODO: create health report object
    $this->healthReport       = $data->get('healthReport', array());
    $this->inQueue            = $data->get('inQueue', FALSE);
    $this->keepDependencies   = $data->get('keepDependencies', FALSE);
    $this->nextBuildNumber    = $data->get('nextBuildNumber', "0");
    $this->property           = $data->get('property', array());
    $this->queueItem          = $data->get('queueItem', NULL);
    $this->scm                = $data->get('scm', array());
    $this->upstreamProjects   = $data->get('upstreamProjects', array());

    $build = $data->get('firstBuild', array());
    $this->firstBuild = ($build) ? Build::factory($this->conn, $this->name, new JsonData($build)) : NULL;

    $build = $data->get('lastBuild', array());
    $this->lastBuild = ($build) ? Build::factory($this->conn, $this->name, new JsonData($build)) : NULL;

    $build = $data->get('lastCompletedBuild', array());
    $this->lastCompletedBuild = ($build) ? Build::factory($this->conn, $this->name, new JsonData($build)) : NULL;

    $build = $data->get('lastFailedBuild', array());
    $this->lastFailedBuild = ($build) ? Build::factory($this->conn, $this->name, new JsonData($build)) : NULL;

    $build = $data->get('lastStableBuild', array());
    $this->lastStableBuild = ($build) ? Build::factory($this->conn, $this->name, new JsonData($build)) : NULL;

    $build = $data->get('lastSuccessfulBuild', array());
    $this->lastSuccessfulBuild = ($build) ? Build::factory($this->conn, $this->name, new JsonData($build)) : NULL;

    $build = $data->get('lastUnstableBuild', array());
    $this->lastUnstableBuild = ($build) ? Build::factory($this->conn, $this->name, new JsonData($build)) : NULL;

    $build = $data->get('lastUnsuccessfulBuild', array());
    $this->lastUnsuccessfulBuild = ($build) ? Build::factory($this->conn, $this->name, new JsonData($build)) : NULL;
  }
}
