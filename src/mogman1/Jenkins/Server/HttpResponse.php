<?php

namespace mogman1\Jenkins\Server;

use mogman1\Jenkins\Exception\JenkinsConnectionException;
use mogman1\Jenkins\JsonData;

/**
 * Representation of HTTP response from Jenkins server
 * @author Shaun Carlson
 */
class HttpResponse {
  /**
   * Content sent as part of response
   * @var string
   */
  private $body;

  /**
   * Array of key-value pairs found in header
   * @var array
   */
  private $headers;

  /**
   * Version of HTTP encountered
   * @var string
   */
  private $http_version;

  /**
   * Status code number (e.g. 200)
   * @var int
   */
  private $status_code_number;

  /**
   * Text representation of status_code_number (e.g. OK)
   * @var string
   */
  private $status_code_text;

  /**
   * Raw text value of response
   * @var string
   */
  private $rawResponse;

  /**
   * Path on Jenkins server that the request was made to
   * @var string
   */
  private $request;

  /**
   * POST params sent to $request
   * @var array
   */
  private $requestParams;

  public function __construct($response, $request, $requestParams=array()) {
    $this->rawResponse = $response;
    $this->request = $request;
    $this->requestParams = $requestParams;

    $lines = explode("\n", $response);
    if (count($lines) < 2) {
      throw new \UnexpectedValueException("Unrecognized response data: ".$response);
    }

    $statusPieces = explode(" ", trim($lines[0]));
    if (count($statusPieces) < 3) {
      throw new \UnexpectedValueException("Unrecognized HTTP status code line: ".$lines[0]);
    }

    //text representation of status could be multiple words.  Pull off the first two elements,
    //which will always be the HTTP version and status code, then implode whatever remains
    $this->http_version = $statusPieces[0]; unset($statusPieces[0]);
    $this->status_code_number = $statusPieces[1]; unset($statusPieces[1]);
    $this->status_code_text = implode(" ", $statusPieces);
    unset($lines[0]);

    $this->headers = array();
    foreach ($lines as $num => $line) {
      $line = trim($line);
      unset($lines[$num]);
      if ($line == "") break;

      $pieces = explode(": ", $line);
      if (count($pieces) < 2) {
        throw new \UnexpectedValueException("Unrecognized header line: [$line]");
      }

      $key = $pieces[0];
      unset($pieces[0]);
      $this->headers[strtolower($key)] = implode(": ", $pieces);
    }

    $this->body = implode("\n", $lines);
  }

  /**
   * Returns response body
   * @return string
   */
  public function getBody() {
    return $this->body;
  }

  /**
   * Attempts to fetch data as JSON.  If the response did not return JSON data, or if it is
   * unparseable, an exception will be thrown.
   *
   * @throws JenkinsConnectionException
   * @return JsonData
   */
  public function getJson() {
    if (!$this->isJson()) {
      throw new JenkinsConnectionException("Expected JSON content, received [".$this->getHeader("Content-Type")."]");
    }

    $data = json_decode($this->getBody(), TRUE);
    if (is_null($data)) {
      throw new JenkinsConnectionException("Unable to parse returned JSON:\n".$this->getBody());
    }

    return new JsonData($data);
  }

  /**
   * Returns a header value.  If no such header value was sent, an empty string is returned.  Keys
   * can be passed in without concern of case-sensitivity, all lookups are done in a case-insensitive
   * manner.
   *
   * @param string $headerKey
   * @return string
   */
  public function getHeader($headerKey) {
    $headerKey = strtolower($headerKey);
    return (isset($this->headers[$headerKey])) ? $this->headers[$headerKey] : "";
  }

  /**
   * Returns raw response, including header
   * @return string
   */
  public function getRawResponse() {
    return $this->rawResponse;
  }

  /**
   * Returns HTTP status code (e.g. 200)
   * @return int
   */
  public function getStatusCode() {
    return $this->status_code_number;
  }

  /**
   * Returns the HTTP text representation of the status code (e.g. OK)
   * @return string
   */
  public function getStatusText() {
    return $this->status_code_text;
  }

  /**
   * Returns TRUE if the Content-Type header indicates it is json
   * @return boolean
   */
  public function isJson() {
    return (stripos($this->getHeader("Content-Type"), "json") !== FALSE);
  }
}
