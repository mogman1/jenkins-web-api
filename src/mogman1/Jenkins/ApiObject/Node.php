<?php

namespace mogman1\Jenkins\ApiObject;

use mogman1\Jenkins\ApiObject;
use mogman1\Jenkins\Jenkins;
use mogman1\Jenkins\JsonData;

class Node extends ApiObject {
  /**
   * I don't know
   * TODO: Research
   * @var array
   */
  public $assignedLabels;

  /**
   * Always seems to be null
   * TODO: Research
   * @var unknown
   */
  public $description;

  /**
   * Array of jobs available on this node
   * @var array[Job]
   */
  public $jobs;

  /**
   * I don't know
   * @var string
   */
  public $mode;

  /**
   * Description of this node (unclear distinction with $description)
   * @var string
   */
  public $nodeDescription;

  /**
   * Name of this node
   * @var string
   */
  public $nodeName;

  /**
   * Number of available executors of tasks on this node
   * @var int
   */
  public $numExecutors;

  /**
   * Uncertain of format
   * TODO: Research
   * @var array
   */
  public $overallLoad;

  /**
   * Primary view for this node
   * @var View
   */
  public $primaryView;

  /**
   * I don't know
   * TODO: Research
   * @var bool
   */
  public $quietingDown;

  /**
   * I don't know
   * TODO: Research
   * @var int
   */
  public $slaveAgentPort;

  /**
   * I don't know
   * TODO: Research
   * @var array
   */
  public $unlabeledLoad;

  /**
   * Whether CSRF crumbs are needed to make requests
   * @var bool
   */
  public $useCrumbs;

  /**
   * Presumably whether to require authentication or not
   * @var bool
   */
  public $useSecurity;

  /**
   * All views available on this node
   * @var array[View]
   */
  public $views;

  public function __construct(Jenkins $conn) {
    parent::__construct($conn, "/");
  }

  protected function isImpValid() {
    return TRUE;
  }

  /**
   * Constructs a node from data assumed to have come from a Jenkins API call
   *
   * @param Jenkins $conn
   * @param JsonData $data
   * @return \mogman1\Jenkins\Node
   */
  public static function factory(Jenkins $conn, JsonData $data) {
    $node = new Node($conn);
    $node->updateProperties($data);

    return $node;
  }

  protected function updateImpProperties(JsonData $data) {
    $this->assignedLabels = $data->get('assignedLabels', array());
    $this->description    = $data->get('description');
    $this->jobs           = Job::multiFactory($this->conn, $data->get('jobs', array()));
    $this->mode           = $data->get('mode', "UNKNOWN");
    $this->nodeDescription= $data->get('nodeDescription');
    $this->nodeName       = $data->get('nodeName');
    $this->numExecutors   = $data->get('numExecutors', 0);
    $this->overallLoad    = $data->get('overallLoad', array());
    $this->quietingDown   = $data->get('quietingDown', FALSE);
    $this->slaveAgentPort = $data->get('slaveAgentPort', 0);
    $this->unlabeledLoad  = $data->get('unlabeledLoad', array());
    $this->useCrumbs      = $data->get('useCrumbs', FALSE);
    $this->useSecurity    = $data->get('useSecurity', FALSE);
    $this->views          = View::multiFactory($this->conn, $data->get('views', array()));

    $viewData = $data->get('primaryView', array());
    $this->primaryView = ($viewData) ? View::factory($this->conn, new JsonData($data->get('primaryView', array()))) : NULL;
  }
}
