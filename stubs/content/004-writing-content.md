---
title: Writing your eBook
---

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