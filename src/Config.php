<?php

namespace Ibis;

use Illuminate\Support\Str;

class Config
{
    /**
     * @var array
     */
    public $config;

    public string $ibisConfigPath;

    public $contentPath;

    public function __construct(public $workingPath = "")
    {
        if ($workingPath === "") {
            $this->workingPath = "./";
        } elseif (!is_dir($workingPath)) {
            $this->workingPath = "./";
        }

        $this->ibisConfigPath = self::buildPath($this->workingPath, 'ibis.php');
        $this->config = require $this->ibisConfigPath;
    }

    public static function load($directory = ""): self
    {
        return new self($directory);
    }

    public static function buildPath(...$pathElements): string
    {
        //$paths = func_get_args();
        $last_key = count($pathElements) - 1;
        array_walk($pathElements, static function (&$val, $key) use ($last_key): void {
            $val = match ($key) {
                0 => rtrim($val, '/ '),
                $last_key => ltrim($val, '/ '),
                default => trim($val, '/ '),
            };
        });
        $first = array_shift($pathElements);
        $last = array_pop($pathElements);
        $paths = array_filter($pathElements); // clean empty elements to prevent double slashes
        array_unshift($paths, $first);
        $paths[] = $last;

        return implode('/', $paths);
    }

    public function setContentPath($directory = ""): bool
    {
        $this->contentPath = $directory;
        if ($this->contentPath === "") {
            $this->contentPath = self::buildPath($this->workingPath, "content");
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
