## Setup

### Installing

Iceberg is written in PHP, and you'll need PHP 5.3 or higher for it work properly. Iceberg doesn't install files in your system, so using it is a simple as getting a copy of the files, and then running them. You can do this through git or use the github [download](https://github.com/cyrilmengin/iceberg/archive/dev.zip) feature.

```shell
$ git clone	git://github.com/cyrilmengin/iceberg.git website
```

You might get an error when trying to run the ``iceberg`` executable, something along the lines of ``iceberg: Permission denied``. This means that your shell isn't recognizing the executable, so you'll have to ``chmod +x iceberg`` it to make it a proper executable file.

### Usage

Using Iceberg is done through the command line and the ``iceberg`` executable. The executable will always take one first argument, the name of the command to call, followed by any other arguments in the ``--<key> <value>`` format. Arguments will default to ``true`` if no ``<value>`` element is given. A list of all commands and arguments is available in the [manual](#manual) section. Here are a few examples.

```shell
$ ./iceberg generate --file post.md
$ ./iceberg generate --name post --layout article.twig
$ ./iceberg generate --name post --no-hook
```

### Configuration

The configuration for iceberg is set in the ``config.ini`` file, and as you'll have guessed, it's in ``ini`` format. If you wish, you can pass Iceberg a different config file by using the ``--config <file path>`` argument. Here are the default and required config values.

```ini
[general]
; your current timezone. make sure it is a valid php timezone.
timezone = Europe/Berlin
; the name of the default author. can be overwritten in posts.
author = John Appleseed
; an sqlite database in which old article data will be stored.
database = articles.sqlite

[article]
; the path to post files. the {article} will be replaced with the article name.
input = posts/{article}.md
; the path to layout files. the {layout} will be replaced with the layout name.
layout = layout/{layout}.twig
; the path to the output directory. it should preferably have a trailing slash.
output = output/
```

## Manual

```
BASIC USAGE

	$ ./iceberg <command> [<options>]

COMMANDS & SPECIFIC OPTIONS

	generate
		--file <file path> || --name <post name>
		[--output <output path>]
		[--layout <layout name>]

GLOBAL OPTIONS

	--config <config path>
	--no-hook [<hook name[, ...]>]
```
