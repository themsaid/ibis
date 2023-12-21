<?php

namespace Ibis\Commands;

use Ibis\Ibis;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPePub\Core\EPub;
use Symfony\Component\Console\Input\InputOption;

class BuildEpubCommand extends BaseBuildCommand
{
    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('epub')

            ->addOption(
                'content',
                'c',
                InputOption::VALUE_OPTIONAL,
                'The path of the content directory',
                ''
            )
            ->setDescription('Generate the book in EPUB format.');
    }

    /**
     * Execute the command.
     *
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

        $this->preExecute($input, $output);





        $this->config["breakLevel"] = 1;
        $result = $this->buildEpub(
            $this->buildHtml($this->contentDirectory, $this->config),
            $this->config,
            $this->currentPath
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
     * @param  string  $theme
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    protected function buildEpub(Collection $chapters, array $config, string $currentPath): bool
    {

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
        $book->addCSSFile("style.css", "css1", $this->getStyle($this->currentPath, "style"));
        $cssData = file_get_contents("https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.css");
        $book->addCSSFile("github.css", "css2", $cssData);
        //$book->addChapter("Table of Contents", "TOC.xhtml", null, false, EPub::EXTERNAL_REF_IGNORE);
        $cover = $content_start . "<h1>" . Ibis::title() . "</h1>\n";
        if (Ibis::author()) {
            $cover .= "<h2>By: " . Ibis::author() . "e</h2>\n";
        }
        $content_end = "</body></html>";
        $cover .= $content_end;
        $coverImage = "cover.jpg";
        if (array_key_exists("image", $config['cover'])) {
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

        $epubFilename = 'export/' . Ibis::outputFileName() . '.epub';
        $book->saveBook($epubFilename);


        $this->output->writeln('<fg=green>==></> EPUB file ' . $epubFilename . ' created');
        return true;
    }

    /**
     * @param $currentPath
     * @param $themeName
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getStyle(string $currentPath, string $themeName)
    {
        return $this->disk->get($currentPath . "/assets/$themeName.css");
    }


}
