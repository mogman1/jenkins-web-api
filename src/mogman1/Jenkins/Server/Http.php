<?php

namespace mogman1\Jenkins\Server;

use mogman1\Jenkins\Server;
use mogman1\Jenkins\Exception\JenkinsConnectionException;

/**
 * HTTP implementation of connection to Jenkins server
 * @author Shaun Carlson
 * @codeCoverageIgnore
 */
class Http extends Server {
  /**
   * URL to server with http(s) included, but no trailing slash
   * @var string
   */
  protected $jenkinsUrl;

  /**
   * User connecting to Jenkins
   * @var string
   */
  protected $jenkinsUser;

  /**
   * Access token used to authenticate user
   * @var string
   */
  protected $accessToken;

  /**
   * @param string $jenkinsUrl  URL to server with http(s) included, but no trailing slash
   * @param string $jenkinsUser User connecting to Jenkins
   * @param string $accessToken Access token used to authenticate user
   */
  public function __construct($jenkinsUrl, $jenkinsUser, $accessToken) {
    $this->jenkinsUrl = $jenkinsUrl;
    $this->jenkinsUser = $jenkinsUser;
    $this->accessToken = $accessToken;
  }

  /**
   * (non-PHPdoc)
   * @see \mogman1\Jenkins\Server::get()
   */
  public function get($path, array $params=array()) {
    $params['pretty'] = "true";
    $request = $this->jenkinsUrl.$path."/api/json";
    $crumbData = $this->getCrumb();

    $curl = curl_init($request);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, Array($crumbData['crumbRequestField'].": ".$crumbData['crumb']));
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($curl, CURLOPT_USERPWD, "$this->jenkinsUser:$this->accessToken");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $resp = curl_exec($curl);
    curl_close($curl);

    return new HttpResponse($resp, $request, $params);
  }

  public function getCrumb() {
    $request = $this->jenkinsUrl."/crumbIssuer/api/json";
    $curl = curl_init($request);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_USERPWD, "$this->jenkinsUser:$this->accessToken");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $resp = curl_exec($curl);
    curl_close($curl);

    $resp = new HttpResponse($resp, $request);
    $crumbData = json_decode($resp->getBody(), TRUE);
    if (!isset($crumbData['crumb']) || !isset($crumbData['crumbRequestField'])) {
      throw new JenkinsConnectionException("Unrecognized format of crumb data");
    }

    return $crumbData;
  }

  public function getServerUrl() {
    return $this->jenkinsUrl;
  }
}
