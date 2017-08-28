<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestCase extends \PHPUnit\Framework\TestCase
{
    private $php;

    public function setUp()
    {
        $this->php = getenv("PHP_BIN");
    }

    public function tearDown()
    {
        unset($this->php);
    }

    protected function runCommand($cmd = 'help', $max_seconds = 20)
    {
        $string = sprintf("%s %s",
                escapeshellcmd('php'), //$this->php
                escapeshellarg(getcwd().DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'pho.php')
            );
        if (!file_exists('tests/logs')) mkdir('tests/logs');
        $f = fopen('tests/logs/output.txt', 'w'); fclose($f);
        $f = fopen('tests/logs/error-output.txt', 'w'); fclose($f);
        $descriptorspec = array(
           0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
           1 => array("file", 'tests/logs/output.txt', "w"),  // stdout is a pipe that the child will write to
           2 => array("file", "tests/logs/error-output.txt", "w") // stderr is a file to write to
        );

        $stream = proc_open($string.' '.escapeshellcmd($cmd), $descriptorspec, $pipes);
        if (is_resource($stream)) {
          
          $time = time() + $max_microseconds;
          
          do {
            $etat=proc_get_status($stream);
            sleep(1);
          } while ($etat['running']==FALSE || $time > time());

          proc_close($stream);
          $output = file_get_contents('tests/logs/output.txt').file_get_contents('tests/logs/error-output.txt');

          return $output;
        }

        return '';
    }
}
