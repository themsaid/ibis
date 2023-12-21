<?php

namespace Ibis\Commands;

use Ibis\Ibis;
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

class BuildCommand extends BaseBuildCommand
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
            ->setName('build')
            ->addArgument('theme', InputArgument::OPTIONAL, 'The name of the theme', 'light')
            ->addOption(
                'content',
                'c',
                InputOption::VALUE_OPTIONAL,
                'The path of the content directory',
                ''
            )
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
    public function execute(InputInterface $input, OutputInterface $output): int
    {

        $this->preExecute($input, $output);
        $this->themeName = $input->getArgument('theme');

        $this->ensureExportDirectoryExists($this->currentPath);

        $theme = $this->getTheme($this->currentPath, $this->themeName);

        $this->buildPdf(
            $this->buildHtml($this->contentDirectory, $this->config),
            $this->config,
            $this->currentPath,
            $theme
        );

        $this->output->writeln('');
        $this->output->writeln('<info>Book Built Successfully!</info>');

        return 0;
    }






    /**
     * @param  Collection  $chapters
     * @param  array  $config
     * @param  string  $currentPath
     * @param  string  $theme
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    protected function buildPdf(Collection $chapters, array $config, string $currentPath, string $theme)
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

        $pdf->SetTitle(Ibis::title());
        $pdf->SetAuthor(Ibis::author());
        $pdf->SetCreator(Ibis::author());

        $pdf->setAutoTopMargin = 'pad';

        $pdf->setAutoBottomMargin = 'pad';

        $tocLevels = $config['toc_levels'];

        $pdf->h2toc = $tocLevels;
        $pdf->h2bookmarks = $tocLevels;

        $pdf->SetMargins(400, 100, 12);
        $coverImage = "cover.jpg";
        if (key_exists("image", $config['cover'])) {
            $coverImage = $config['cover']['image'];
        }
        if ($this->disk->isFile($currentPath . '/assets/' . $coverImage)) {
            $this->output->writeln('<fg=yellow>==></> Adding Book Cover ...');

            $coverPosition = $config['cover']['position'] ?? 'position: absolute; left:0; right: 0; top: -.2; bottom: 0;';
            $coverDimensions = $config['cover']['dimensions'] ?? 'width: 210mm; height: 297mm; margin: 0;';

            $pdf->WriteHTML(
                <<<HTML
<div style="{$coverPosition}">
    <img src="assets/{$coverImage}" style="{$coverDimensions}"/>
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
        foreach ($chapters as $key => $chapter) {
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

        $pdf->Output(
            $currentPath . '/export/' . Ibis::outputFileName() . '-' . $this->themeName . '.pdf'
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
