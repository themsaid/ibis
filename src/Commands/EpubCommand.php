<?php

namespace Ibis\Commands;

use Ibis\Ibis;
use SplFileInfo;
use Mpdf\Config\FontVariables;
use Mpdf\Config\ConfigVariables;
use League\CommonMark\Environment\Environment;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Command\Command;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use League\CommonMark\Extension\Table\TableExtension;
use Symfony\Component\Console\Output\OutputInterface;
use League\CommonMark\MarkdownConverter;
use PHPePub\Core\EPub;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;

class EpubCommand extends Command
{
    /**
     * @var string|string[]|null
     */
    public $themeName;

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
            ->setName('epub')
            ->addArgument('theme', InputArgument::OPTIONAL, 'The name of the theme', 'light')
            ->setDescription('Generate the book in Epub format.');
    }

    /**
     * Execute the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->output->writeln('✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨');
        $this->output->writeln('<info>✨ EPUB generation is a Working in Progress!! ✨</info>');
        $this->output->writeln('<info>✨                Stay tuned!!                ✨</info>');
        $this->output->writeln('✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨✨');
        //return 0;
        $this->disk = new Filesystem();
        $this->output = $output;
        $this->themeName = $input->getArgument('theme');

        $currentPath = getcwd();
        $configIbisFile = $currentPath . '/ibis.php';
        if (!$this->disk->isFile($configIbisFile)) {
            $this->output->writeln('<error>Error, check if ' . $configIbisFile . ' exists.</error>');
            exit -1;
        }

        $config = require $configIbisFile;
        $this->ensureExportDirectoryExists(
            $currentPath = getcwd()
        );

        $theme = $this->getTheme($currentPath, $this->themeName);

        $result = $this->buildEpub(
            $this->buildHtml($currentPath . '/content', $config),
            $config,
            $currentPath,
            $theme
        );

        $this->output->writeln('');
        if ($result) {

            $this->output->writeln('<info>Book Built Successfully!</info>');
        } else {
            $this->output->writeln('<error>Book Built Failed!</error>');
        }


        return 0;
    }

    /**
     * @param  string  $currentPath
     */
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

    /**
     * @param  string  $path
     * @param  array $config
     * @return Collection
     */
    protected function buildHtml(string $path, array $config)
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

        return collect($this->disk->files($path))
            ->map(function (SplFileInfo $file, $i) use ($converter) {
                if ($file->getExtension() != 'md') {
                    return '';
                }

                $markdown = $this->disk->get(
                    $file->getPathname()
                );


                $chapter = collect([]);
                $convertedMarkdown = $converter->convert($markdown);
                $chapter["mdfile"] = $file->getFilename();
                $chapter["frontmatter"] = false;
                if ($convertedMarkdown instanceof RenderedContentWithFrontMatter) {
                    $chapter["frontmatter"] = $convertedMarkdown->getFrontMatter();
                }
                $chapter["html"] = $this->prepareForPdf(
                    $convertedMarkdown->getContent(),
                    $i + 1
                );


                return $chapter;
            });
        //->implode(' ');
    }

    /**
     * @param  string  $html
     * @param $file
     * @return string|string[]
     */
    private function prepareForPdf(string $html, $file)
    {
        $commands = [
            '[break]' => '<div style="page-break-after: always;"></div>'
        ];

        if ($file > 1) {
            $html = str_replace('<h1>', '[break]<h1>', $html);
        }

        $html = str_replace('<h2>', '[break]<h2>', $html);
        $html = str_replace("<blockquote>\n<p>{notice}", "<blockquote class='notice'><p><strong>Notice:</strong>", $html);
        $html = str_replace("<blockquote>\n<p>{warning}", "<blockquote class='warning'><p><strong>Warning:</strong>", $html);
        $html = str_replace("<blockquote>\n<p>{quote}", "<blockquote class='quote'><p>", $html);

        $html = str_replace(array_keys($commands), array_values($commands), $html);

        return $html;
    }

    /**
     * @param  Collection  $chapters
     * @param  array  $config
     * @param  string  $currentPath
     * @param  string  $theme
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    protected function buildEpub(Collection $chapters, array $config, string $currentPath, string $theme)
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];


        $content_start =
            "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
            . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n"
            . "    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"
            . "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n"
            . "<head>"
            . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n"
            . "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" />\n"
            . "<title>Test Book</title>\n"
            . "</head>\n"
            . "<body>\n";
        $content_start =
        "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
        . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:epub=\"http://www.idpf.org/2007/ops\">\n"
        . "<head>"
        . "<meta http-equiv=\"Default-Style\" content=\"text/html; charset=utf-8\" />\n"
        . "<link rel=\"stylesheet\" type=\"text/css\" href=\"github.css\" />\n"
        . "<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" />\n"
        . "<title>" . Ibis::title() . "</title>\n"
        . "</head>\n"
        . "<body>\n";
        $book = new EPub();
        $book->setTitle(Ibis::title());
        $book->setAuthor(Ibis::author(), Ibis::author());
        $book->setIdentifier(Ibis::title() . "&amp;stamp=" . time(), EPub::IDENTIFIER_URI);
        $book->setLanguage("en");
        //$cssData = "body {\n  margin-left: .5em;\n  margin-right: .5em;\n  text-align: justify;\n}\n\np {\n  font-family: serif;\n  font-size: 10pt;\n  text-align: justify;\n  text-indent: 1em;\n  margin-top: 0px;\n  margin-bottom: 1ex;\n}\n\nh1, h2 {\n  font-family: sans-serif;\n  font-style: italic;\n  text-align: center;\n  background-color: #6b879c;\n  color: yellow;\n  width: 100%;\n}\n\nh1 {\n    margin-bottom: 2px;\n}\n\nh2 {\n    margin-top: -2px;\n    margin-bottom: 2px;\n}\n";
        //$book->addCSSFile("styles.css", "css1", $cssData);
        $cssData = file_get_contents("assets/style.css");
        //dd($cssData);
        $book->addCSSFile("style.css", "css1", $cssData);
        $cssData = file_get_contents("https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.css");
        //dd($cssData);
        $book->addCSSFile("github.css", "css2", $cssData);
        //$book->addChapter("Table of Contents", "TOC.xhtml", null, false, EPub::EXTERNAL_REF_IGNORE);
        $cover = $content_start . "<h1>" . Ibis::title() . "</h1>\n";
        if (Ibis::author()) {
            $cover .= "<h2>By: " . Ibis::author() . "e</h2>\n";
        }
        $content_end = "</body></html>";
        $cover .= $content_end;
        $coverImage = "cover.jpg";
        if (key_exists("image", $config['cover'])) {
            $coverImage = $config['cover']['image'];
        }
        if ($this->disk->isFile($currentPath . '/assets/' . $coverImage)) {
            $this->output->writeln('<fg=yellow>==></> Adding Book Cover ...');

            $coverPosition = $config['cover']['position'] ?? 'position: absolute; left:0; right: 0; top: -.2; bottom: 0;';
            $coverDimensions = $config['cover']['dimensions'] ?? 'width: 210mm; height: 297mm; margin: 0;';
            $book->setCoverImage("Cover.jpg", file_get_contents("assets/{$coverImage}"), "image/jpeg");
        }

        $book->addChapter("Notices", "Cover.html", $cover);
        $book->buildTOC();
        foreach ($chapters as $key => $chapter) {
            $this->output->writeln('<fg=yellow>==></> ❇️ ' . $chapter["mdfile"] . ' ...');
            $book->addChapter(
                Arr::get($chapter, "frontmatter.title", "Chapter " . $key),
                "Chapter" . $key . " .html",
                $content_start . $chapter["html"] . $content_end
            );
            //file_put_contents('export/' . "Chapter" . $key . " .html", $content_start . $chapter["html"] . $content_end);
        }
        //$book->buildTOC();

        $book->finalize();

        $epubFilename = 'export/' . Ibis::outputFileName() . '-' . $this->themeName . '.epub';
        $zipData = $book->saveBook($epubFilename);


        $this->output->writeln('<fg=green>==></> EPUB file ' . $epubFilename . ' created');
        return true;

        $this->output->writeln('<fg=yellow>==></> Writing EPUB To Disk ...');

        $this->output->writeln('');
    }

    /**
     * @param $currentPath
     * @param $themeName
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getTheme($currentPath, $themeName)
    {
        return $this->disk->get($currentPath . "/assets/theme-$themeName.html");
    }

    /**
     * @param $fontData
     * @return array
     */
    protected function fonts($config, $fontData)
    {
        return $fontData + collect($config['fonts'])->mapWithKeys(function ($file, $name) {
            return [
                $name => [
                    'R' => $file
                ]
            ];
        })->toArray();
    }
}
