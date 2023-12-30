<?php

namespace Ibis\Commands;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    private ?\Symfony\Component\Console\Output\OutputInterface $output = null;

    private ?\Illuminate\Filesystem\Filesystem $disk = null;


    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('init')
            ->addOption(
                'workingdir',
                'd',
                InputOption::VALUE_OPTIONAL,
                'The path of the working directory where `ibis.php` and `assets` directory will be created',
                ''
            )
            ->setDescription('Initialize a new project in the working directory (current dir by default).');
    }

    /**
     * Execute the command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->disk = new Filesystem();
        $this->output = $output;

        $workingPath = $input->getOption('workingdir');
        if ($workingPath === "") {
            $workingPath = "./";
        } elseif (!is_dir($workingPath)) {
            $workingPath = "./";
        }
        $ibisConfigPath = $workingPath . '/ibis.php';
        $contentPath = $workingPath . '/content/';

        $this->output->writeln('<info>Creating config/assets directory as: ' . $workingPath . '</info>');
        $this->output->writeln('<info>Creating config file as: ' . $ibisConfigPath . '</info>');
        $this->output->writeln('<info>Creating content directory as: ' . $contentPath . '</info>');




        $currentPath = $workingPath;

        if ($this->disk->isDirectory($currentPath . '/assets')) {
            $this->output->writeln('');
            $this->output->writeln('<comment>Project already initialised!</comment>');

            return Command::INVALID;
        }

        $this->disk->makeDirectory(
            $currentPath . '/assets'
        );

        $this->disk->makeDirectory(
            $currentPath . '/assets/fonts'
        );

        $this->disk->makeDirectory(
            $currentPath . '/content'
        );

        $this->disk->copyDirectory(
            __DIR__ . '/../../stubs/content',
            $currentPath . '/content'
        );

        $this->disk->put(
            $currentPath . '/ibis.php',
            $this->disk->get(__DIR__ . '/../../stubs/ibis.php')
        );

        $this->disk->put(
            $currentPath . '/assets/cover.jpg',
            $this->disk->get(__DIR__ . '/../../stubs/assets/cover.jpg')
        );

        $this->disk->put(
            $currentPath . '/assets/theme-dark.html',
            $this->disk->get(__DIR__ . '/../../stubs/assets/theme-dark.html')
        );

        $this->disk->put(
            $currentPath . '/assets/theme-light.html',
            $this->disk->get(__DIR__ . '/../../stubs/assets/theme-light.html')
        );

        $this->disk->put(
            $currentPath . '/assets/style.css',
            $this->disk->get(__DIR__ . '/../../stubs/assets/style.css')
        );

        $this->output->writeln('');
        $this->output->writeln('<info>Done!</info>');

        return Command::SUCCESS;
    }
}
