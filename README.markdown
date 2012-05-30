OpenOPJ
=======

This repository contains a parser of [Origin][] project files (OPJ) written in
PHP. It supports reading worksheet data, parameters and notes.

Although there exist other OPJ parsers, OpenOPJ has been written to merge it
with an existing PHP project. Additionally, it contains detailed documentation
of the supported features of the OPJ format (see `docs/opj_format.markdown`)
and a test suite.

The parser has been developed in the [Minor Laboratory][] at the [University
of Virginia][].

[Origin]: http://www.originlab.com/index.aspx?go=PRODUCTS/Origin
[Minor Laboratory]: http://olenka.med.virginia.edu/CrystUVa/wladek_home.php
[University of Virginia]: http://www.virginia.edu/


Tests
-----

Tests for the supported features are written in PHPUnit 3.6. If you cloned the
repository with submodules you can use `make test` to run them with an
RSpec-like result printer. Otherwise, just run `phpunit` in the repository
directory.

