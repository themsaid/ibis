<p align="center">
    <img src="https://raw.githubusercontent.com/hi-folks/ibis-next/main/art/ibis-next-cover.png" alt="Ibis Next cover" width="480">
</p>

---

## Create eBooks with Markdown

Ibis Next is an open-source tool developed for ebook creators who want to focus on content creation.
Ibis Next supports writing in Markdown format and can generate ebooks in PDF, EPUB or HTML format. The tool aims to simplify the ebook creation process, allowing the writers to concentrate on their content while providing functionality for converting it into polished ebooks efficiently.

### What is Ibis Next?

Ibis Next is a PHP-based tool that simplifies the entire eBook creation process. Leveraging the power of Markdown, it empowers users to focus on content creation while automating the complexities of generating professional-quality eBooks. Whether you're a seasoned author, a technical writer, or someone venturing into the world of eBook creation for the first time, Ibis Next is here to make the process seamless and efficient.

### Key features

- **Markdown**: Write your content using the simplicity and versatility of Markdown.
- **Automatic Generation**: Effortlessly create PDF, EPUB or HTML files with a single command using the `ibis-next pdf` command or `ibis-next epub` or `ibis-next html`.
- **Aesthetic Appeal**: create your eBooks with a custom cover photo, a clickable auto-generated table of contents, and code syntax highlighting.
- **Theme Options**: Choose between two visually appealing themes - Light and Dark (theme support available for PDFs).

### Why Choose Ibis Next?

Ibis Next is a powerful tool for effortlessly creating digital books (e-books) in EPUB, PDF, and HTML formats. With Ibis Next, writers can concentrate on crafting content without worrying about formatting. The content is authored in Markdown format, allowing for simplicity and flexibility.

Ibis Next seamlessly handles the conversion process, ensuring a hassle-free transition from Markdown to the correct EPUB, PDF and HTML formats. Embracing markdown streamlines the writing process and enhances collaboration and ease of editing, making it an ideal choice for authors seeking efficiency and focusing on content creation.

Get ready to revolutionize your eBook creation process with Ibis Next!

Mohamed Said created the Ibis project. The sources of the Ibis project are https://github.com/themsaid/ibis.
Thank you to Mohamed for creating this tool.

We forked the repository to speed up the process of supporting PHP 8.2, **PHP 8.3**, **Laravel 11**, **Symfony 7**, **Commonmark 2**, and other dependencies upgrades.
With Ibis Next, we also added the **support for generating the EPUB format** and **HTML format**. So, with Ibis Next, you can create Markdown files and export them into PDF, EPUB, and HTML for better compatibility with your devices and software.

## Installation

Before you begin, ensure that you have PHP 8.1 or above installed on your system, and ensure the gd extension is enabled in your `php.ini` file.


### Installing ibis-next locally

To quickly get started with building your eBook locally, follow these steps:

If you want to start quickly to build your eBook, you can:

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

To get started, initialize your project directory using the `init` command. This command automatically creates the necessary configuration file, the assets folder, and the content folder for your Markdown files.

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
- `/assets/theme-html.html`
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

### Adding Aside
Inspired by the great Astro Starlight tool for creating technical documentation, we support aside block.
Taking the definition from Astro Starlight documentation:
Asides (also known as “admonitions” or “callouts”) help display secondary information alongside a page’s main content.

<p align="center">
    <img src="https://raw.githubusercontent.com/hi-folks/ibis-next/main/art/aside-examples.png" alt="Aside block examples" width="480">
</p>


Ibis Next offers a tailored Markdown syntax designed for presenting asides. To demarcate aside blocks, use a set of triple colons `:::` to enclose your content, specifying the type as `note`, `tip`, `caution`, or `danger`.

While you can nest various other Markdown content types within an aside, using asides for brief and succinct portions of the content is recommended.

