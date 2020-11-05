<?php

namespace Ibis\Commands;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SortContentCommand extends Command
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Filesystem
     */
    private $disk;

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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->disk = new Filesystem();

        $currentPath = getcwd();

        collect($this->disk->files($currentPath.'/content'))->each(function ($file, $index) use ($currentPath) {
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
                $currentPath.'/content/'.Str::slug($newName).'.md'
            );
        });

        return 0;
    }
}
