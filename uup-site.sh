#!/bin/bash
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

function setup_templates()
{
    for t in $srcdir/template/*; do 
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
    setup_templates
    setup_dispatcher vendor/bmc/uup-site
}

function setup_standalone()
{
    echo "Setup in standalone mode"
    setup_config .
    setup_templates
    setup_dispatcher .
}

if [ -d vendor/bmc/uup-site ]; then
  setup_package
else
  setup_standalone
fi
