# Ibis Book Maker

## Installation

Make sure you have PHP7.3 or above installed on your system.

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

Inside the content directory, you can write multiple `.md` files. Ibis uses the headings to divide the book into parts and chapters:

```
# Part 1

<h1> tags define the start of a part. A separate PDF page will be generated to print the part title and any content below.

## Chapter 1

<h2> tags define the start of a chapter. A chapter starts on a new page always.

### Starting with Ibis

<h3> tags define different titles inside a chapter.
``` 

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

## Credits

- [Mohamed Said](https://github.com/themsaid)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.