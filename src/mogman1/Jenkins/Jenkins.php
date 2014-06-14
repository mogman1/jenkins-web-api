<?php

namespace mogman1\Jenkins;

use mogman1\Jenkins\Exception\JenkinsConnectionException;
use mogman1\Jenkins\ApiObject\Job;
use mogman1\Jenkins\ApiObject\Node;
use mogman1\Jenkins\Server\HttpResponse;

class Jenkins {
  /**
   * Connection to Jenkins
   * @var Server
   */
  private $conn;

  public function __construct(Server $jenkinsConnection) {
    $this->conn = $jenkinsConnection;
  }

  public function getJob($name) {
    return Job::factory($this, $this->get(Job::getUrlFromName($name))->getJson());
  }

  /**
   * Returns information on this server node
   *
   * @return \mogman1\Jenkins\Node
   */
  public function getNodeInfo() {
    return Node::factory($this, $this->get("/")->getJson());
  }

  /**
   * Gets URL to server
   * @return string
   */
  public function getServerUrl() {
    return $this->conn->getServerUrl();
  }

  /**
   * Fetches response from Jenkins
   *
   * @param string $path Resource to fetch from Jenkins
   * @param array  $params Associative array of parameters to send as POST parameters
   * @throws JenkinsConnectionException
   * @return HttpResponse
   */
  public function get($path, array $params=array()) {
    $response = null;
    try {
      $response = $this->conn->get($path, $params);
    } catch (\UnexpectedValueException $e) {
      throw new JenkinsConnectionException($e->getMessage(), 0, $e);
    }

    if (!in_array($response->getStatusCode(), array(200, 201))) {
      throw new JenkinsConnectionException("Jenkins connection failed [".$response->getStatusCode()." ".$response->getStatusText()."]");
    }

    return $response;
  }
}
