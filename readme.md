# ![Markdown](https://github.com/dcurtis/markdown-mark/raw/master/png/39x24-solid.png) Iceberg

Iceberg is a static file blogging platform, built around **Markdown**. It runs on PHP 5.3+, and unlike many alternatives, it doesn't require any external libraries or dependencies. It's as simple download, run the script, and you've got yourself a static blog.
Iceberg is being developed by **[@cyrilmengin](http://twitter.com/cyrilmengin)**.

-----

### Installing

*Iceberg was built on PHP 5.3.8, however, any of the 5.3 versions should be fine.*

Installing Iceberg is very simple, as it doesn't require any specific libraries or extensions, other than the ones that are already installed. Git can be nice to have, but is not required. 
To install Iceberg, go to wherever you want to install Iceberg inside your terminal, and run:

```shell
$ git clone git://github.com/cyrilmengin/iceberg.git TestBlog
```

If you don't have git or don't want to use it, simply use Github's download feature. Congratulations, that's all there is to it, and you've installed Iceberg properly!
Read on to learn how to use and customize Iceberg.

-----

### Using Iceberg

So, you've installed Iceberg. That wasn't too hard, but what now? Well, you'll want to create a new post of course!
To do that, open your text editor of choice, and create a new plain text file with the following at the top of it:

```
-----
title: This is my new post!
slug: this-is-my-new-post
description: My first post
layout: post
-----

# My New Post
This post was made using Iceberg and Markdown!
```

That little chunk at the top of the file is very important. Here's a little description of what the values do:

+ ``title`` : Quite simply, the title of your post.
+ ``author`` : When this value is set, it will override the "global author" which is set in the config. It is optional.
+ ``slug`` : The url slug of your post. If your slug is ``hello-world``, your post will appear at ``example.com/article/hello-world``.
+ ``description`` : A short description of what the post is about. This line is used when generating the RSS feed for your posts.
+ ``layout`` : This is the name of the template you want to use for this specific post. You can read more about this below.
+ **others** : You can have custom values in there. If you want to have a custom tag to use in your layouts, you can set it there.

You can now write your article underneath that information block. 
Once you're done writing your article, save it to ``data/<slug>/<slug>.md`` (the ``slug`` value being the same as in the information block, mentioned above).

If you want to have images or files attached to your blog post, create an ``assets`` folder inside the same folder as the markdown file (so it would be ``data/<slug>/assets``) and put them inside it. 
Link to them *relative* of the post itself, for example: ``![Image](assets/image.png)``. Iceberg will take care of expanding it into an absolute path.

Finally, to generate the static files and actually use Iceberg, go back to the root of the iceberg install, and type the following in your terminal:
Note that you may get some extra output, such as something to do with hooks -- you can read about that below.

```shell
$ ./iceberg generate this-is-my-new-post
=> Generated article "this-is-my-new-post" at "output/articles/this-is-my-new-post/index.html"
```

-----

### Writing Themes

Writing themes with Iceberg is extremely easy and pain-free. Iceberg uses [Twig](http://twig.sensiolabs.org/) for it's templating, making templates clean and simple. You can forget about ugly wordpress-style php-filled files!
First of all, set the template directory you'd like to use in your config file (this is the ``layout: layouts/default/`` line of the config, default being a subdirectory of layouts).

The template directory should contain all the Twig template files with the ``.twig`` extension.
When writing an article, and setting "layout: post" in the information block (for example) you're actually telling Iceberg to use the ``post.twig``template.

#### Reload Files

Another feature of Iceberg is the "reload" file. What it is, is simply a file that contains any pages that should be "reloaded" or "regenerated" when using a specific layout. This can be useful, for example for updating the post list on your homepage, or an RSS feed.

This is a file named ``<layout name>.reload`` at the same level as the corresponding layout file (``<layout name>`` being the name of the layout defined in the article. It corresponds to ``<layout name>.twig``). 

When you run the generate command, before compiling the actual post layout, it will read this file to see if there are any other files that should be reloaded. If there are some, it will compile them. You can also set directories, in which case they'll be copied.

The syntax for these files are the following (Note that you musn't add the ``.twig`` extension when declaring the template name in the reload file):

```yaml
# template: output name
index: index.html
rss: feed.rss

# directory: output path
static: static
```

-----

### Writing Hooks

Iceberg has a hook feature, similar to git. Basically, hooks are scripts that will be run at specific moments during the execution of a command.
They can be useful for example, for uploading your new blog posts automatically, or compiling LESS / HAML files.

Iceberg currently has the following hooks:

+ **preGenerate:** this hook is run before any compiling of posts is done.
+ **postGenerate:** this hook is run after any compiling of posts is done. 

To create a hook, simply create a file in the ``lib/hook`` directory, and put the corresponding code inside. The name of the file should be the name of the hook (but ucfirst), and have "Hook" appended to it. For example, ``PostGenerateHook.php``.
All hooks should extend from either the ``AbstractShellHook`` class if you want to run a command line script, or the ``AbstractCodeHook`` class if you want a pure PHP code hook.

Two example hooks are available in the hook directory, so please look into those to see how the internals of the system work.

-----

### Thanks & Credits

+ **[Michel Fortin](https://github.com/michelf)** for the library used to parse Markdown.
+ **[SPYC](http://code.google.com/p/spyc/)** which is used for parsing YAML.
+ **[Twig](http://twig.sensiolabs.org/)** which is used for templating.

-----

### License

Iceberg is licensed under the [WTFPL](http://sam.zoy.org/wtfpl/COPYING) license, so go wild, do what you want. Please see the licenses of PHP-Markdown, Twig and SPYC, which are located at the top or bottom of the files used.