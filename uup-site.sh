#!/bin/bash
# 
# Usage: 
# 
#   uup-site.sh --setup [--auth] [--edit]   // setup site, tools and theme(s)
#   uup-site.sh --config <options>          // run configuration script (batch)
# 
#   uup-site.sh --develop                   // setup develop mode
#   uup-site.sh --migrate <dir>|<file>...   // migrate existing site (expert)
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
    for dir in logon logoff; do
        if ! [ -e public/$dir ]; then
            cp -a $srcdir/example/secure/$dir public/$dir
        fi
    done
}

function setup_edit()
{
    if ! [ -e public/edit ]; then
        cp -a $srcdir/example/edit public/edit
        echo "Install content editors by running setup.sh in public/edit/view/editor/plugins"
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

case "$1" in
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
        echo "$0 --setup [--auth] [--edit]"
        echo "$0 --config --help"
        exit 1
        ;;
esac
