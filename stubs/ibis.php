<?php

return [
    /**
     * The book title.
     */
    'title' => 'Laravel Queues in Action',

    /**
     * The author name.
     */
    'author' => 'Mohamed Said',

    /**
     * The list of fonts to be used in the different themes.
     */
    'fonts' => [
        //        'calibri' => 'Calibri-Regular.ttf',
        //        'times' => 'times-regular.ttf',
    ],

    /**
     * Document Dimensions.
     */
    'document' => [
        'format' => [210, 297],
        'margin_left' => 27,
        'margin_right' => 27,
        'margin_bottom' => 14,
        'margin_top' => 14,
    ],

    /**
     * Table of Contents Levels
     */
    'toc_levels' => [
        'H1' => 0,
        'H2' => 1,
    ],

    /**
     * Cover photo position and dimensions
     */
    'cover' => [
        'position' => 'position: absolute; left:0; right: 0; top: -.2; bottom: 0;',
        'dimensions' => 'width: 210mm; height: 297mm; margin: 0;',
        'image' => 'cover.jpg',
    ],

    /**
     * Page ranges to be used with the sample command.
     */
    'sample' => [
        [1, 3],
        [80, 85],
        [100, 103],
    ],

    /**
     * default commonmark
     */
    'configure_commonmark' => [
    ],
    /**
     * A notice printed at the final page of a generated sample.
     */
    'sample_notice' => 'This is a sample from "Laravel Queues in Action" by Mohamed Said. <br>
                        For more information, <a href="https://www.learn-laravel-queues.com/">Click here</a>.',

    /**
     * CSS inline style for the page header.
     * If you want to skip header, comment the line
     */
    'header' => 'font-style: italic; text-align: right; border-bottom: solid 1px #808080;',
];
