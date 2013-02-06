## Setup

### Installing

Iceberg is written in PHP, and you'll need PHP 5.3 or higher for it work properly. Iceberg doesn't install files in your system, so using it is a simple as getting a copy of the files, and then running them. You can do this through git or use the github [download](https://github.com/cyrilmengin/iceberg/archive/dev.zip) feature.

```shell
$ git clone	git://github.com/cyrilmengin/iceberg.git website
$ cd website
$ chmod +x iceberg
```

### Usage

Using Iceberg is done through the command line and the ``iceberg`` executable. The executable will always take one first argument, the name of the command to call, followed by any other arguments in the ``--<key> <value>`` format. Arguments will default to ``true`` if no ``<value>`` element is given. A list of all commands and arguments is available in the [man](#manual) file. Here are a few examples.

```shell
$ ./iceberg generate --file post.md
$ ./iceberg generate --name post --layout article.twig
$ ./iceberg generate --name post --no-hook
```

## Miscellaneous

### Manual

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
