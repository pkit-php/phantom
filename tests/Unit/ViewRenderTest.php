<?php
use Pkit\Phantom;

/** Without variables */

test('render without extend', function () {
   $rendered = Phantom::render("without_extend/simple");

   expect($rendered)->toEqual("layer_without_extend");
});

test('render with one extend', function () {
   $rendered = Phantom::render("with_extend/one");

   expect($rendered)->toEqual("layer_one\nlayer_to_extend\n");
});

test('render with two extend', function () {
   $rendered = Phantom::render("with_extend/two");

   expect($rendered)->toEqual("layer_one\nlayer_two\nlayer_to_extend\n");
});

/** With Variables */

test('render without extend with variables', function ($layer_without_extend) {
   $rendered = Phantom::render("without_extend/variable", compact(["layer_without_extend"]));

   expect($rendered)->toEqual("layer_without_extend=\n$layer_without_extend");
})->with(["1", 2, true, false]);


test('render with one extend with variables', function ($layer_one, $layer_to_extend) {
   $rendered = Phantom::render("with_extend/one_variable", compact(["layer_one", "layer_to_extend"]));

   expect($rendered)->toEqual("layer_one=\n$layer_one\nlayer_to_extend=\n$layer_to_extend\n");
})->with([[1, true], ["a", false]]);

test('render with two extend with variables', function ($layer_one, $layer_two, $layer_to_extend) {
   $rendered = Phantom::render("with_extend/two_variable", compact(["layer_one", "layer_two", "layer_to_extend"]));

   expect($rendered)->toEqual("layer_one=\n$layer_one\nlayer_two=\n$layer_two\nlayer_to_extend=\n$layer_to_extend\n");
})->with([["1", 2, true], ["a", "b", false]]);