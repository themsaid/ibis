---
title: Installing Ibis Next
---

## Installation

Before you begin, ensure that you have PHP 8.1 or above installed on your system, and make sure that the gd extension is enabled in your `php.ini` file.

>{notice} For more detailed information, refer to the [Ibis Next GitHub repository](https://github.com/Hi-Folks/ibis-next).



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
