<?php

namespace mogman1\Jenkins;

use mogman1\Jenkins\Exception\InvalidApiObjectException;

abstract class ApiObject {
  /**
   * @var Jenkins
   */
  protected $conn;

  /**
   * Path on Jenkins server to fetch information on this API object from
   * @var string
   */
  public $url;

  public function __construct(Jenkins $conn, $url) {
    $this->conn = $conn;
    $this->url = $url;
  }

  /**
   * Returns full URL with server info, instead of just path (which is stored in $url)
   * @return string
   */
  public function getUrl() {
    return $this->conn->getServerUrl().$this->url;
  }

  /**
   * Determines if object is in a valid state
   * @return bool
   */
  public function isValid() {
    if (!$this->url) return FALSE;
    if (!($this->conn instanceof Jenkins)) return FALSE;

    return $this->isImpValid();
  }

  /**
   * Required method for determining if implementing class is in a valid
   * state.
   *
   * @return bool
   */
  abstract protected function isImpValid();

  /**
   * Updates object with information coming from Jenkins server
   * @throws InvalidApiObjectException
   */
  public function update() {
    $jsonData = $this->conn->get($this->url)->getJson();
    $this->updateProperties($jsonData);
  }

  /**
   * Receives data from a Jenkins API call and updates object properties
   * @param JsonData $data
   * @throws InvalidApiObjectException
   */
  public function updateProperties(JsonData $data) {
    $this->updateImpProperties($data);
    if (!$this->isValid()) throw new InvalidApiObjectException("API object in invalid state");
  }

  /**
   * Required method for implementing class to update properties from data.
   * @param JsonData $data
   */
  abstract protected function updateImpProperties(JsonData $data);
}
