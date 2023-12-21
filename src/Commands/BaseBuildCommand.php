<?php

namespace Ibis\Commands;

use SplFileInfo;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\TableExtension;

use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\MarkdownConverter;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;

class BaseBuildCommand extends Command
{
    protected OutputInterface $output;

    protected Filesystem $disk;

    protected string $contentDirectory;

    protected string $currentPath;

    protected array $config;


    protected function preExecute(InputInterface $input, OutputInterface $output)
    {
        $this->disk = new Filesystem();
        $this->output = $output;

        $this->currentPath = getcwd();

        $this->contentDirectory = $input->getOption('content');
        if ($this->contentDirectory === "") {
            $this->contentDirectory = getcwd() . DIRECTORY_SEPARATOR . "content";
        }
        if (!$this->disk->isDirectory($this->contentDirectory)) {
            $this->output->writeln('<error>Error, check if ' . $this->contentDirectory . ' exists.</error>');
            exit;
        }
        $this->output->writeln('<info>Loading content from: ' . $this->contentDirectory . '</info>');

        $configIbisFile = $this->currentPath . '/ibis.php';
        if (!$this->disk->isFile($configIbisFile)) {
            $this->output->writeln('<error>Error, check if ' . $configIbisFile . ' exists.</error>');
            exit;
        }

        $this->config = require $configIbisFile;

    }


    protected function buildHtml(string $path, array $config): Collection
    {
        $this->output->writeln('<fg=yellow>==></> Parsing Markdown ...');


        $environment = new Environment([]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new FrontMatterExtension());

        $environment->addRenderer(FencedCode::class, new FencedCodeRenderer([
            'html', 'php', 'js', 'bash', 'json'
        ]));
        $environment->addRenderer(IndentedCode::class, new IndentedCodeRenderer([
            'html', 'php', 'js', 'bash', 'json'
        ]));

        if (is_callable($config['configure_commonmark'])) {
            call_user_func($config['configure_commonmark'], $environment);
        }

        $converter = new MarkdownConverter($environment);

        return collect($this->disk->allFiles($path))
            ->map(function (SplFileInfo $file, $i) use ($converter, $config) {

                $chapter = collect([]);
                if ($file->getExtension() != 'md') {
                    $chapter["mdfile"] = $file->getFilename();
                    $chapter["frontmatter"] = false;
                    $chapter["html"] = "";
                    return $chapter;
                }

                $markdown = $this->disk->get(
                    $file->getPathname()
                );


                //$chapter = collect([]);
                $convertedMarkdown = $converter->convert($markdown);
                $chapter["mdfile"] = $file->getFilename();
                $chapter["frontmatter"] = false;
                if ($convertedMarkdown instanceof RenderedContentWithFrontMatter) {
                    $chapter["frontmatter"] = $convertedMarkdown->getFrontMatter();
                }
                $chapter["html"] = $this->prepareHtmlForEbook(
                    $convertedMarkdown->getContent(),
                    $i + 1,
                    Arr::get($config, "breakLevel", 2)
                );


                return $chapter;
            });
        //->implode(' ');
    }


    /**
     * @param $file
     * @return string|string[]
     */
    protected function prepareHtmlForEbook(string $html, $file, $breakLevel = 2): string
    {
        $commands = [
            '[break]' => '<div style="page-break-after: always;"></div>'
        ];

        if ($file > 1) {
            if ($breakLevel >= 1) {
                $html = str_replace('<h1>', '[break]<h1>', $html);
            }
        }
        if ($breakLevel >= 2) {
            $html = str_replace('<h2>', '[break]<h2>', $html);
        }
        $html = str_replace("<blockquote>\n<p>{notice}", "<blockquote class='notice'><p><strong>Notice:</strong>", $html);
        $html = str_replace("<blockquote>\n<p>{warning}", "<blockquote class='warning'><p><strong>Warning:</strong>", $html);
        $html = str_replace("<blockquote>\n<p>{quote}", "<blockquote class='quote'><p>", $html);

        return str_replace(array_keys($commands), array_values($commands), $html);
    }



    protected function ensureExportDirectoryExists(string $currentPath): void
    {
        $this->output->writeln('<fg=yellow>==></> Preparing Export Directory ...');

        if (!$this->disk->isDirectory($currentPath . '/export')) {
            $this->disk->makeDirectory(
                $currentPath . '/export',
                0755,
                true
            );
        }
    }

}
