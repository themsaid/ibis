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

If you want to automatically generate an EPUB file from your Markdown content you can use the `epub` command:

~~~shell
ibis-next epub
~~~

Ibis will parse the files in alphabetical order and store the EPUB file in the `export` directory.
You can upload or send your EPUB file on your Mobile devices, table or Kindle devices.
