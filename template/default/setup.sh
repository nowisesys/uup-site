#!/bin/bash
#
# Author: Anders Lövgren
# Date:   2015-12-14

set -x

name=default
destdir=${destdir:-"public"}

cwd=$(pwd)
src=$(realpath $(dirname $0))

mkdir -p $destdir/theme/$name/assets/{css,fonts,js}

ln -sf $cwd/vendor/twitter/bootstrap/dist/css/*.min.css $destdir/theme/$name/assets/css
ln -sf $cwd/vendor/twitter/bootstrap/dist/fonts/*       $destdir/theme/$name/assets/fonts
ln -sf $cwd/vendor/twitter/bootstrap/dist/js/*.min.js   $destdir/theme/$name/assets/js
ln -sf $cwd/vendor/frameworks/jquery/jquery.min.js      $destdir/theme/$name/assets/js

ln -sf $src/css/* $destdir/theme/$name/assets/css
