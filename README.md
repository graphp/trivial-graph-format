# graphp/trivial-graph-format [![Build Status](https://travis-ci.org/graphp/trivial-graph-format.svg?branch=master)](https://travis-ci.org/graphp/trivial-graph-format)

[Trivial Graph Format](http://en.wikipedia.org/wiki/Trivial_Graph_Format) (TGF) is a simple text-based file format for describing graphs.

> Note: This project is in beta stage! Feel free to report any issues you encounter.

**Table of contents**

* [Usage](#usage)
  * [TrivialGraphFormat](#trivialgraphformat)
    * [getOutput()](#getoutput)
* [Install](#install)
* [Tests](#tests)
* [License](#license)

## Usage

### TrivialGraphFormat

#### getOutput()

The `getOutput(Graph $graph): string` method can be used to
export the given graph instance.

```php
$graph = new Fhaculty\Graph\Graph();

$a = $graph->createVertex('a');
$b = $graph->createVertex('b');
$a->createEdgeTo($b);

$exporter = new Graphp\TrivialGraphFormat\TrivialGraphFormat();
$data = $exporter->getOutput($graph);

file_put_contents('example.tgf', $data);
echo $data;
```

The TGF output will look something like this:

```
1 a
2 b
#
1 2
```

This method only supports exporting the basic graph structure, with all
vertices and directed and undirected edges.

Note that TGF does not support the notion of directed and undirected
edges. As such, this method will print two edges in opposite directions
for any undirected edges.

Note that TGF does not support the notion of structured attributes. As
such, this method will print the mathematical attributes as vertex and
edge labels as appropriate.

## Install

The recommended way to install this library is [through composer](http://getcomposer.org). [New to composer?](http://getcomposer.org/doc/00-intro.md)

```JSON
{
    "require": {
        "graphp/trivial-graph-format": "~0.1.0"
    }
}
```

This project aims to run on any platform and thus does not require any PHP
extensions and supports running on legacy PHP 5.3 through current PHP 7+ and
HHVM.
It's *highly recommended to use PHP 7+* for this project.

## Tests

To run the test suite, you first need to clone this repo and then install all
dependencies [through Composer](https://getcomposer.org):

```bash
$ composer install
```

To run the test suite, go to the project root and run:

```bash
$ php vendor/bin/phpunit
```

## License

Released under the terms of the permissive [MIT license](http://opensource.org/licenses/MIT).
