<p align="center">
    <img src="https://raw.githubusercontent.com/hi-folks/ibis-next/main/art/ibis-next-cover.png" alt="Ibis Next cover" width="480">
</p>

---

This PHP tool helps you write eBooks in markdown. You can automatically generate a PDF or an EPUB file.
Run `ibis-next build` or `ibis-next epub` and an eBook will be generated with:

1. A cover photo.
2. Clickable auto-generated table of contents.
3. Code syntax highlighting.
4. Available in 2 themes. Light and dark (the theme features is supported only for PDF).

The Ibis project was created by Mohamed Said. The sources of the Ibis project are https://github.com/themsaid/ibis.
We want to say thank you to Mohamed for creating this tool.
Ibis was used to create [Laravel Queues in Action](https://learn-laravel-queues.com), an eBook Mohamed published in August 2020. [Click here](https://learn-laravel-queues.com/laravel-queues-in-action.zip) for the sample.

Why we forked the repository: to speed up the process of supporting PHP 8.2, **PHP 8.3**, **Laravel 10**, **Symfony 7**, **Commonmark 2**, and other dependencies upgrades.
With *Ibis Next* we added also the **support for generating the EPUB format**. So with *Ibis Next* you can create your markdown files and then export them into PDF and EPUB for better compatibility with your devices and software.

## Installation

Make sure you have PHP8.1 or above installed on your system and that your gd extension is enabled in your php.ini file.
You can decide if you want to install it locally for a specific project (eBook in this case) or to install it globally, making it available across all projects (eBooks).


### Installing ibis-next locally

If you want to start quickly to build your eBook you can:
- create a new empty directory via the `mkdir` command, and then jump into the new directory via the `cd` command:

~~~shell
mkdir my-first-ebook
cd my-first-ebook
~~~

Now you are in the new empty directory, you can install Ibis Next:

~~~shell
composer require hi-folks/ibis-next
~~~

Once the tool is installed, you will find the `vendor/` directory where you can find your new tool (`vendor/bin/ibis-next`).

Now you can initialize properly the directory via the `init` command for automatically creating the configuration file, the assets folder, and the content folder (for creating your Markdown files).
To launch the `init`` command:

~~~shell
./vendor/bin/ibis-next init
~~~

### Installing ibis-next globally


Instead, if you prefer to install the composer package globally you can add the `global` option while you are running the `composer require` command:

~~~shell
composer global require hi-folks/ibis-next
~~~

Then, run this command inside an **empty directory**:

~~~shell
ibis-next init
~~~

This will create the following files and directories:

- /assets
- /assets/fonts
- /assets/cover.jpg
- /assets/theme-light.html
- /assets/theme-dark.html
- /assets/style.css
- /content
- /ibis.php

You may configure your book by editing the `ibis.php` configuration file.

## Writing Your eBook

The `init` command will create sample .md files inside the `content` folder. You can explore those files to see how you can write your book.
This sample content is taken from [Laravel Queues in Action](https://learn-laravel-queues.com) by Mohamed Said.

Inside the `content` directory, you can write multiple `.md` files. Ibis uses the headings to divide the book into parts and chapters:

~~~markdown
# Part 1

<h1> tags define the start of a part. A separate PDF page will be generated to print the part title and any content below.

## Chapter 1

<h2> tags define the start of a chapter. A chapter starts on a new page always.

### Starting with Ibis

<h3> tags define different titles inside a chapter.
~~~

### Adding different quotes

Three different types of quotes can be added: `quote`, `warning`, and `notice`.

~~~markdown
>{quote} This is a quote.

>{warning} This is a warning.

>{notice} This is a notice.
~~~

### Using images

Images can be stored in the content folder and then brought in like this:

~~~markdown
![Screenshot 1](content/screenshot-1.png)
~~~

### Adding a cover image
To use a cover image, add a `cover.jpg` in the `assets/` directory (or a `cover.html` file if you'd prefer a HTML-based cover page). If you don't want a cover image, delete these files.
If your cover is in a PNG format you can store the file in the `assets/` directory and then in the `ibis.php` file you can adjust the `cover` configuration where you can set the cover file name, for example:

~~~php
    'cover' => [
        'position' => 'position: absolute; left:0; right: 0; top: -.2; bottom: 0;',
        'dimensions' => 'width: 210mm; height: 297mm; margin: 0;',
        'image' => 'cover.png',
    ],
~~~

### Setting the page headers

You can set a page header for the pages.
To add the header you need to set the `header` parameter in the `ibis.php` configuration file.
For example, in the `ibis.php` file, you can set:

~~~php
     /**
      * CSS inline style for the page header.
      * If you want to skip header, comment the line
      */
     'header' => 'font-style: italic; text-align: right; border-bottom: solid    1px #808080;',
~~~

If you want to customize the page header for each section, in the markdown file, you can add in the frontmatter section the `title` parameter:

~~~markdown
---
title: My Title
---

## My Section Title
This is an example.

~~~

<p align="center">
    <img src="https://raw.githubusercontent.com/hi-folks/ibis-next/main/art/ibis-next-setting-page-header.png" alt="Setting the Page Header" width="480">
Setting the page header.
</p>

## Using Fonts

Edit your `/ibis.php` configuration files to define the font files to be loaded from the `/assets/fonts` directory. After that you may use the defined fonts in your themes (`/assets/theme-light.html` & `/assets/theme-dark.html`).

## Generating PDF eBook

~~~shell
ibis-next pdf
~~~

Ibis will parse the files in alphabetical order and store the PDF file in the `export` directory.

By default, for generating the PDF file, the light theme is used. To generate a PDF using the dark theme:

~~~shell
ibis-next pdf dark
~~~

### Using content from a different directory

If you have your markdown files (your content), in a different directory (by default the content directory is `./content/`) you can define the content directory with the `--content` option:

~~~shell
ibis-next pdf --content=./your-content-directory
~~~

or, in a shorter form, via the `-c` option:

~~~shell
ibis-next pdf -c ./your-content-directory
~~~


## Generating EPUB eBook 🆕

We are introducing a new feature: exporting your eBook in EPUB format.
This is an experimental feature, feel free to use and share your issues or feature requests according to the Contributing guidelines.


```
ibis-next epub
```

Ibis will parse the files in alphabetical order and store the EPUB file in the `export` directory.

By default, for generating the EPUB file, the `assets/style.css` file is used.

If you are managing more than one book, you can use define the working directory. The working directory is the directory where your `assets` folder and `ibis.php` configuration file are located. You can define the path of the working directory via the `-d` option:

```
ibis-next epub -c ../your-dir-with-markdown-files -d ../myibisbook
```

You can combine the usage of the `-c` option for defining the content directory and the `-d` option for defining the working directory.

> You can organize your Markdown files in your content directory in subfolders.

## Generating A Sample

```
ibis-next sample

ibis-next sample dark
```

This command will use the generated files from the `ibis-next build` command to generate samples from your PDF eBook. You can configure which pages to include in the sample by updating the `/ibis.php` file.

## Development

If you want to contribute to the development of this open-source project you can read the CONTRIBUTING.md file, at the root of the project.

## Credits

- [Mohamed Said](https://github.com/themsaid) the author of the original Ibis project
- [Roberto Butti](https://github.com/roberto-butti) the author of the updates and the fork for Ibis Next project
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
