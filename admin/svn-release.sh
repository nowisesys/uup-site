#!/bin/sh
#
# Perform an SVN release in current directory. Requires the script
# svn-release.php to be present. Checksum files will be created, but
# not commited to SVN.
#
# Author: Anders Lövgren
# Date:   2018-08-22

# This script directory:
admin=$(dirname $0)

# Sanity check:
if ! [ -e svn-release.php ]; then
  echo "$0: the svn-release.php script is missing in current directory."
  exit 1
fi

# Fetch new releases from SVN:
php svn-release.php

# Create checksum files:
$admin/checksum.sh

# Add new files to next commit:
files=$(svn st | grep '^\?' | awk -- '{print $2}' | tr '\n' ' ')
if [ -n "$files" ]; then
  svn add $files
fi

# Collect commit statistics:
total=$(svn st | wc -l)
added=$(svn st | grep ^A | wc -l)
changed=$(svn st | grep ^M | wc -l)
deleted=$(svn st | grep ^D | wc -l)

# Display message if anything needs commit:
if [ "$total" != "0" ]; then
  echo "(i) $total files ready for commit (svn ci -m \"<message>\") [added=$added, changed=$changed, deleted=$deleted]"
fi
