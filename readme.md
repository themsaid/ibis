# Ibis Book Maker

First run this command inside your project root:

```
ibis init
```

This will create the following files and directories:

- /assets
- /assets/cover.jpg
- /content
- /ibis.php

You may configure your book by editing the ibis.php configuration file.

Inside the content directory, you can write multiple `.md` file and then run:

```
ibis build
```

Ibis will parse the files in alphabetical order and store the PDF file in `/export`.