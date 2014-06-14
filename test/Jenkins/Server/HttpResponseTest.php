<?php

use mogman1\Jenkins\Server\HttpResponse;
use mogman1\Jenkins\Exception\JenkinsConnectionException;

class HttpResponseTest extends PHPUnit_Framework_TestCase {
  public function testIsAndGetJson() {
    $header = "HTTP/1.1 200 OK\r\n";
    $body = "Content-Type: application/json\r\n\r\n";
    $body .= '{"hello": "world"}';

    $resp = new HttpResponse($header.$body, "/");
    $this->assertTrue($resp->isJson());
    $this->assertInstanceOf('mogman1\Jenkins\JsonData', $resp->getJson());
    $this->assertEquals('world', $resp->getJson()->get('hello'));

    $body .= 'bluh bluhbluh invalid now';
    $resp = new HttpResponse($header.$body, "/");
    $this->assertTrue($resp->isJson());
    try {
      $resp->getJson();
      $this->fail("No JenkinsConnectionException thrown");
    } catch (JenkinsConnectionException $e) {
      $this->assertEquals("Unable to parse returned JSON:\n".'{"hello": "world"}'.'bluh bluhbluh invalid now', $e->getMessage());
    }

    $body = "Content-Type: application/html\r\n\r\n<html></html>";
    $resp = new HttpResponse($header.$body, "/");
    $this->assertFalse($resp->isJson());
    try {
      $resp->getJson();
      $this->fail("No JenkinsConnectionException thrown");
    } catch (JenkinsConnectionException $e) {
      $this->assertEquals("Expected JSON content, received [application/html]", $e->getMessage());
    }
  }

  public function testGetBody() {
    $resp = new HttpResponse("HTTP/1.1 200 OK\r\n\r\nhi mom!", "/");
    $this->assertEquals("hi mom!", $resp->getBody());
  }

  public function testGetStatuses() {
    $resp = new HttpResponse("HTTP/1.1 200 OK\r\n\r\nhi mom!", "/");
    $this->assertEquals(200, $resp->getStatusCode());
    $this->assertEquals("OK", $resp->getStatusText());

    $resp = new HttpResponse("HTTP/1.1 201 Created Foo\r\n\r\nhi mom!", "/");
    $this->assertEquals(201, $resp->getStatusCode());
    $this->assertEquals("Created Foo", $resp->getStatusText());
  }

  public function testGetRawResponse() {
    $t = "HTTP/1.1 200 OK\r\n\r\nhi mom!";
    $resp = new HttpResponse($t, "/");
    $this->assertEquals($t, $resp->getRawResponse());
  }

  public function testGetHeader() {
    $resp = new HttpResponse("HTTP/1.1 200 OK\r\nHeader1: hi!\r\nHeader2: bye!\r\n\r\n", "/");
    $this->assertEquals("hi!", $resp->getHeader("Header1"));
    $this->assertEquals("hi!", $resp->getHeader("header1"));
    $this->assertEquals("bye!", $resp->getHeader("Header2"));
    $this->assertEquals("bye!", $resp->getHeader("header2"));
    $this->assertEquals("", $resp->getHeader("Header3"));
    $this->assertEquals("", $resp->getHeader("header3"));
  }

  public function testResponseTooShort() {
    try {
      $resp = new HttpResponse("HTTP/1.1 200 OK", "/");
      $this->fail("No UnexpectedValueExceptionThrown");
    } catch (\UnexpectedValueException $e) {
      $this->assertEquals("Unrecognized response data: ", substr($e->getMessage(), 0, 28));
    }
  }

  public function testInvalidHttpStatusLine() {
    try {
      $resp = new HttpResponse("HTTP/1.1 OK\r\nHeader: value\r\n\r\n", "/");
      $this->fail("No UnexpectedValueExceptionThrown");
    } catch (\UnexpectedValueException $e) {
      $this->assertEquals("Unrecognized HTTP status code line: ", substr($e->getMessage(), 0, 36));
    }
  }

  public function testInvalidHeaderField() {
    try {
      $resp = new HttpResponse("HTTP/1.1 200 OK\r\nHeader: value\r\nInvalidHeader\r\n\r\n", "/");
      $this->fail("No UnexpectedValueExceptionThrown");
    } catch (\UnexpectedValueException $e) {
      $this->assertEquals("Unrecognized header line: ", substr($e->getMessage(), 0, 26));
    }
  }
}
