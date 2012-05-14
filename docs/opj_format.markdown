OPJ (Origin Project files) format description
=============================================

This file contains a description of OPJ file format. It is wildly incomplete
and contains only what was necessary for the author.

Authors: Juliusz Gonera

Inspired by:

* [liborigin](http://sourceforge.net/projects/liborigin/)
* [liborigin2](http://soft.proindependent.com/liborigin2/)


General observations
--------------------

* Files seem to be divided into blocks sperated by line feeds (0x0A).
* Integers are little endian.
* 4-byte blocks seem to indicate the size of the upcoming block (probably
  in case it contains 0x0A as its data in which case it cannot be treated
  as a regular block separator).
* A 4-byte block filled with 0x0 immediately followed by another 4-byte block
  seems to be and ID of a structure.


File structure
--------------

### Header

A few values separated by spaces (0x20) and terminated with a line feed (0x0A).

    0x0000, 5 chars
        Probably identifier, terminated with space, always "CPYA"?
    0x0005, 6 chars
        File version in MAJOR.MINOR format terminated with space
    0x000C, 4 chars or variable?
        Build number terminated with "#"


### Unknown

A block indicating size and then a block with data (a subheader with additional
information?).

    0x0000, 5 bytes
        [size] + line feed.
    0x0005, [size] + 1 bytes
        Data + line feed.


### Data blocks

Each data block starts with a 0x0 + 0x7B two-block identifier:

    0x0000, 5 bytes
        Zeros + line feed.
    0x0005, 5 bytes
        0x7B ID + line feed.

Then the data follows in the next block:

    0x0000, 22 bytes
        Unknown.
    0x0016, 2 bytes, short int
        [dataType] (from liborigin).
    0x0018, 37 bytes
        Unknown.
    0x003D, 1 byte, char
        [valueSize], size of a single data value.
    0x003E, 1 byte
        Unknown.
    0x003F, 1 byte, char
        [dataTypeU] (from liborigin).
    0x0040, 24 bytes
        Unknown.
    0x0058, 25 bytes, zero-padded string
        Data name, for worksheets it's "WORKSHEET_COLUMN".