~~~markdown
:::note
**Ibis Next** is an open-source tool, and you can contribute to the project by joining the [Ibis Next GitHub repository](https://github.com/Hi-Folks/ibis-next).
:::

:::warning
**Ibis Next** is an open-source tool, and you can contribute to the project by joining the [Ibis Next GitHub repository](https://github.com/Hi-Folks/ibis-next).
:::

:::tip
**Ibis Next** is an open-source tool, and you can contribute to the project by joining the [Ibis Next GitHub repository](https://github.com/Hi-Folks/ibis-next).
:::

:::danger
**Ibis Next** is an open-source tool, and you can contribute to the project by joining the [Ibis Next GitHub repository](https://github.com/Hi-Folks/ibis-next).
:::
~~~

You can also customize the title of the aside block using the square brackets `[your title]` in this way:

~~~markdown
:::tip[My two cents]
I want to give you some advice: use **Ibis Next** to create your e-books.
:::
~~~

In the example above, the aside type "tip" was used (`:::tip`), with a custom title "My two cents" (`[My two cents]`), and the content of the block can contain text formatted with classic Markdown markers.

### Adding different quotes
For historical reasons, Ibis Next also supports another syntax for the quotes. I suggest using the Aside block instead of these (deprecated) quotes.
Three quotes can be added: `quote`, `warning`, and `notice`.

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
To use a cover image, add a `cover.jpg` in the `assets/` directory (or a `cover.html` file if you'd prefer an HTML-based cover page). If you don't want a cover image, delete these files.
If your cover is in a PNG format, you can store the file in the `assets/` directory, and then in the `ibis.php` file, you can adjust the `cover` configuration where you can set the cover file name, for example:

~~~php
    'cover' => [
        'position' => 'position: absolute; left:0; right: 0; top: -.2; bottom: 0;',
        'dimensions' => 'width: 210mm; height: 297mm; margin: 0;',
        'image' => 'cover.png',
    ],
~~~

> You can use WebP, PNG, or JPG formats for the cover image.

### Setting the page headers

In Ibis Next, you can set a customized header for your pages. To do this, navigate to the `ibis.php` configuration file and locate the `header` parameter.
Within the `ibis.php` file, you can specify your desired header like this:

~~~php
     /**
      * CSS inline style for the page header.
      * If you want to skip header, comment the line
      */
     'header' => 'font-style: italic; text-align: right; border-bottom: solid    1px #808080;',
~~~

This allows you to personalize the header content according to your preferences. Feel free to modify the value within the single quotes to suit your specific requirements. The value of the `header` parameter is the CSS inline style you want to apply to your page header.
You can eliminate the `header` parameter if you don't need or don't want the page header in your eBook.

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

Edit your `/ibis.php` configuration files to define the font files to be loaded from the `/assets/fonts` directory. Afterward, you may use the defined fonts in your themes (`/assets/theme-light.html` & `/assets/theme-dark.html`).

### Setting the Attributes

Considering that the process of converting Markdown to PDF involves generating HTML, you can apply CSS styles to set specific class styles.
To add a CSS class (or any attribute) to an element in Markdown, you can use attribute syntax.

For example, to add a CSS class to an image, consider that the conversion from Markdown results in an HTML `p` element containing an `img` element.
You can define a CSS style like this:

```css
.image-container {
    text-align: center; /* Center the content inside the paragraph */
    padding: 20px; /* Optional: Add padding around the paragraph */
    background-color: #f5f5f5; /* Optional: Set a background color */
    border-radius: 10px; /* Optional: Add rounded corners */
}

.image-container img {
    max-width: 80vw; /* Set the image to 80% of the viewport width */
    height: auto; /* Maintain the aspect ratio */
    display: inline-block; /* Ensure the image behaves like an inline-block element */
}
```

Then, when you need to embed an image in Markdown using the image-container CSS class, you can use the following syntax:

```md
{#id-cover-001 .image-container}
![Ibis Next Cover Image](./content/images/ibis-next-cover.png)
```


## Generating eBook

### Generating PDF eBook

To generate a PDF eBook using Ibis Next, run the following command:

~~~shell
ibis-next pdf
~~~

Ibis Next parses files alphabetically by default and stores the PDF file in the `export` directory.

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

Ibis Next will parse files alphabetically and store the EPUB file in the `export` directory. You can easily upload or transfer the EPUB file to your mobile, tablet, or Kindle devices.

By default, the `assets/style.css` file is used to generate the EPUB file.


### Using a different assets/config directory for generating an eBook

If you manage multiple books, you can specify the working directory, the location of your assets folder, and the `ibis.php` configuration file. Define the path of the working directory using the `-d` option:

```
ibis-next epub -c ../your-dir-with-markdown-files -d ../myibisbook
```

You can combine the usage of the `-c` option for defining the content directory and the `-d` option for defining the working directory.

> You can organize your Markdown files in your content directory in subfolders.

### Generating HTML eBook

To automatically generate an HTML file from your Markdown content, use the `html` command:

~~~shell
ibis-next html
~~~

Ibis Next will parse files alphabetically and store the HTML file in the `export` directory. You can easily read the HTML file with any Browsers.

By default, the `assets/theme-html.html` file is used to generate the HTML file.

## Markdown Files List Configuration
The `md_file_list` configuration (in the `ibis.php` configuration file) allows you to specify which Markdown files should be included when generating PDF, EPUB, or HTML outputs. By default, if `md_file_list` is not set, all Markdown files in the content directory will be used.

### Usage
If you want to limit the files to a specific subset, you can define the `md_file_list` array with the filenames (including extensions) as follows:

```php
'md_file_list' => [
    'routing.md',
    'artisan.md',
    'console-tests.md',
],
```
In this example, only `routing.md`, `artisan.md`, and `console-tests.md` from the content directory will be processed for PDF, EPUB, or HTML generation.

### Notes
- Default Behavior: If `md_file_list` is not specified, all Markdown files in the content directory will be included.
- File Paths: Ensure that the filenames listed in `md_file_list` include the correct extensions and are located within the content directory.

This configuration provides flexibility in selecting specific files for your output needs, enabling you to tailor the content as required.

For example, you can use `md_file_list` if you need to build a sample of your book.


## Generating A Sample

For generating a sample, I suggest to evaluate using the `md_file_list` configuration.
For historical reasons also the sample command is available but it works only for PDF files.

```
ibis-next sample

ibis-next sample dark
```

This command will use the generated files from the `ibis-next build` command to generate samples from your PDF eBook. You can configure which pages to include in the sample by updating the `/ibis.php` file.

## Feedback
If you are using or if you are evaluating using Ibis Next for creating your next eBook, let me know.
I can support you by demoing the tool, helping with the configuration or evaluating feature requests.



## Development

If you want to contribute to developing this open-source project, you can read the CONTRIBUTING.md file at the project's root.

## Credits

- [Mohamed Said](https://github.com/themsaid) the author of the original Ibis project
- [Roberto Butti](https://github.com/roberto-butti) the author of the updates and the fork for Ibis Next project
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
