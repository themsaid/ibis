<p align="center">
    <img src="https://raw.githubusercontent.com/themsaid/ibis/master/art/cover.png" alt="Ibis logo" width="480">
    
Artwork by <a href="https://twitter.com/ericlbarnes">Eric L. Barnes</a> and <a href="https://twitter.com/Caneco">Caneco</a> from <a href="https://laravel-news.com/ibis-book-maker">Laravel News</a> ❤️.
</p>

---

This PHP tool helps you write eBooks in markdown. Run `ibis build` and an eBook will be generated with:
 
1. A cover photo.
2. Clickable auto-generated table of contents.
3. Code syntax highlighting.
4. Available in 2 themes. Light and dark.

Ibis was used to create [Laravel Queues in Action](https://learn-laravel-queues.com), an eBook I published in August 2020. [Click here](https://learn-laravel-queues.com/laravel-queues-in-action.zip) for the sample.

## Installation

Make sure you have PHP7.3 or above installed on your system and that your gd extension is enabled in your php.ini file.

First, install the composer package globally:

```
composer global require themsaid/ibis
```

Then, run this command inside an empty directory:

```
ibis init
```

This will create the following files and directories:

- /assets
- /assets/fonts
- /assets/cover.jpg
- /assets/theme-light.html
- /assets/theme-dark.html
- /content
- /ibis.php

You may configure your book by editing the `/ibis.php` configuration file.

## Writing Your eBook

The `init` command will create sample .md files inside the content folder. You can explore those files to see how you can write your book. This sample content is taken from [Laravel Queues in Action](https://learn-laravel-queues.com). 

Inside the content directory, you can write multiple `.md` files. Ibis uses the headings to divide the book into parts and chapters:

```
# Part 1

<h1> tags define the start of a part. A separate PDF page will be generated to print the part title and any content below.

## Chapter 1

<h2> tags define the start of a chapter. A chapter starts on a new page always.

### Starting with Ibis

<h3> tags define different titles inside a chapter.
``` 

### Using images

Images can be stored in the content folder and then brought in like this:

```
![Screenshot 1](content/screenshot-1.png)
```

### Adding a cover image
To use a cover image, add a `cover.jpg` in the `assets/` directory (or a `cover.html` file if you'd prefer a HTML-based cover page). If you don't want a cover image, delete these files.


## Using Fonts

Edit your `/ibis.php` configuration files to define the font files to be loaded from the `/assets/fonts` directory. After that you may use the defined fonts in your themes (`/assets/theme-light.html` & `/assets/theme-dark.html`).

## Generating PDF eBook

```
ibis build
```

Ibis will parse the files in alphabetical order and store the PDF file in `/export`.

The default is to generate the PDF using the light theme, to generate a PDF using the dark theme:

```
ibis build dark
```

## Generating A Sample

```
ibis sample

ibis sample dark
```

This command will use the generated files from the `ibis build` command to generate samples from your PDF eBook. You can configure which pages to include in the sample by updating the `/ibis.php` file.

## Development

This project uses PHP CS Fixer with a code standard defined in `.php_cs`.  

To review code out of style, you can run the fix command as a dry run.  Run the composer script like this:

`composer run csfix-review`

To fix the source code, run the following composer script:

`composer run csfix`

## Credits

- [Mohamed Said](https://github.com/themsaid)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
