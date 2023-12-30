<?php

namespace Ibis\Commands;

use Ibis\Ibis;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SampleCommand extends Command
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
            ->setDescription('Generate a sample.');
    }

    /**
     * Execute the command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $currentPath = getcwd();

        $config = require $currentPath . '/ibis.php';

        $mpdf = new \Mpdf\Mpdf();

        $fileName = Ibis::outputFileName() . '-' . $input->getArgument('theme');

        $mpdf->setSourceFile($currentPath . '/export/' . $fileName . '.pdf');

        foreach ($config['sample'] as $range) {
            foreach (range($range[0], $range[1]) as $page) {
                $mpdf->useTemplate(
                    $mpdf->importPage($page)
                );
                $mpdf->AddPage();
            }
        }

        $mpdf->WriteHTML('<p style="text-align: center; font-size: 16px; line-height: 40px;">' . $config['sample_notice'] . '</p>');

        $mpdf->Output(
            $currentPath . '/export/sample-.' . $fileName . '.pdf'
        );

        return 0;
    }
}
