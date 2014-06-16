<?php

namespace mogman1\Jenkins\ApiObject;

use mogman1\Jenkins\ApiObject;
use mogman1\Jenkins\JsonData;
use mogman1\Jenkins\Jenkins;
use mogman1\Jenkins\Exception\InvalidApiObjectException;

class QueueItem extends ApiObject {
  /**
   * I don't know
   *
   * TODO: Research
   * @var array
   */
  public $actions;

  /**
   * Build is being blocked by another build already in progress, required resources
   * are not available, or otherwise blocked by Task::isBuildBlocked()
   *
   * @see https://github.com/jenkinsci/jenkins/blob/master/core/src/main/java/hudson/model/Queue.java
   * @var bool
   */
  public $blocked;

  /**
   * Whether this item is buildable or not.  Documentation makes me think this
   * will always be FALSE.
   *
   * @see https://github.com/jenkinsci/jenkins/blob/master/core/src/main/java/hudson/model/Queue.java
   * @var bool
   */
  public $buildable;

  /**
   * Whether the queue item was cancelled before being executed.
   * @var bool
   */
  public $cancelled;

  /**
   * As with $task, technically this is anything implementing the Executable interface
   * found in Queue.java, but in practice this seems to always be a Build.  If I can find
   * a use case where this isn't a Build, I'll update accordingly.
   *
   * @see https://github.com/jenkinsci/jenkins/blob/master/core/src/main/java/hudson/model/Queue.java
   * @var Build
   */
  public $executable;

  /**
   * Queue item ID, as distinct from build number, as distinct from build ID.
   *
   * @see \mogman1\Jenkins\ApiObject\Build
   * @var int
   */
  public $id;

  /**
   * Timestamp, in milliseconds, when this item entered the build queue
   *
   * @var int
   */
  public $inQueueSince;

  /**
   * Custom build parameters associated with this soon-to-be build.
   *
   * @var array
   */
  public $params;

  /**
   * Whether this queue item is stuck and unable to proceed through queue for
   * whatever reason
   *
   * @see https://github.com/jenkinsci/jenkins/blob/master/core/src/main/java/hudson/model/Queue.java
   * @var bool
   */
  public $stuck;

  /**
   * Technically something that implements interface Task, but in practice this always
   * seems to be a job.  If I can find a use case where this isn't a job, I'll update
   * accordingly.
   *
   * @var Job
   */
  public $task;

  /**
   * A timestamp, in milliseconds.  I think of when the request was made?  It's usually
   * later than $inQueueSince, and that's all I can figure.  When an $executable finally
   * becomes available, this value disappears.
   *
   * @var int
   */
  public $timestamp;

  /**
   * Why this item is in the queue, as opposed to doing something useful, like being
   * worked on.
   *
   * @var string
   */
  public $why;

  public function __construct(Jenkins $conn, $queueNumber) {
    $this->id = $queueNumber;
    parent::__construct($conn, QueueItem::getUrlFromQueueNumber($queueNumber));
  }

  /**
   * Constructs a Job from data assumed to have come from a Jenkins API call
   *
   * @param Jenkins $conn
   * @param JsonData $data
   * @return \mogman1\Jenkins\QueueItem
   */
  public static function factory(Jenkins $conn, JsonData $data) {
    $id = $data->get("id", "");
    if ($id == "")  throw new InvalidApiObjectException("'id' is required, but not found in build data");
    $item = new QueueItem($conn, $id);
    $item->updateProperties($data);

    return $item;
  }

  public static function getUrlFromQueueNumber($queueNumber) {
    return "/queue/item/$queueNumber";
  }

  /**
   * (non-PHPdoc)
   * @see \mogman1\Jenkins\ApiObject::isImpValid()
   */
  protected function isImpValid() {
    return ($this->id != "" && $this->id >= 0);
  }

  /**
   * Params come from Jenkins in a string with parameters separated by a newline, and
   * parameters themselves being formatted as "key=value".  This parses that string
   * and returns an associative array of those parameters.
   *
   * @param string $pStr
   * @return array
   */
  private function parseParamsString($pStr) {
    $lines = explode("\n", $pStr);
    $params = array();
    foreach ($lines as $line) {
      $line = trim($line);
      if (!$line) continue;
      $temp = explode("=", $line);
      if (count($temp) < 2) {
        //shouldn't happen, but stick in the whole line rather than lose it
        $params[$line] = "";
      } else {
        //in case an equal sign was a part of the value, which also shouldn't happen, but handle
        //for it just in case
        $key = $temp[0]; unset($temp[0]);
        $params[$key] = implode("=", $temp);
      }
    }

    return $params;
  }

  protected function updateImpProperties(JsonData $data) {
    //id and url are not updated since, once they are set, they should never change

    //TODO: figure out what actions are
    $this->actions      = $data->get('actions', array());
    $this->blocked      = $data->get('blocked', FALSE);
    $this->buildable    = $data->get('buildable', FALSE);
    $this->cancelled    = $data->get('cancelled', FALSE);
    $this->inQueueSince = $data->get('inQueueSince', "0");
    $this->params       = $this->parseParamsString($data->get('params', ""));
    $this->stuck        = $data->get('stuck', FALSE);
    $this->timestamp    = $data->get('timestamp', "0");
    $this->why          = $data->get('why', "");

    $task = $data->get('task', array());
    $this->task = ($task) ? Job::factory($this->conn, new JsonData($data->get('task', array()))) : NULL;

    $build = $data->get('executable', array());
    $this->executable = ($build) ? Build::factory($this->conn, $this->task->name, new JsonData($build)) : NULL;
  }
}
