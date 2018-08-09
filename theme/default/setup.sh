#!/bin/bash
#
# Author: Anders Lövgren
# Date:   2016-10-17

name=default
destdir=${destdir:-"public"}

cwd=$(pwd)
src=$(realpath $(dirname $0))

mkdir -p $destdir/theme/$name/assets/{css,fonts,img}
mkdir -p $cwd/template/$name

cp -a $src/public/css/* $destdir/theme/$name/assets/css
ln -sf $src/public/img/* $destdir/theme/$name/assets/img
ln -sf $src/public/fonts/* $destdir/theme/$name/assets/fonts

for file in $src/template/*; do
    if ! [ -e $cwd/template/$file ]; then
        cp -a $file $cwd/template/$name
    fi
done
