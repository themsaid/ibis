<?php

test('building path dataset', function ($path, $pathToTest) {
    expect(
        \Ibis\Config::buildPath(
            ...$pathToTest
        )
    )->toBeEqualString($path);
})->with("paths");
