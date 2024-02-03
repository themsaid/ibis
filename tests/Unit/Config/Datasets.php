<?php

dataset('paths', [
    ["assets/myfile.png", ["assets/", "myfile.png"]],
    ["./assets/myfile.png", ["./", "assets/", "myfile.png"]],
    ["./assets/myfile.png", ["./", "assets/", "/myfile.png"]],
    ["./assets/myfile.png", [".", "assets", "myfile.png"]],
    ["./export/some.epub", ["./", "export", "some.epub"]],
]);
