<?php

namespace Ibis\Commands;

use Ibis\Config;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

class BuildHtmlCommand extends BaseBuildCommand
{
    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('html')

            ->addOption(
                'content',
                'c',
                InputOption::VALUE_OPTIONAL,
                'The path of the content directory',
                ''
            )
            ->addOption(
                'workingdir',
                'd',
                InputOption::VALUE_OPTIONAL,
                'The path of the working directory where `ibis.php` and `assets` directory are located',
                ''
            )
            ->setDescription('Generate the book in HTML format.');
    }

    /**
     * Execute the command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->output->writeln('<info>✨ Building HTML file ✨</info>');

        if (!$this->preExecute($input, $output)) {
            return Command::INVALID;
        }


        $this->ensureExportDirectoryExists($this->config->workingPath);


        $this->config->config["breakLevel"] = 1;
        $result = $this->buildHtmlFile(
            $this->buildHtml($this->config->contentPath, $this->config->config),
            $this->config->config,
            $this->config->workingPath
        );

        $this->output->writeln('');
        if ($result) {

            $this->output->writeln('<info>Book Built Successfully!</info>');
        } else {
            $this->output->writeln('<error>Book Built Failed!</error>');
        }


        return Command::SUCCESS;
    }


    /**
     * @throws FileNotFoundException
     */
    protected function buildHtmlFile(Collection $chapters, array $config, string $currentPath): bool
    {
        $template = $this->disk->get($currentPath . '/assets/theme-html.html');
        $outputHtml = str_replace("{{\$title}}", $this->config->title(), $template);
        $outputHtml = str_replace("{{\$author}}", $this->config->author(), $outputHtml);




        $html = "";
        foreach ($chapters as $chapter) {
            $this->output->writeln('<fg=yellow>==></> ❇️ ' . $chapter["mdfile"] . ' ...');
            $html .= $chapter["html"];
        }

        $outputHtml = str_replace("{{\$body}}", $html, $outputHtml);



        $htmlFilename = Config::buildPath(
            $currentPath,
            "export",
            $this->config->outputFileName() . '.html'
        );
        file_put_contents($htmlFilename, $outputHtml);


        $this->output->writeln('<fg=green>==></> HTML file ' . $htmlFilename . ' created');
        return true;
    }


}
