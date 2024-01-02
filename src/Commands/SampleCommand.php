<?php

namespace Ibis\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SampleCommand extends BaseBuildCommand
{
    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('sample')
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
            ->setDescription('Generate a sample from the PDF.');
    }

    /**
     * Execute the command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        if (!$this->preExecute($input, $output)) {
            return Command::INVALID;
        }

        $themeName = $input->getArgument('theme');

        $pdfFilename = $this->config->workingPath . '/export/' . $this->config->outputFileName() . '-' . $themeName . '.pdf';


        if (!$this->disk->isFile($pdfFilename)) {
            $this->output->writeln('<fg=red> ⚠️  File ' . $pdfFilename . ' not exists (i need it for creating the sample)</>');
            $this->output->writeln('<fg=yellow> Suggestion : try to execute `ibis-next pdf` before generating a sample</>');
            return Command::FAILURE;
        }

        $mpdf = new \Mpdf\Mpdf();



        $mpdf->setSourceFile($pdfFilename);

        foreach ($this->config->config['sample'] as $range) {
            foreach (range($range[0], $range[1]) as $page) {
                $mpdf->useTemplate(
                    $mpdf->importPage($page)
                );
                $mpdf->AddPage();
            }
        }

        $mpdf->WriteHTML('<p style="text-align: center; font-size: 16px; line-height: 40px;">' . $this->config->config['sample_notice'] . '</p>');
        $sampleFileName = $this->config->workingPath . '/export/sample-' . $this->config->outputFileName() . '-' . $themeName . '.pdf';
        $this->output->writeln('<fg=yellow>==></> Writing Sample PDF To Disk ...');
        $mpdf->Output(
            $sampleFileName
        );
        $this->output->writeln('<fg=green> ✅ File ' . $sampleFileName . ' created</>');

        $this->output->writeln('');
        $this->output->writeln('✨✨ ' . $mpdf->page . ' PDF pages ✨✨');


        return 0;
    }
}
