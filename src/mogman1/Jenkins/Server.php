<?php

namespace mogman1\Jenkins;

use mogman1\Jenkins\Server\HttpResponse;

/**
 * Facilitates communication to and from a Jenkins server
 * @author Shaun Carlson
 */
abstract class Server {
  /**
   * Performs a request against a Jenkins server
   * @param string $path Absolute path to hit (e.g. /jobs)
   * @param array $params Associative array of parameters to send as POST parameters
   * @throws \UnexpectedValueException If there is an error parsing the response
   * @return HttpResponse
   */
  abstract public function get($path, array $params);

  /**
   * Returns URL to server
   * @return string
   */
  abstract public function getServerUrl();
}
