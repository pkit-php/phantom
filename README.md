# phantom

A simple module to handle views

## Installation

Using Composer, enter the following command:

```sh
composer require pkit/phantom
```

## Usage

### Rendering

Initially, the page is rendered through the static class ```Pkit\Phantom```.

```php
use Pkit\Phantom;
use Pkit\View;

# ".\view" is the default directory, and views are discovered without file extension
return Phantom::render("page", ["var" => $value]);
/***/
return Phantom::renderView(new View(/* path */), "dir/page");
```

### Pages

The page is written in vanilla PHP, and the extension used is ```.phtml```, which cannot be modified.

```php
<? use Pkit\Phantom ?>

<? /* Passed variables are already converted */ ?>
<?= $var ?>

<? /* Views from other folders are supported */ ?>
<?= Phantom::renderView(new View(/* path */), "dir/page") ?>

```

### Components

```php
# ./view/component.phtml
component
<?= $var ?>
```

To add components, the ```include``` method is basically used.

```php
# ./view/master.phtml
<? use Pkit\Phantom ?>

<?= Phantom::include("component", ["var" => 1]) ?>
```

### Sections

It is possible to create templates using the slot syntax.

```php
# ./view/master.phtml
<? use Pkit\Phantom ?>

<? //* Slots must have unique keys to avoid conflicts */ ?>
<tag>
<?= Phantom::slot("slot_1") ?>
</tag>
<?= Phantom::slot("slot_2") ?>

```

To use them on the page, you should extend the view and thus, by wrapping the sections, they will be delivered to the slots.

```php
<? use Pkit\Phantom ?>
<? Phantom::extend("master")?>

<? Phantom::section("slot_1") ?>
# text...
<? Phantom::stop() ?>
# The text in these intervals will be ignored
# Note: the code in this area will still be executed
<? Phantom::section("slot_2") ?>
# text...
<? Phantom::stop() ?>

```
