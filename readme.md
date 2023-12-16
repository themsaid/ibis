<p align="center">
    <img src="https://raw.githubusercontent.com/hi-folks/ibis-next/main/art/ibis-next-cover.png" alt="Ibis Next cover" width="480">

Artwork generated with Cover Wizard.
</p>

---

This PHP tool helps you write eBooks in markdown. Run `ibis-next build` and an eBook will be generated with:

1. A cover photo.
2. Clickable auto-generated table of contents.
3. Code syntax highlighting.
4. Available in 2 themes. Light and dark.

The Ibis project was created by Mohamed Said. The sources of the Ibis project are https://github.com/themsaid/ibis.
We want to say thank you to Mohamed for creating this tool.
Ibis was used to create [Laravel Queues in Action](https://learn-laravel-queues.com), an eBook Mohamed published in August 2020. [Click here](https://learn-laravel-queues.com/laravel-queues-in-action.zip) for the sample.

Why we forked the repository: to speed up the process of supporting PHP 8.2, PHP 8.3, Laravel 10, Symfony 7, Commonmark 2, and other dependencies upgrades.
Then we want to try to support also EPUB format, not only PDF.

## Installation

Make sure you have PHP8.1 or above installed on your system and that your gd extension is enabled in your php.ini file.

First, install the composer package globally:

```
composer global require hi-folks/ibis-next
```

Then, run this command inside an **empty directory**:

```
ibis-next init
```

This will create the following files and directories:

- /assets
- /assets/fonts
- /assets/cover.jpg
- /assets/theme-light.html
- /assets/theme-dark.html
- /content
- /ibis.php

You may configure your book by editing the `ibis.php` configuration file.

## Writing Your eBook

The `init` command will create sample .md files inside the `content` folder. You can explore those files to see how you can write your book.
This sample content is taken from [Laravel Queues in Action](https://learn-laravel-queues.com) by Mohamed Said.

Inside the `content` directory, you can write multiple `.md` files. Ibis uses the headings to divide the book into parts and chapters:

```
# Part 1

<h1> tags define the start of a part. A separate PDF page will be generated to print the part title and any content below.

## Chapter 1

<h2> tags define the start of a chapter. A chapter starts on a new page always.

### Starting with Ibis

<h3> tags define different titles inside a chapter.
```

### Adding different quotes

Three different types of quotes can be added: `quote`, `warning`, and `notice`.

```md
>{quote} This is a quote.

>{warning} This is a warning.

>{notice} This is a notice.
```

### Using images

Images can be stored in the content folder and then brought in like this:

```
![Screenshot 1](content/screenshot-1.png)
```

### Adding a cover image
To use a cover image, add a `cover.jpg` in the `assets/` directory (or a `cover.html` file if you'd prefer a HTML-based cover page). If you don't want a cover image, delete these files.
If your cover is in a PNG format you can store the file in the `assets/` directory and then in the `ibis.php` file you can adjust the `cover` configuration where you can set the cover file name, for example:

```
    'cover' => [
        'position' => 'position: absolute; left:0; right: 0; top: -.2; bottom: 0;',
        'dimensions' => 'width: 210mm; height: 297mm; margin: 0;',
        'image' => 'cover.png',
    ],
```

## Using Fonts

Edit your `/ibis.php` configuration files to define the font files to be loaded from the `/assets/fonts` directory. After that you may use the defined fonts in your themes (`/assets/theme-light.html` & `/assets/theme-dark.html`).

## Generating PDF eBook

```
ibis-next build
```

Ibis will parse the files in alphabetical order and store the PDF file in the `export` directory.

By default, for generating the PDF file, the light theme is used. To generate a PDF using the dark theme:

```
ibis-next build dark
```

## Generating A Sample

```
ibis-next sample

ibis-next sample dark
```

This command will use the generated files from the `ibis-next build` command to generate samples from your PDF eBook. You can configure which pages to include in the sample by updating the `/ibis.php` file.

## Development

This project uses Laravel Pint to fix the code style according to `per` preset.
The `pint.json` file defines the Laravel Pint configuration.
Laravel Pint is built on top of the great tool PHP-CS-Fixer.

To review code out of style, you can run the fix command as a dry run.  Run the composer script like this:

`composer run csfix-review`

To fix the source code, run the following composer script:

`composer run csfix`

## Credits

- [Mohamed Said](https://github.com/themsaid) the author of the original Ibis project
- [Roberto Butti](https://github.com/roberto-butti) the author of the updates and the fork for Ibis Next project
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
