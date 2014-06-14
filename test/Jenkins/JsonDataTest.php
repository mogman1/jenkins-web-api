<?php

use mogman1\Jenkins\JsonData;

class JsonDataTest extends PHPUnit_Framework_TestCase {
  public function testGet() {
    $testJson = Array('null' => NULL, 'false' => false, 'zero' => 0, "empty" => "", "summit" => "hello");
    $json = new JsonData(json_decode(json_encode($testJson), TRUE));
    $this->assertNotNull($json->get('null'));
    $this->assertEquals("", $json->get('null'));
    $this->assertTrue($json->get('null', TRUE));

    $this->assertNotNull($json->get('nothing'));
    $this->assertEquals("", $json->get('nothing'));
    $this->assertTrue($json->get('nothing', TRUE));

    $this->assertFalse($json->get('false'));
    $this->assertFalse($json->get('false', TRUE));

    $this->assertEquals(0, $json->get('zero'));
    $this->assertEquals(0, $json->get('zero', 1));

    $this->assertEquals("", $json->get('empty'));
    $this->assertEquals("", $json->get('empty', 1));

    $this->assertEquals("hello", $json->get('summit'));
    $this->assertEquals("hello", $json->get('summit', 1));
  }
}
