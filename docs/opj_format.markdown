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


### Size blocks

* 4-byte blocks seem to indicate the size of the following block (most likely
  in case it contains 0x0A as its data in which case it could not be treated
  as a regular block separator).
* Sometimes there are 4-byte blocks filled with 0s. It seems they just should
  be skipped. There is no zero-length block following them, instead there is
  another non-zero 4-byte size block.

    0x0000, 4 bytes, int
      Size of the following block.


### Problems

* Sometimes, after a size block of 0s, there is no another size block. Instead
  data follows (either binary or ASCII). Some way of detecting this is needed.


File structure
--------------

### Header

A few values separated by spaces (0x20) and terminated with a line feed (0x0A).

    0x0000, 5 chars
        Probably identifier, terminated with space, always "CPYA"?
    0x0005, 6 chars
        File version in MAJOR.MINOR format terminated with space
    0x000C, 4 chars (not constant?)
        Build number terminated with "#"


### Unknown block

After the header there is a size block and then a block with unknown data.
(a subheader with additional information?)


### Data sections

Data section consists of two blocks: header and content. It can contain
worksheet column values, matrix or graph data. liborigin calls each header
and content group a "column".

Data sections seem to end with two size blocks filled with 0s.


#### Data header blocks

Each data header block is prepended with a 0 size block and a following size
block. Size seems to be always 123.

The data header block itself has the following structure:

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
    0x0071, 10 bytes
        Unknown.


#### Data content blocks


