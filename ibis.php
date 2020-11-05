<?php

return [
    /**
     * The book title.
     */
    'title' => 'The Ocean at the End of the Lane',

    /**
     * The author name.
     */
    'author' => 'Neil Gaiman',

    /**
     * The list of fonts to be used in the different themes.
     */
    'fonts' => [
        //        'calibri' => 'Calibri-Regular.ttf',
        //        'times' => 'times-regular.ttf',
    ],

    'document' => [
        'format' => [210, 297],
        'margin_left' => 27,
        'margin_right' => 27,
        'margin_bottom' => 14,
        'margin_top' => 14,
    ],

    'cover' => [
        'position' => 'position: absolute; left:0; right: 0; top: -.2; bottom: 0;',
        'dimensions' => 'width: 210mm; height: 297mm; margin: 0;',
    ],

    /**
     * Page ranges to be used with the sample command.
     */
    'sample' => [
        [1, 3],
        [80, 85],
        [100, 103]
    ],

    /**
     * A notice printed at the final page of a generated sample.
     */
    'sample_notice' => 'This is a sample from "The Ocean at the End of the Lane" by Neil Gaiman. <br> 
                        For more information, <a href="https://www.neilgaiman.com/">Click here</a>.',
];
