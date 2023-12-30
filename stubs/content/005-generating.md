---
title: Generating your eBook
---

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

### Using a different assets/config directory for generating an eBook

If you are managing multiple books, you can specify the working directory, the location where your `assets` folder and `ibis.php` configuration file reside. Define the path of the working directory using the `-d` option:

```
ibis-next epub -c ../your-dir-with-markdown-files -d ../myibisbook
```

You can combine the usage of the `-c` option for defining the content directory and the `-d` option for defining the working directory.

>{notice} You can organize your Markdown files in your content directory in subfolders.