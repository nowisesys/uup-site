#!/bin/bash
#
# Author: Anders Lövgren
# Date:   2016-10-17

set -x

name=bootstrap
destdir=${destdir:-"public"}

cwd=$(pwd)
src=$(realpath $(dirname $0))

mkdir -p $destdir/theme/$name/assets/{css,fonts,js,img}
mkdir -p $cwd/template/$name

ln -sf $cwd/vendor/twitter/bootstrap/dist/css/*.min.css $destdir/theme/$name/assets/css
ln -sf $cwd/vendor/twitter/bootstrap/dist/fonts/*       $destdir/theme/$name/assets/fonts
ln -sf $cwd/vendor/twitter/bootstrap/dist/js/*.min.js   $destdir/theme/$name/assets/js
ln -sf $cwd/vendor/frameworks/jquery/jquery.min.js      $destdir/theme/$name/assets/js

cp -a $src/public/css/* $destdir/theme/$name/assets/css
cp -a $src/public/img/* $destdir/theme/$name/assets/img

for file in $src/template/*; do
    if ! [ -e $cwd/template/$file ]; then
        cp -a $file $cwd/template/$name
    fi
done
