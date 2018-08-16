#!/bin/bash
# 
# Usage: See --help or usage() function.
# 
# Author: Anders Lövgren
# Date:   2015-12-16

# This script name and version:
prog=$(basename $0)
vers="1.0"

# The source directory:
srcdir="$(dirname $(realpath $0))"

# Default HTTP request location:
location="/"

function bootstrap() 
{
    if [ "$1" == "--help" -o "$1" == "-h" ]; then
        composer init --help
        exit 0
    fi

    composer init \
        --type=project \
        --stability=stable \
        --license="Apache-2.0" \
        --repository="{\"type\":\"composer\",\"url\":\"https://it.bmc.uu.se/andlov/php/uup-site/\"}" \
        --repository="{\"type\":\"composer\",\"url\":\"https://it.bmc.uu.se/andlov/php/uup-auth/\"}" \
        --repository="{\"type\":\"composer\",\"url\":\"https://it.bmc.uu.se/andlov/php/uup-soap/\"}" \
        --repository="{\"type\":\"composer\",\"url\":\"https://it.bmc.uu.se/andlov/php/uup-mail/\"}" \
        --repository="{\"type\":\"composer\",\"url\":\"https://it.bmc.uu.se/andlov/php/uup-html/\"}" \
        --repository="{\"type\":\"composer\",\"url\":\"https://it.bmc.uu.se/andlov/php/uup-web-component/\"}" "$@" && \
    composer require bmc/uup-site && \
    echo "(i) Bootstrap completed. Please run --setup to initialize."
}

function setup_config()
{
    mkdir -p config
    for file in apache.conf defaults.site; do
        if ! [ -e config/$file ]; then
            cp -a $srcdir/config/$file.in config/$file
            sed -i -e s%"@location@"%"${location}"%g \
                   -e s%"@excluded@"%"theme\|assets\|example"%g \
                   -e s%"//\(.*'location'.*\)"%"\1"%1 config/$file
            echo "(i) File config/$file has been installed (please modify)."
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

    if ! [ -d vendor/bmc/uup-auth ]; then
        composer require bmc/uup-auth
    fi

    if ! [ -f config/auth.inc.in ]; then
        cp -a $srcdir/config/auth.inc.in config/auth.inc.in
    fi
    if ! [ -f config/auth.inc ]; then
        mv -f config/auth.inc.in config/auth.inc
        echo "(i) Open config/auth.inc to configure the authenticate stack."
    fi
}

function setup_edit()
{
    if ! [ -e public/edit ]; then
        cp -a $srcdir/plugins/edit public/edit
        for dir in ajax view; do
            mv -f public/edit/$dir/index.inc public/edit/$dir/index.php
        done
        echo "(i) Install content editors by running setup.sh in public/edit/view/editor/plugins"
    fi
}

function setup_locale() 
{
    if ! [ -d locale ]; then
        mkdir -p locale
        cp -a $srcdir/admin/Makefile .
        echo "(i) Edit settings in makefile, then run 'make new-locale' and 'make' in current directory."
    fi
}

function setup_guide() 
{
    if ! [ -e public/guide ]; then
        cp -a $srcdir/plugins/guide public/guide
    fi

    if ! [ -d vendor/bmc/uup-web-component ]; then
        composer require bmc/uup-web-component
    fi
}

function setup_examples() 
{
    if ! [ -d public/example ]; then
        cp -a $srcdir/example public/example

        find public/example -type f -name *.php | while read file; do
            sed -i s%'/vendor/'%'/../vendor/'%1 $file
        done

        if [ -f public/example/routing/.htaccess ]; then
            sed -i -e s%"@location@"%"${location}"%g \
                   -e s%"@excluded@"%"theme|assets"%g \
                         public/example/routing/.htaccess
        fi
    fi
}

function setup_dispatcher()
{
    for file in .htaccess dispatch.php; do
        if ! [ -e public/$file ]; then
            cp -a $srcdir/example/routing/$file public/$file 
            sed -i -e s%'/../../vendor/'%'/../vendor/'%1 \
                   -e s%"@location@"%"${location}"%g \
                   -e s%"@excluded@"%"theme|assets|example"%g \
                   -e s%'/example/routing'%''%g public/$file
            echo "(i) File public/$file has been installed (please modify)."
        fi
    done
}

function setup_pages() 
{
    if ! [ -f public/index.php ]; then
        cp -a $srcdir/admin/hello/* public
    fi
}

function setup()
{
    setup_config
    setup_themes
    setup_dispatcher
    setup_pages
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
    php $srcdir/admin/config.php $*
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

function cleanup()
{
    ( cd public
      rm -rf fragment
      rm -f index.php view.php welcome.php
      rm -f standard.menu )
}

function usage()
{
    echo "$prog - Setup and management tool."
    echo 
    echo "Usage: $prog --bootstrap [<composer-options>]"
    echo "       $prog --setup [--auth] [--edit] [--locale] [--guide] [--examples]"
    echo "       $prog --config <options>"
    echo "       $prog --develop"
    echo "       $prog --migrate <dir>|<file>..."
    echo 
    echo "Options:"
    echo "  --bootstrap : Bootstrap new instance using composer."
    echo "  --setup     : Setup site, tools and theme(s)."
    echo "  --auth      : Install authentication plugin."
    echo "  --edit      : Install online edit plugin."
    echo "  --locale    : Install support for gettext translation."
    echo "  --guide     : Install end-user content publisher guide."
    echo "  --examples  : Install examples in public."
    echo "  --cleanup   : Remove installation test files."
    echo "  --config    : Run configuration script (batch)."
    echo "  --develop   : Setup develop mode."
    echo "  --migrate   : Migrate existing site (expert)."
    echo "  --verbose   : Be verbose about executed commands."
    echo "  --version   : Display version of this script."
    echo 
    echo "Example:"
    echo "  # Generate composer.json and install requirements (see --bootstrap --help)"
    echo "  $prog --bootstrap"
    echo 
    echo "  # Setup for virtual host"
    echo "  $prog --setup --auth --edit --guide"
    echo 
    echo "  # Setup for web application"
    echo "  $prog --setup --auth"
    echo 
    echo "  # Setup for location /myapp"
    echo "  $prog --location /myapp --setup --auth"
    echo
    echo "Notice:"
    echo "  1. The --location or --verbose options must be used before any other option."
    echo 
    echo "Copyright (C) 2015-2018 Nowise Systems and Uppsala University (Anders Lövgren, BMC-IT)"
}

function version()
{
    echo "$prog v$vers"
}

# Relocate srcdir when running in bootstrap mode:
if [ -d vendor/bmc/uup-site ]; then
    srcdir="$(pwd)/vendor/bmc/uup-site"
fi

while [ -n "$1" ]; do
    case "$1" in
        --verbose|-v)
            set -x
            ;;
        --help|-h)
            usage
            exit 0
            ;;
        --version|-V)
            version
            exit 0
            ;;
        --bootstrap)
            shift
            bootstrap "$@"
            exit 0
            ;;
        --setup)
            setup
            ;;
        --auth)
            setup_auth
            ;;
        --edit)
            setup_edit
            ;;
        --locale)
            setup_locale
            ;;
        --guide)
            setup_guide
            ;;
        --examples)
            setup_examples
            ;;
        --cleanup)
            cleanup
            ;;
        --develop)
            develop
            ;;
        --migrate)
            migrate $*
            ;;
        --config)
            config $*
            ;;
        --location)
            shift
            location="$(realpath -sm $1)/"
            ;;
        *)
            usage
            exit 1
            ;;
    esac
    shift
done
