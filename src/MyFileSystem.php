<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

/**
 * Description of MyFileSystem
 *
 * @author tomasz
 */
class MyFileSystem
{
    private $readSystem;
    private $writeSystem;
    private $inputFolder;
    private $outputFolder;
    private $outputSuffix;
    private $counter;
    private $normalize;
    private $normalizeText;

    /*
     * @param $inputfolder string - input folder for reading files
     * @param $putputFolder string - folder where files will be saves
     *  if null, foder will be $inputFolder + /output/
     * @param $normalize - interface for changing filenames      
     */

    public function __construct(string $inputFolder, string $outputFolder = null, NormalizeFileNameInterface $normalize = null)
    {
        $this->inputFolder = $inputFolder;
        $adapter = new Local($inputFolder);
        $this->readSystem = new Filesystem($adapter);

        if ($outputFolder) {
            $adapter = new Local($outputFolder);
            $this->writeSystem = new Filesystem($adapter);
            $this->outputFolder = $outputFolder;
            $this->outputSuffix = '';
        } else {
            $this->writeSystem = $this->readSystem;
            $this->outputFolder = null;
            $this->outputSuffix = '/output/';
        }

        $this->normalize = $normalize;
        $this->counter = 0;
    }

    private function isGoodToCopy($newFileName, $file)
    {
        return !$this->writeSystem->has($this->outputSuffix . $newFileName) && $file['type'] === 'file';
    }

    private function isGoodToRename($file, $newFileName)
    {
        return $file['type'] === 'file' && $file['basename'] !== $newFileName;
    }

    private function normalizeFileName($file)
    {
        return ($this->normalize ?
                $this->normalize->normalize($file['basename']) : $file['basename']);
    }

    public function copyFiles()
    {
        $contents = $this->readSystem->listContents();
        foreach ($contents as $file) {
            $newFileName = $this->normalizeFileName($file);
            if ($this->isGoodToCopy($newFileName, $file)) {
                if ($this->outputFolder) {
                    $fileContent = $this->readSystem->read($file['basename']);
                    $this->writeSystem->write($newFileName, $fileContent);
                } else {
                    $this->readSystem->copy($file['basename'], $this->outputSuffix . $newFileName);
                }
                $this->counter++;
            }
        }
        $this->normalizeText = 'copied';
    }

    public function renameFiles()
    {
        if ($this->normalize) {
            $contents = $this->readSystem->listContents();
            foreach ($contents as $file) {
                $newFileName = $this->normalizeFileName($file);
                if ($this->isGoodToRename($file, $newFileName)) {
                    $this->readSystem->rename($file['basename'], $newFileName);
                    $this->counter++;
                }
            }
        }
        $this->normalizeText = 'normalized';
    }

    public function showSummary()
    {
        if ($this->counter) {
            echo $this->counter . ' files were ' . $this->normalizeText . '.' . PHP_EOL;
        } else {
            echo 'Nothing ' . $this->normalizeText . '.' . PHP_EOL;
        }
    }

}
