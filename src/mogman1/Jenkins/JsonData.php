<?php

namespace mogman1\Jenkins;

class JsonData {
  /**
   * @var array
   */
  private $data;
  public function __construct(array $data) {
    $this->data = $data;
  }

  /**
   * If the data element exists, and is not null, the value is returned.  Otherwise
   * the default value is returned
   *
   * @param string $key
   * @param mixed  $default
   * @return mixed
   */
  public function get($key, $default="") {
    return (isset($this->data[$key]) && !is_null($this->data[$key])) ? $this->data[$key] : $default;
  }
}
