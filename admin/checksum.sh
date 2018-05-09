#!/bin/sh
#
# Generate checksum files.
# 
# Author: Anders Lövgren
# Date:   2018-05-09

find -type f | egrep '/(stable|testing|binary)/' | while read path; do file=${path##*/}; dir=${path%/*}; echo "$dir -> $file"; ( cd $dir; md5sum $file > $file.md5; sha1sum $file > $file.sha1; sha256sum $file > $file.sha256 ); done
