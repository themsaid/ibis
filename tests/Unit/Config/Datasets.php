<?php

dataset('paths', [
    ["assets/myfile.png", ["assets/", "myfile.png"]],
    ["./assets/myfile.png", ["./", "assets/", "myfile.png"]],
    ["./assets/myfile.png", ["./", "assets/", "/myfile.png"]],
    ["./assets/myfile.png", [".", "assets", "myfile.png"]],
]);
