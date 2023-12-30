<p align="center">
    <img src="https://raw.githubusercontent.com/hi-folks/ibis-next/main/art/ibis-next-cover.png" alt="Ibis Next cover" width="480">
</p>

---

## Create eBooks with Markdown

Are you a content creator looking to streamline your eBook creation process?
Look no further.
Ibis Next is your all-in-one solution, meticulously crafted to empower you in transforming your ideas into polished eBooks with unparalleled ease.

### What is Ibis Next?

Ibis Next is a PHP-based tool specifically designed to simplify the entire eBook creation journey. Leveraging the power of Markdown, it empowers users to focus on content creation while automating the complexities of generating professional-quality eBooks. Whether you're a seasoned author, a technical writer, or someone venturing into the world of eBook creation for the first time, Ibis Next is here to make the process seamless and efficient.

### Key features

- **Markdown Magic**: Write your content using the simplicity and versatility of Markdown.
- **Automatic Generation**: Effortlessly create PDF or EPUB files with a single command using the `ibis-next pdf` command or `ibis-next epub`.
- **Aesthetic Appeal**: Elevate your eBooks with a professionally designed cover photo, a clickable auto-generated table of contents, and code syntax highlighting.
- **Theme Options**: Choose between two visually appealing themes - Light and Dark (theme support available for PDFs).

### Why Choose Ibis Next?

Ibis Next eliminates the complexities of manual formatting, allowing you to concentrate on what matters most - your content. With Ibis Next, the once-daunting process of eBook creation becomes a smooth and enjoyable experience.

This documentation serves as your guide to unlocking the full potential of Ibis Next. Whether you are a seasoned developer integrating Ibis Next into your workflow or a content creator exploring the possibilities, each section will provide insights, instructions, and best practices to ensure a seamless experience.

Get ready to revolutionize your eBook creation process with Ibis Next!

