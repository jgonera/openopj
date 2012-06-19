OpenOPJ
=======

This repository contains a parser of [Origin][] project files (OPJ) written in
PHP. It supports reading worksheet data, parameters and notes.

Although there exist other OPJ parsers, OpenOPJ has been written to merge it
with an existing PHP project. Additionally, it contains detailed documentation
of the supported features of the OPJ format (see `docs/opj_format.markdown`)
and a test suite.

The parser has been developed in the [Minor Laboratory][] at the [University
of Virginia][] and is licensed under the MIT license.

[Origin]: http://www.originlab.com/index.aspx?go=PRODUCTS/Origin
[Minor Laboratory]: http://olenka.med.virginia.edu/CrystUVa/wladek_home.php
[University of Virginia]: http://www.virginia.edu/


How to use
----------

```php
require_once('openopj/lib/OpenOPJ.php');

// read from file
$opj = new OPJFile(new FileReader('path/file.opj'));
// or read from string (e.g. when stored in DB, fetched from other server)
$opj = new OPJFile(new StringReader($binaryString));

// first row of a column in a worksheet
echo $opj->data['Worksheet_Column'][0];
// parameter
echo $opj->parameters['SOME_NAME'];
// notes, e.g. ResultsLog
echo $opj->notes['ResultsLog'];
```


Tests
-----

Tests for the supported features are written in PHPUnit 3.6. If you cloned the
repository with submodules you can use `make test` to run them with an
RSpec-like result printer. Otherwise, just run `phpunit` in the repository
directory.

