<?php

namespace Ibis\Commands;

use Ibis\Ibis;
use Mpdf\Mpdf;
use SplFileInfo;
use Mpdf\Config\FontVariables;
use Mpdf\Config\ConfigVariables;
use League\CommonMark\Environment;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use League\CommonMark\Block\Element\FencedCode;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use League\CommonMark\Extension\Table\TableExtension;
use Symfony\Component\Console\Output\OutputInterface;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class BuildCommand extends Command
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
            ->setName('build')
            ->addArgument('theme', InputArgument::OPTIONAL, 'The name of the theme', 'light')
            ->setDescription('Generate the book.');
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
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->disk = new Filesystem();
        $this->output = $output;
        $this->themeName = $input->getArgument('theme');

        $currentPath = getcwd();
        $config = require $currentPath.'/ibis.php';

        $this->ensureExportDirectoryExists(
            $currentPath = getcwd()
        );

        $theme = $this->getTheme($currentPath, $this->themeName);

        $this->buildPdf(
            $this->buildHtml($currentPath.'/content', $config),
            $config,
            $currentPath,
            $theme
        );

        $this->output->writeln('');
        $this->output->writeln('<info>Book Built Successfully!</info>');

        return 0;
    }

    /**
     * @param  string  $currentPath
     */
    protected function ensureExportDirectoryExists(string $currentPath): void
    {
        $this->output->writeln('<fg=yellow>==></> Preparing Export Directory ...');

        if (! $this->disk->isDirectory($currentPath.'/export')) {
            $this->disk->makeDirectory(
                $currentPath.'/export',
                0755,
                true
            );
        }
    }

    /**
     * @param  string  $path
     * @param  array $config
     * @return string
     */
    protected function buildHtml(string $path, array $config)
    {
        $this->output->writeln('<fg=yellow>==></> Parsing Markdown ...');

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new TableExtension());

        $environment->addBlockRenderer(FencedCode::class, new FencedCodeRenderer([
            'html', 'php', 'js', 'bash', 'json'
        ]));

        if (is_callable($config['configure_commonmark'])) {
            call_user_func($config['configure_commonmark'], $environment);
        }

        $converter = new GithubFlavoredMarkdownConverter([], $environment);

        return collect($this->disk->files($path))
            ->map(function (SplFileInfo $file, $i) use ($converter) {
                if ($file->getExtension() != 'md') {
                    return '';
                }

                $markdown = $this->disk->get(
                    $file->getPathname()
                );

                return $this->prepareForPdf(
                    $converter->convertToHtml($markdown),
                    $i + 1
                );
            })
            ->implode(' ');
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
     * @param  string  $html
     * @param  array  $config
     * @param  string  $currentPath
     * @param  string  $theme
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    protected function buildPdf(string $html, array $config, string $currentPath, string $theme)
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => $config['document']['format'] ?? [210, 297],
            'margin_left' => $config['document']['margin_left'] ?? 27,
            'margin_right' => $config['document']['margin_right'] ?? 27,
            'margin_bottom' => $config['document']['margin_bottom'] ?? 14,
            'margin_top' => $config['document']['margin_top'] ?? 14,
            'fontDir' => array_merge($fontDirs, [getcwd().'/assets/fonts']),
            'fontdata' => $this->fonts($config, $fontData),
        ]);

        $pdf->SetTitle(Ibis::title());
        $pdf->SetAuthor(Ibis::author());
        $pdf->SetCreator(Ibis::author());

        $pdf->setAutoTopMargin = 'pad';

        $pdf->setAutoBottomMargin = 'pad';

        $tocLevels = $config['toc_levels'];

        $pdf->h2toc = $tocLevels;
        $pdf->h2bookmarks = $tocLevels;

        $pdf->SetMargins(400, 100, 12);

        if ($this->disk->isFile($currentPath.'/assets/cover.jpg')) {
            $this->output->writeln('<fg=yellow>==></> Adding Book Cover ...');

            $coverPosition = $config['cover']['position'] ?? 'position: absolute; left:0; right: 0; top: -.2; bottom: 0;';
            $coverDimensions = $config['cover']['dimensions'] ?? 'width: 210mm; height: 297mm; margin: 0;';

            $pdf->WriteHTML(
                <<<HTML
<div style="{$coverPosition}">
    <img src="assets/cover.jpg" style="{$coverDimensions}"/>
</div>
HTML
            );

            $pdf->AddPage();
        } elseif ($this->disk->isFile($currentPath.'/assets/cover.html')) {
            $this->output->writeln('<fg=yellow>==></> Adding Book Cover ...');

            $cover = $this->disk->get($currentPath.'/assets/cover.html');

            $pdf->WriteHTML($cover);

            $pdf->AddPage();
        } else {
            $this->output->writeln('<fg=red>==></> No assets/cover.jpg File Found. Skipping ...');
        }

        $pdf->SetHTMLFooter('<div id="footer" style="text-align: center">{PAGENO}</div>');

        $this->output->writeln('<fg=yellow>==></> Building PDF ...');

        $pdf->WriteHTML(
            $theme.$html
        );

        $this->output->writeln('<fg=yellow>==></> Writing PDF To Disk ...');

        $this->output->writeln('');
        $this->output->writeln('✨✨ '.$pdf->page.' PDF pages ✨✨');

        $pdf->Output(
            $currentPath.'/export/'.Ibis::outputFileName().'-'.$this->themeName.'.pdf'
        );
    }

    /**
     * @param $currentPath
     * @param $themeName
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getTheme($currentPath, $themeName)
    {
        return $this->disk->get($currentPath."/assets/theme-$themeName.html");
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
