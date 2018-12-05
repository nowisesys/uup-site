#!/bin/bash
#
# Author: Anders Lövgren
# Date:   2018-12-05

name=capture
destdir=${destdir:-"public"}

cwd=$(pwd)
src=$(realpath $(dirname $0))

mkdir -p $cwd/template/$name

for file in $src/template/*; do
    if ! [ -e $cwd/template/$file ]; then
        cp -a $file $cwd/template/$name
    fi
done
