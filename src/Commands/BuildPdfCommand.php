<?php

namespace Ibis\Commands;

use Mpdf\Mpdf;

use Mpdf\Config\FontVariables;
use Mpdf\Config\ConfigVariables;


use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class BuildPdfCommand extends BaseBuildCommand
{
    /**
     * @var string|string[]|null
     */
    public $themeName;






    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('pdf')
            ->setAliases(["build"])
            ->addArgument('theme', InputArgument::OPTIONAL, 'The name of the theme', 'light')
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
            ->setDescription('Generate the book in PDF format.');
    }

    /**
     * Execute the command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->output->writeln('<info>✨ Building PDF file ✨</info>');


        if (!$this->preExecute($input, $output)) {
            return Command::INVALID;
        }

        $this->themeName = $input->getArgument('theme');

        $this->ensureExportDirectoryExists($this->config->workingPath);

        $theme = $this->getTheme($this->config->workingPath, $this->themeName);

        $this->buildPdf(
            $this->buildHtml($this->config->contentPath, $this->config->config),
            $this->config->config,
            $this->config->workingPath,
            $theme
        );

        $this->output->writeln('');
        $this->output->writeln('<info>Book Built Successfully!</info>');

        return Command::SUCCESS;
    }






    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    protected function buildPdf(Collection $chapters, array $config, string $currentPath, string $theme): bool
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
            'fontDir' => array_merge($fontDirs, [getcwd() . '/assets/fonts']),
            'fontdata' => $this->fonts($config, $fontData),
        ]);

        $pdf->SetTitle($this->config->title());
        $pdf->SetAuthor($this->config->author());
        $pdf->SetCreator($this->config->author());

        $pdf->setAutoTopMargin = 'pad';

        $pdf->setAutoBottomMargin = 'pad';

        $tocLevels = $config['toc_levels'];

        $pdf->h2toc = $tocLevels;
        $pdf->h2bookmarks = $tocLevels;

        $pdf->SetMargins(400, 100, 12);
        $coverImage = "cover.jpg";
        if (array_key_exists("image", $config['cover'])) {
            $coverImage = $config['cover']['image'];
        }

        if ($this->disk->isFile($currentPath . '/assets/' . $coverImage)) {
            $this->output->writeln('<fg=yellow>==></> Adding Book Cover ...');

            $coverPosition = $config['cover']['position'] ?? 'position: absolute; left:0; right: 0; top: -.2; bottom: 0;';
            $coverDimensions = $config['cover']['dimensions'] ?? 'width: 210mm; height: 297mm; margin: 0;';

            $pdf->WriteHTML(
                <<<HTML
<div style="{$coverPosition}">
    <img src="{$currentPath}/assets/{$coverImage}" style="{$coverDimensions}"/>
</div>
HTML
            );

            $pdf->AddPage();
        } elseif ($this->disk->isFile($currentPath . '/assets/cover.html')) {
            $this->output->writeln('<fg=yellow>==></> Adding Book Cover ...');

            $cover = $this->disk->get($currentPath . '/assets/cover.html');

            $pdf->WriteHTML($cover);

            $pdf->AddPage();
        } else {
            $this->output->writeln('<fg=red>==></> No assets/' . $coverImage . ' File Found. Skipping ...');
        }

        $pdf->SetHTMLFooter('<div id="footer" style="text-align: center">{PAGENO}</div>');

        $this->output->writeln('<fg=yellow>==></> Building PDF ...');

        $pdf->WriteHTML(
            $theme
        );
        //dd($chapters);
        foreach ($chapters as $chapter) {
            //if ( is_string($chapter) ) { dd($key, $chapter);}
            $this->output->writeln('<fg=yellow>==></> ❇️ ' . $chapter["mdfile"] . ' ...');
            if (array_key_exists('header', $config)) {
                $pdf->SetHTMLHeader(
                    '
                    <div style="' . $config['header'] . '">
                        ' . Arr::get($chapter, "frontmatter.title", $config["title"]) . '
                    </div>'
                );
            }

            $pdf->WriteHTML(
                $chapter["html"]
            );
        }

        $this->output->writeln('<fg=yellow>==></> Writing PDF To Disk ...');

        $this->output->writeln('');
        $this->output->writeln('✨✨ ' . $pdf->page . ' PDF pages ✨✨');

        $pdfFilename = $currentPath . '/export/' . $this->config->outputFileName() . '-' . $this->themeName . '.pdf';




        $pdf->Output(
            $pdfFilename
        );

        $this->output->writeln('<fg=green>==></> PDF file ' . $pdfFilename . ' created');
        return true;
    }

    /**
     * @param $currentPath
     * @param $themeName
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getTheme(string $currentPath, $themeName)
    {
        return $this->disk->get($currentPath . sprintf('/assets/theme-%s.html', $themeName));
    }

    /**
     * @param $fontData
     * @return array
     */
    protected function fonts(array $config, $fontData): float|int|array
    {
        return $fontData + collect($config['fonts'])->mapWithKeys(static fn($file, $name): array => [
            $name => [
                'R' => $file
            ]
        ])->toArray();
    }
}
