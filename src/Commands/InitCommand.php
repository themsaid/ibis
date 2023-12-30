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
    protected function execute(InputInterface $input, OutputInterface $output): int
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
        $assetsPath = $workingPath . '/assets';

        $this->output->writeln('<info>Creating directory/files for:</info>');
        $this->output->writeln('<info>✨ config/assets directory as: ' . $assetsPath . '</info>');

        if ($this->disk->isDirectory($assetsPath)) {
            $this->output->writeln('');
            $this->output->writeln('<comment>Project already initialised!</comment>');

            return Command::INVALID;
        }

        $this->disk->makeDirectory(
            $assetsPath
        );

        $this->disk->makeDirectory(
            $assetsPath . '/fonts'
        );

        $assetsToCopy = [
            'cover.jpg',
            'cover-ibis.webp',
            'theme-dark.html',
            'theme-light.html',
            'style.css'
        ];

        foreach ($assetsToCopy as $assetToCopy) {
            $this->disk->put(
                $assetsPath . '/' . $assetToCopy,
                $this->disk->get(__DIR__ . '/../../stubs/assets/' . $assetToCopy)
            );
        }


        $this->output->writeln('<info>✨ content directory as: ' . $contentPath . '</info>');

        $this->disk->makeDirectory(
            $contentPath
        );

        $this->disk->copyDirectory(
            __DIR__ . '/../../stubs/content',
            $contentPath
        );

        $this->output->writeln('<info>✨ config file as: ' . $ibisConfigPath . '</info>');

        $this->disk->put(
            $ibisConfigPath,
            $this->disk->get(__DIR__ . '/../../stubs/ibis.php')
        );


        $this->output->writeln('');
        $this->output->writeln('<info>✅ Done!</info>');

        return Command::SUCCESS;
    }
}
