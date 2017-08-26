<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ServeTest extends TestCase
{
    public function testEntityGetAttributes()
    {
       $this->accertSame('foo', '$output');
        return 1;
       $output = $this->runCommand('serve');
       $this->accertSame('foo', $output);
    }
}
