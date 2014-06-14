<?php

namespace mogman1\Jenkins\ApiObject;

use mogman1\Jenkins\ApiObject;
use mogman1\Jenkins\Jenkins;
use mogman1\Jenkins\JsonData;
use mogman1\Jenkins\Exception\InvalidApiObjectException;

class View extends ApiObject {
  /**
   * Name of this view
   * @var string
   */
  public $name;

  public function __construct(Jenkins $conn, $url, $name) {
    parent::__construct($conn, $url);

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
   * Constructs a View from data assumed to have come from a Jenkins API call
   *
   * @param Jenkins $conn
   * @param JsonData $data
   * @return \mogman1\Jenkins\View
   */
  public static function factory(Jenkins $conn, JsonData $data) {
    $name = $data->get("name", "");
    $url  = $data->get("url", "");
    if (!$name) throw new InvalidApiObjectException("'name' is required, but not found in build data");
    if (!$url)  throw new InvalidApiObjectException("'url' is required, but not found in build data");
    $view = new View($conn, $url, $name);
    $view->updateProperties($data);

    return $view;
  }

  /**
   * Constructs an array of views from data, which is assumed to be an array of data that
   * would have come from a Jenkins API call
   *
   * @param Jenkins $conn
   * @param array $data
   * @return array[\mogman1\Jenkins\View]
   */
  public static function multiFactory(Jenkins $conn, array $data) {
    $views = array();
    foreach ($data as $viewData) $views[] = View::factory($conn, new JsonData($viewData));

    return $views;
  }

  /**
   * (non-PHPdoc)
   * @see \mogman1\Jenkins\ApiObject::update()
   */
  public function update() {
    throw new \RuntimeException("update not implemented for views");
  }

  /**
   * (non-PHPdoc)
   * @see \mogman1\Jenkins\ApiObject::updateImpProperties()
   */
  protected function updateImpProperties(JsonData $data) {
    $this->name  = $data->get('name', "");
  }
}
