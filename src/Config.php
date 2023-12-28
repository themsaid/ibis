<?php

namespace Ibis;

use Illuminate\Support\Str;

class Config
{
    /**
     * @var array
     */
    public $config;

    /**
     * @var string
     */
    public $ibisConfigPath;
    public $contentPath;

    public function __construct(public $workingPath = "")
    {
        if ($workingPath === "") {
            $this->workingPath = "./";
        } elseif (!is_dir($workingPath)) {
            $this->workingPath = "./";
        }
        $this->ibisConfigPath = $this->workingPath . '/ibis.php';
        $this->config = require $this->ibisConfigPath;
    }

    public static function load($directory = ""): self
    {
        return new self($directory);
    }

    public function setContentPath($directory = ""): bool
    {
        $this->contentPath = $directory;
        if ($this->contentPath === "") {
            $this->contentPath = $this->workingPath . DIRECTORY_SEPARATOR . "content";
        }
        return is_dir($this->contentPath);

    }

    /**
     *
     */
    public function title()
    {
        return $this->config['title'];
    }

    /**
     *
     */
    public function outputFileName()
    {
        return Str::slug($this->title());
    }

    /**
     *
     */
    public function author()
    {
        return $this->config['author'];
    }

}
