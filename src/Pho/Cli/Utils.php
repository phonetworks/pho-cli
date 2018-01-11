<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Cli;

/**
 * Various toolbelt methods to use within Pho\Cli context.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Utils 
{
    /**
     * Recursively copy files from one directory to another
     * 
     * @link https://ben.lobaugh.net/blog/864/php-5-recursively-move-or-copy-files
     * 
     * @param String $src - Source of files being moved
     * @param String $dest - Destination of files being moved
     */
    public static function rcopy(string $src, string $dest): void
    {
        // If source is not a directory stop processing
        if(!is_dir($src)) return;

        // If the destination directory does not exist create it
        if(!is_dir($dest)) { 
            if(!mkdir($dest)) {
                // If the destination directory could not be created stop processing
                return;
            }    
        }

        // Open the source directory to read in files
        $i = new \DirectoryIterator($src);
        foreach($i as $f) {
            if($f->isFile()) {
                \copy($f->getRealPath(), "$dest/" . $f->getFilename());
            } else if(!$f->isDot() && $f->isDir()) {
                self::rcopy($f->getRealPath(), "$dest/$f");
            }
        }
    }

    /**
     * Checks if the given dir has any files in it.
     * 
     * @link https://stackoverflow.com/questions/7497733/how-can-use-php-to-check-if-a-directory-is-empty
     *
     * @param string $dir Directory to check
     * 
     * @return boolean
     */
    public static function dirEmpty(string $dir): bool
    {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
          if ($entry != "." && $entry != ".." && $entry != "README.md") {
            return false;
          }
        }
        return true;
    }

    /**
     * Recursive directory deleter
     * 
     * @link https://stackoverflow.com/questions/4594180/deleting-all-files-from-a-folder-using-php
     *
     * @param string $dir Directory to truncate
     * 
     * @return void
     */
    public static function dirDel(string $dir): void
    {
        $di = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
        $ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ( $ri as $file ) {
            $file->isDir() ?  \rmdir($file) : \unlink($file);
        }
    }
}