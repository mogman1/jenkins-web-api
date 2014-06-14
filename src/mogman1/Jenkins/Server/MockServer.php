<?php

namespace mogman1\Jenkins\Server;

use mogman1\Jenkins\Server;
use mogman1\Jenkins\Server\HttpResponse;

/**
 * Mock connection to Jenkins for use in testing API class Jenkins
 * @author Shaun Carlson
 */
class MockServer extends Server {
  /**
   * @var array
   */
  private $response = array();

  public function clearResponses() {
    $this->response = array();
  }

  /**
   * (non-PHPdoc)
   * @see \mogman1\Jenkins\Server::get()
   */
  public function get($path, array $params=array()) {
    if (!count($this->response)) throw new \UnderflowException("No responses queued in mock server");

    return new HttpResponse(array_shift($this->response), $path, $params);
  }

  /**
   * (non-PHPdoc)
   * @see \mogman1\Jenkins\Server::getServerUrl()
   */
  public function getServerUrl() {
    return "";
  }

  /**
   * Adds a response to be fetched by a future call to this "server".  Responses
   * will be served in FIFO
   * @param string $response
   */
  public function queueResponse($response) {
    array_push($this->response, $response);
  }

  /**
   * Clears the response queue and pushes this new element
   * @param string $response
   */
  public function setResponse($response) {
    $this->clearResponses();
    $this->queueResponse($response);
  }
}