The Ibis project was created by Mohamed Said. The sources of the Ibis project are https://github.com/themsaid/ibis.
We want to say thank you to Mohamed for creating this tool.
Ibis was used to create [Laravel Queues in Action](https://learn-laravel-queues.com), an eBook Mohamed published in August 2020. [Click here](https://learn-laravel-queues.com/laravel-queues-in-action.zip) for the sample.

Why we forked the repository: to speed up the process of supporting PHP 8.2, **PHP 8.3**, **Laravel 10**, **Symfony 7**, **Commonmark 2**, and other dependencies upgrades.
With *Ibis Next* we added also the **support for generating the EPUB format**. So with *Ibis Next* you can create your markdown files and then export them into PDF and EPUB for better compatibility with your devices and software.

## Installation

Before you begin, ensure that you have PHP 8.1 or above installed on your system, and make sure that the gd extension is enabled in your `php.ini` file.


### Installing ibis-next locally

To quickly get started with building your eBook locally, follow these steps:

If you want to start quickly to build your eBook you can:

1. Create a new empty directory via the `mkdir` command and navigate into it:

~~~shell
mkdir my-first-ebook
cd my-first-ebook
~~~

2. Install Ibis Next using Composer:

~~~shell
composer require hi-folks/ibis-next
~~~

3. Once the tool is installed, you'll find the `vendor/` directory containing your new tool (`vendor/bin/ibis-next`).

4. When launching Ibis Next locally in a specific directory, use the following command:

~~~shell
./vendor/bin/ibis-next list
~~~

### Installing ibis-next globally


If you prefer to install the composer package globally, use the `global` option with the `composer require` command:

~~~shell
composer global require hi-folks/ibis-next
~~~

When Ibis Next is installed globally, you can launch and run it using the `ibis-next` command:

~~~shell
ibis-next list
~~~

## Initializing the eBook

To get started, initialize your project directory using the `init` command. This command automatically creates the necessary configuration file, assets folder, and content folder for your Markdown files.

### Locally Installed Ibis Next

If you installed Ibis Next locally, launch the `init` command from your project directory:

~~~shell
./vendor/bin/ibis-next init
~~~

### Globally Installed Ibis Next
If you installed Ibis Next globally, run the `init` command inside an empty directory where you want to create your eBook:

~~~shell
ibis-next init
~~~

This will generate the following files and directories:

- `/assets`
- `/assets/fonts`
- `/assets/cover.jpg`
- `/assets/cover-ibis.webp`
- `/assets/theme-light.html`
- `/assets/theme-dark.html`
- `/assets/style.css`
- `/content`
- `/ibis.php`

Configure your eBook by editing the `ibis.php` configuration file.

### Setting a specific directory

If you prefer to initialize a different empty directory (not the current one), use the `-d` option with the `init` command. For example:

~~~shell
ibis-next init -d ../some-other-directory
~~~

This is especially useful if you want to install Ibis Next once and manage multiple books in separate directories.

## Writing Your eBook

The `init` command will create sample .md files inside the `content` folder. You can explore those files to see how you can write your book.
This sample content is taken from [Ibis Next: create your eBooks with Markdown](https://github.com/Hi-Folks/ibis-next) by Roberto Butti.

Inside the `content` directory, you can write multiple `.md` files. Ibis uses the headings to divide the book into parts and chapters:

~~~markdown
# Part 1

`<h1>` tags define the start of a part. A separate PDF page will be generated to print the part title and any content below.

## Chapter 1

`<h2>` tags define the start of a chapter. A chapter starts on a new page always.

### Starting with Ibis

`<h3>` tags define different titles inside a chapter.
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

In Ibis Next, you have the flexibility to set a customized header for your pages. To do this, navigate to the `ibis.php` configuration file and locate the `header` parameter.
Within the `ibis.php` file, you can specify your desired header like this:

~~~php
     /**
      * CSS inline style for the page header.
      * If you want to skip header, comment the line
      */
     'header' => 'font-style: italic; text-align: right; border-bottom: solid    1px #808080;',
~~~

This allows you to personalize the header content according to your preferences. Feel free to modify the value within the single quotes to suit your specific requirements. The value of the `header` parameter is the CSS inline style you want to apply to your page header.
If you don't need or don't want the page header in your eBook you can eliminate the `header` parameter.

If you want to customize the text of the page header for each section, in the markdown file, you can add in the frontmatter section the `title` parameter:

~~~markdown
---
title: My Title
---

## My Section Title
This is an example.

~~~

![Setting the page header](https://raw.githubusercontent.com/hi-folks/ibis-next/main/art/ibis-next-setting-page-header.png)

### Using Fonts

Edit your `/ibis.php` configuration files to define the font files to be loaded from the `/assets/fonts` directory. After that, you may use the defined fonts in your themes (`/assets/theme-light.html` & `/assets/theme-dark.html`).


## Generating eBook

### Generating PDF eBook

To generate a PDF eBook using Ibis Next, run the following command:

~~~shell
ibis-next pdf
~~~

By default, Ibis Next parses files in alphabetical order and stores the PDF file in the `export` directory.

If you prefer using the dark theme for the PDF, use the following command:

~~~shell
ibis-next pdf dark
~~~

### Using content from a different directory

If your Markdown files (content) are stored in a directory other than the default `./content/`, specify the content directory using the `--content` option:

~~~shell
ibis-next pdf --content=./your-content-directory
~~~

or, using the shorter form with the `-c` option:

~~~shell
ibis-next pdf -c ./your-content-directory
~~~


### Generating EPUB eBook

To automatically generate an EPUB file from your Markdown content, use the `epub` command:

~~~shell
ibis-next epub
~~~

Ibis Next will parse files in alphabetical order and store the EPUB file in the `export` directory. You can easily upload or transfer the EPUB file to your mobile devices, tablets, or Kindle devices.

By default, for generating the EPUB file, the `assets/style.css` file is used.


### Using a different assets/config directory for generating eBook

If you are managing multiple books, you can specify the working directory, the location where your `assets` folder and `ibis.php` configuration file reside. Define the path of the working directory using the `-d` option:

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
