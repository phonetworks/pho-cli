<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestCmd extends \PHPUnit\Framework\TestCase {

    private $php;

    public function setUp() {
      $this->php = getenv("PHP_BIN");
    }

    public function tearDown() {
      unset($this->php);
    }

    public function testHello() {
      $last_line = exec(
        sprintf("%s %s hello",
            escapeshellcmd($this->php),
            escapeshellarg(glob(__DIR__."/../bin/pho.php")[0])
        )
      );
      $this->assertEquals("foo", $last_line);
    }
}
