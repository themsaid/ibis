<?php

namespace Ibis\Commands;

use Ibis\Config;
use Ibis\Markdown\Extensions\Aside;
use Ibis\Markdown\Extensions\AsideExtension;
use Ibis\Markdown\Extensions\AsideRenderer;
use League\CommonMark\Extension\Attributes\AttributesExtension;
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

    protected Config $config;


    protected function preExecute(InputInterface $input, OutputInterface $output): bool
    {
        $this->disk = new Filesystem();
        $this->output = $output;

        $this->config = Config::load($input->getOption('workingdir'));
        $this->output->writeln('<info>Loading config/assets from: ' . $this->config->workingPath . '</info>');
        $this->output->writeln('<info>Loading config file from: ' . $this->config->ibisConfigPath . '</info>');
        if ($this->config->setContentPath($input->getOption('content')) === false) {
            $this->output->writeln('<error>Error, check if ' . $this->config->contentPath . ' exists.</error>');
            return false;
        }

        $this->output->writeln('<info>Loading content from: ' . $this->config->contentPath . '</info>');

        if (!$this->disk->isFile($this->config->ibisConfigPath)) {
            $this->output->writeln('<error>Error, check if ' . $this->config->ibisConfigPath . ' exists.</error>');
            return false;
        }

        return true;
    }


    protected function buildHtml(string $path, array $config): Collection
    {
        $this->output->writeln('<fg=yellow>==></> Parsing Markdown ...');


        $environment = new Environment([]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new FrontMatterExtension());
        $environment->addExtension(new AsideExtension());
        $environment->addExtension(new AttributesExtension());


        $environment->addRenderer(FencedCode::class, new FencedCodeRenderer([
            'html', 'php', 'js', 'bash', 'json',
        ]));
        $environment->addRenderer(IndentedCode::class, new IndentedCodeRenderer([
            'html', 'php', 'js', 'bash', 'json',
        ]));
        $environment->addRenderer(Aside::class, new AsideRenderer());

        if (is_callable($config['configure_commonmark'])) {
            call_user_func($config['configure_commonmark'], $environment);
        }

        $converter = new MarkdownConverter($environment);

        $fileList = [];
        if (array_key_exists("md_file_list", $config)) {
            foreach ($config["md_file_list"] as $filename) {
                $filefound = new SplFileInfo($path . '/' . $filename);
                $fileList[] = $filefound;
            }
        } else {
            $fileList = $this->disk->allFiles($path);
        }

        return collect($fileList)
            ->map(function (SplFileInfo $file, $i) use ($converter, $config) {

                $chapter = collect([]);
                if ($file->getExtension() !== 'md') {
                    $chapter->put("mdfile", $file->getFilename());
                    $chapter->put("frontmatter", false);
                    $chapter->put("html", "");
                    return $chapter;
                }

                $markdown = $this->disk->get(
                    $file->getPathname(),
                );

                $convertedMarkdown = $converter->convert($markdown);
                $chapter->put("mdfile", $file->getFilename());
                $chapter->put("frontmatter", false);
                if ($convertedMarkdown instanceof RenderedContentWithFrontMatter) {
                    $chapter->put("frontmatter", $convertedMarkdown->getFrontMatter());
                }

                $chapter->put("html", $this->prepareHtmlForEbook(
                    $convertedMarkdown->getContent(),
                    $i + 1,
                    Arr::get($config, "breakLevel", 2),
                ));


                return $chapter;
            });
        //->implode(' ');
    }


    /**
     * @param $file
     * @param int $breakLevel
     */
    protected function prepareHtmlForEbook(string $html, $file, $breakLevel = 2): string
    {
        $commands = [
            '[break]' => '<div style="page-break-after: always;"></div>',
        ];

        if ($file > 1 && $breakLevel >= 1) {
            $html = str_replace('<h1>', '[break]<h1>', $html);
        }

        if ($breakLevel >= 2) {
            $html = str_replace('<h2>', '[break]<h2>', $html);
        }

        $html = str_replace("<blockquote>\n<p>{notice}", "<blockquote class='notice'><p><strong>Notice:</strong>", $html);
        $html = str_replace("<blockquote>\n<p>{warning}", "<blockquote class='warning'><p><strong>Warning:</strong>", $html);
        $html = str_replace("<blockquote>\n<p>{quote}", "<blockquote class='quote'><p>", $html);
        $html = str_replace("<blockquote>\n<p>[!NOTE]", "<blockquote class='notice'><p><strong>Note:</strong>", $html);
        $html = str_replace("<blockquote>\n<p>[!WARNING]", "<blockquote class='warning'><p><strong>Warning:</strong>", $html);

        return str_replace(array_keys($commands), array_values($commands), $html);
    }



    protected function ensureExportDirectoryExists(string $currentPath): void
    {
        $this->output->writeln('<fg=yellow>==></> Preparing Export Directory ...');

        if (!$this->disk->isDirectory(
            Config::buildPath($currentPath, "export"),
        )) {
            $this->disk->makeDirectory(
                Config::buildPath(
                    $currentPath,
                    "export",
                ),
                0755,
                true,
            );
        }
    }

}
