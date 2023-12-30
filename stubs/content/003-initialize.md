---
title: Initializing Your eBook
---

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
