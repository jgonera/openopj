OPJ (Origin Project files) format description
=============================================

This file contains a description of OPJ file format. It is wildly incomplete
and contains only what was necessary for the author.

Authors: Juliusz Gonera

Inspired by:

* [liborigin](http://sourceforge.net/projects/liborigin/)
* [liborigin2](http://soft.proindependent.com/liborigin2/)


Header
------

A few values separated by spaces (0x20) and terminated with a line feed (0x0A).

* 0x0000, 5 chars: Probably identifier, terminated with space,
  always "CPYA "?
* 0x0005, 6 chars: File version in MAJOR.MINOR format terminated with space
* 0x000C, 4 chars?: Build number terminated with "#"
    
