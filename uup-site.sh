#!/bin/bash
# 
# Usage: See --help or usage() function.
# 
# Author: Anders Lövgren
# Date:   2015-12-16

# set -x

srcdir="$(dirname $(realpath $0))"

function setup_config()
{
    mkdir -p config
    for file in apache.conf defaults.site; do
        if ! [ -e config/$file ]; then
            cp -a $srcdir/config/$file.in config/$file
            echo "(i) File config/$file has been installed. Please modify to match your location."
        fi
    done
}

function setup_themes()
{
    if [ -d $srcdir/theme ]; then
        for t in $srcdir/theme/*; do 
            sh $t/setup.sh
        done
    fi

    if [ -d vendor/uup-theme-* ]; then
        for t in vendor/uup-theme-*; do 
            sh $t/setup.sh
        done
    fi
}

function setup_auth()
{
    if ! [ -e public/auth ]; then
        mkdir -p public/auth
    fi
    for dir in logon logoff; do
        if ! [ -e public/auth/$dir ]; then
            cp -a $srcdir/plugins/auth/$dir public/auth/$dir
            mv -f public/auth/$dir/index.inc public/auth/$dir/index.php
        fi
    done
}

function setup_edit()
{
    if ! [ -e public/edit ]; then
        cp -a $srcdir/plugins/edit public/edit
        echo "Install content editors by running setup.sh in public/edit/view/editor/plugins"
    fi
}

function setup_guide() 
{
    if ! [ -e public/guide ]; then
        cp -a $srcdir/plugins/guide public/guide
    fi
}

function setup_dispatcher()
{
    for file in .htaccess dispatch.php; do
        if ! [ -e public/$file ]; then
            cp $srcdir/example/routing/$file public/$file 
        fi
    done
    sed -i s%'/../../vendor/'%'/../vendor/'%1 public/dispatch.php
}

function setup()
{
    setup_config
    setup_themes
    setup_dispatcher
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

function config()
{
    php admin/config.php $*
}

function develop()
{
    ( cd theme/default
      if ! [ -h assets ]; then 
            ln -s public assets
      fi )

    ( cd template
      if [ -d default ]; then
        mv default default-saved
        ln -s ../theme/default/template default
      fi )
}

function usage()
{
    prog=$(basename $0)

    echo "$prog - Setup and management tool."
    echo 
    echo "Usage: $prog --setup [--auth] [--edit] [--guide]"
    echo "       $prog --config <options>"
    echo "       $prog --develop"
    echo "       $prog --migrate <dir>|<file>..."
    echo "Options:"
    echo "  --setup     : Setup site, tools and theme(s)"
    echo "  --config    : Run configuration script (batch)"
    echo "  --develop   : Setup develop mode"
    echo "  --migrate   : Migrate existing site (expert)"
    echo "  --auth      : Install authentication plugin"
    echo "  --edit      : Install online edit plugin"
    echo "  --guide     : Install end-user content publisher guide"
    echo "Example:"
    echo "  # Setup web site as CMS with publisher guide"
    echo "  $prog --setup --auth --edit --guide"
    echo 
    echo "  # Setup for web application"
    echo "  $prog --setup --auth"
    echo 
    echo "Copyright (C) 2015-2018 Nowise Systems and Uppsala University (Anders Lövgren, BMC-IT)"
}

case "$1" in
    --help)
        shift
        usage
        exit 0
        ;;
    --setup)
        shift
        setup
        ;;
    --auth)
        shift
        setup_auth
        ;;
    --edit)
        shift
        setup_edit
        ;;
    --guide)
        shift
        setup_guide
        ;;
    --develop)
        develop
        ;;
    --migrate)
        shift
        migrate $*
        ;;
    --config)
        shift
        config $*
        ;;
    *)
        usage
        exit 1
        ;;
esac
