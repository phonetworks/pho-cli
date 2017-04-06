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

    public function setUp() {
      //echo "hello world";
    }

    public function tearDown() {}

    public function testHello() {
      $last_line = exec(
        sprintf("php %s hello",
            escapeshellarg(glob(__DIR__."/../bin/pho.php")[0])
        )
      );
      $this->assertEquals("foo", $last_line);
    }
}
