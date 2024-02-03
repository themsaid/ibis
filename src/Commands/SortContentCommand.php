<?php

namespace Ibis\Commands;

use Ibis\Config;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SortContentCommand extends Command
{
    private ?\Illuminate\Filesystem\Filesystem $disk = null;

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('content:sort')
            ->setDescription('Sort the files in the content directory.');
    }

    /**
     * Execute the command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->disk = new Filesystem();

        $currentPath = getcwd();

        collect($this->disk->files(
            Config::buildPath(
                $currentPath,
                'content'
            )
        ))->each(function ($file, $index) use ($currentPath): void {
            $markdown = $this->disk->get(
                $file->getPathname()
            );

            $newName = sprintf(
                '%03d%s',
                (int) $index + 1,
                str_replace(['#', '##', '###'], '', explode("\n", $markdown)[0])
            );

            $this->disk->move(
                $file->getPathName(),
                Config::buildPath(
                    $currentPath,
                    'content',
                    Str::slug($newName) . '.md'
                )
            );
        });

        return 0;
    }
}
