# Changelog

## 1.0.9 - 23th January 2024
- Adding Aside block rendering
- Updating rector 0.19

## 1.0.8 - 2nd January 2024
- Fixing and updating the Sample PDF generation

## 1.0.7 - 31th December 2023
- Fixing Table of Content
- SetList::CODING_STYLE

## 1.0.6 - 30th December 2023
- Updating the Sample book
- Using WebP for the cover image
- Updating Highlightjs CSS

## 1.0.5 - 30th December 2023

- Adding the option `-d` for the `init` command to initialize the ebook in a different directory

## 1.0.4 - 28th December 2023

- Adding the option for customizing the working path (the directory with the assets folder)
- Adding the option for customizing the content path (the directory where you have your Markdown files)
- Now you can organize your markdown files in subfolders
- Eliminating most of the warnings during the EPUB generation process (thanks to the `epubcheck` tool)
- Refactoring the configuration class


## 1.0.3 - 21th December 2023
- Creating the export directory if not exist for EPUB creation
- Improving metadata for EPUB
- Table of Contents or EPUB

## 1.0.2 - 21th December 2023
- Setting the content directory
- Refactoring common code EPUB and PDF build
- Introducing RectorPHP


## 1.0.1 - 21th December 2023
- Welcome to the EPUB generation

## 1.0.0 - 17th December 2023

- upgrade and check with PHP 8.2 and PHP 8.3
- update support for Symfony 7 components
- upgrade code using the new renderer of CommonMark
- upgrade GitHub Actions workflow
- using Pint with PSR12
- added configuration for cover image (instead of using hard-coded cover.jpg, you can specify a new file name and format, for example, my-cover.png)
- added the header config for the CSS style for the page header
- added the front matter capabilities (the title front matter option will be used for the page header).