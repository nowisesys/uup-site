#!/bin/bash
# 
# Usage: 
# uup-site.sh --setup
# uup-site.sh --migrate <dir>|<file>
#        
# Author: Anders Lövgren
# Date:   2015-12-16

# set -x

function setup_config()
{
    srcdir="$1"

    mkdir -p config
    for file in apache.conf defaults.site; do
        if ! [ -e config/$file ]; then
            cp -a $srcdir/config/$file.in config/$file
        fi
    done
}

function setup_themes()
{
    for t in $srcdir/theme/*; do 
        sh $t/setup.sh
    done
    for t in vendor/uup-theme-*; do 
        sh $t/setup.sh
    done
}

function setup_dispatcher()
{
    srcdir="$1"

    for file in .htaccess dispatch.php; do
        if ! [ -e public/$file ]; then
            cp $srcdir/example/routing/$file public/$file 
        fi
    done
    sed -i s%'/../../vendor/'%'/../vendor/'%1 public/dispatch.php
}

function setup_package()
{
    echo "Setup in package mode"
    setup_config vendor/bmc/uup-site
    setup_themes
    setup_dispatcher vendor/bmc/uup-site
}

function setup_standalone()
{
    echo "Setup in standalone mode"
    setup_config .
    setup_themes
    setup_dispatcher .
}

function setup()
{
    if [ -d vendor/bmc/uup-site ]; then
        setup_package
    else
        setup_standalone
    fi
}

function migrate_page()
{
    if [ -e admin/migrate.php ]; then
        php admin/migrate.php $1 -o -f
    fi
}

function migrate_dir()
{
    find "$1" -type f -name *.php | while read f; do
        migrate_page $f
    done
}

function migrate()
{
    shift

    for p in $*; do
        if [ -d $p ]; then
            migrate_dir $p
        elif [ -f $p ]; then
            migrate_page $p
        else
            echo "$0: can't find $p"
        fi
    done
}

case "$1" in
    --setup)
        setup
        ;;
    --migrate)
        migrate $*
        ;;
    *)
        setup
        ;;
esac
