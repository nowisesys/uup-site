#!/bin/bash
#
# Author: Anders Lövgren
# Date:   2015-12-16

set -x

function setup_package()
{
}

function setup_standalone()
{
}

if [ -d vendor/bmc/uup-site ]; then
  setup_package
else
  setup_standalone
fi
