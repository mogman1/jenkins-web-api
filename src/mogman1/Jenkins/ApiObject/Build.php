<?php

namespace mogman1\Jenkins\ApiObject;

use mogman1\Jenkins\ApiObject;
use mogman1\Jenkins\JsonData;
use mogman1\Jenkins\Jenkins;
use mogman1\Jenkins\Exception\InvalidApiObjectException;

class Build extends ApiObject {
  /**
   * I don't know
   * TODO: Research
   * @var array
   */
  public $actions;

  /**
   * I don't know
   * TODO: Research
   * @var array
   */
  public $artifacts;

  /**
   * Whether build is currently ongoing or not
   * @var bool
   */
  public $building;

  /**
   * Name of slave this build was done on, or empty string if done on master
   * @var string
   */
  public $builtOn;

  /**
   * List of items that changed in this build from the last
   * @var array
   */
  public $changeSet;

  /**
   * List of users involved in the code since the last build
   * TODO: Does this become empty if build was a success?
   * TODO: Create user object
   * @var array
   */
  public $culprits;

  /**
   * Description of build (seems to always be null/empty)
   * @var string
   */
  public $description;

  /**
   * Length of time build took, in milliseconds
   * @var int
   */
  public $duration;

  /**
   * Jenkins' estimate of how long this build will take.  Probably full of lies.
   * @var int
   */
  public $estimatedDuration;

  /**
   * I don't know
   * TODO: Research
   * @var unknown
   */
  public $executor;

  /**
   * Display name of build, includes job name plus build number (e.g. "MyJob #123")
   * @var string
   */
  public $fullDisplayName;

  /**
   * A timestamp value of form YYYY-MM-DD_HH-MM-SS, text representation of $timestamp
   * @var string
   */
  public $id;

  /**
   * If log should be kept as part of overall keepDependency strategy
   * @see https://github.com/jenkinsci/jenkins/blob/master/core/src/main/java/hudson/model/Run.java
   * @var bool
   */
  public $keepLog;

  /**
   * Build number
   * @var int
   */
  public $number;

  /**
   * Result of build with values SUCCESS, UNSTABLE, FAILURE, NOT_BUILT, ABORTED, UNKNOWN [custom status in case no value was
   * present, not an actual Jenkins result code]
   * @see https://github.com/jenkinsci/jenkins/blob/master/core/src/main/java/hudson/model/Result.java
   * @var string
   */
  public $result;

  /**
   * Timestamp when build was started, in milliseconds
   * @var int
   */
  public $timestamp;

  public function __construct(Jenkins $conn, $jobName, $number) {
    parent::__construct($conn, Build::getUrlFromJobNameAndNumber($jobName, $number));
    $this->number = $number;
  }

  /**
   * Fetches the builds log from Jenkins
   * @return string
   */
  public function getConsoleLog() {
    return $this->conn->get($this->url."/logText/progressiveText")->getBody();
  }

  public static function factory(Jenkins $conn, $jobName, JsonData $data) {
    $number = $data->get("number", "");

    if (!$jobName) throw new InvalidApiObjectException("'jobName' is required, but nothing passed in");
    if (!$number)  throw new InvalidApiObjectException("'number' is required, but not found in build data");
    $build = new Build($conn, $jobName, $number);
    $build->updateProperties($data);

    return $build;
  }

  public static function getUrlFromJobNameAndNumber($jobName, $number) {
    return Job::getUrlFromName($jobName)."/$number";
  }

  protected function isImpValid() {
    $result = TRUE;
    if (!$this->number) $result = FALSE;

    return $result;
  }

  public static function multiFactory(Jenkins $conn, $jobName, array $data) {
    $builds = array();
    foreach ($data as $buildData) $builds[] = Build::factory($conn, $jobName, new JsonData($buildData));

    return $builds;
  }

  protected function updateImpProperties(JsonData $data) {
    //number and url are not updated since, once they are set, they should never change

    $this->actions            = $data->get('actions', array());
    $this->artifacts          = $data->get('artifacts', array());
    $this->building           = $data->get('building', FALSE);
    $this->builtOn            = $data->get('builtOn', "");
    $this->changeSet          = $data->get('changeSet', array());
    $this->culprits           = $data->get('culprits', array()); //TODO: create User object
    $this->description        = $data->get('description', "");
    $this->duration           = $data->get('duration', "0");
    $this->estimatedDuration  = $data->get('estimatedDuration', "0");
    $this->executor           = $data->get('executor', "");
    $this->fullDisplayName    = $data->get('fullDisplayName', "");
    $this->id                 = $data->get('id', "");
    $this->keepLog            = $data->get('keepLog', FALSE);
    $this->result             = $data->get('result', "UNKNOWN");
    $this->timestamp          = $data->get('timestamp', "0");
  }
}
