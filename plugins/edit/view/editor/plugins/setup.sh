#!/bin/bash
#
# Setup javascript libraries.
# 
# Usage: setup.sh {ckeditor|codemirror} {git|svn|zip}
# 
# Author: Anders Lövgren
# Date:   2017-04-05

# set -x

now=$(date +"%s")
cwd=$(realpath $(dirname $0))

cd $cwd

pack=${1:-all}
mode=${2:-git}
mini=${3:-dist}

function install_ckeditor_git()
{
    git clone https://github.com/ckeditor/ckeditor-releases.git ckeditor || exit 1
}

function install_ckeditor_svn()
{
    svn co https://github.com/ckeditor/ckeditor-releases.git/trunk ckeditor || exit 1
}

function install_ckeditor_zip()
{
    wget https://github.com/ckeditor/ckeditor-releases/archive/standard/latest.zip || exit 1
    unzip latest.zip || exit 1
    mv ckeditor-releases-standard-latest/* ckeditor || exit 1
}

function install_ckeditor_custom()
{
    wget http://ckeditor.com/builder/download/abc0a42dcd3e62982d0e0da9e7f65dc9 || exit 1
    unzip abc0a42dcd3e62982d0e0da9e7f65dc9 || exit 1
}

function install_codemirror_git()
{
    git clone https://github.com/codemirror/CodeMirror.git codemirror || exit 1
    build_codemirror 
}

function install_codemirror_svn()
{
    svn co https://github.com/codemirror/CodeMirror.git/trunk codemirror || exit 1
    build_codemirror
}

function install_codemirror_zip()
{
    wget https://github.com/codemirror/CodeMirror/archive/master.zip || exit 1
    unzip master.zip || exit 1
    mv CodeMirror-master codemirror || exit 1
    build_codemirror
}

function build_codemirror()
{
    if ! [ -d codemirror ]; then
        echo "$0: build directory codemirror don't exists"
        exit 1
    fi

    cd codemirror
    npm install || exit 1
    cd $cwd
}

function install_ckeditor()
{
    mode=$1

    case "$mode" in
        git)
            install_ckeditor_git
            ;;
        svn)
            install_ckeditor_svn
            ;;
        zip)
            install_ckeditor_zip
            ;;
        custom)
            install_ckeditor_custom
            ;;
        *)
            install_ckeditor_git
            ;;
    esac
}

function install_codemirror()
{
    mode=$1

    case "$mode" in
        git)
            install_codemirror_git
            ;;
        svn)
            install_codemirror_svn
            ;;
        zip)
            install_codemirror_zip
            ;;
        *)
            install_codemirror_git
            ;;
    esac
}

function backup()
{
    pack=$1

    if [ -d ${pack} ]; then
        mv ${pack} ${pack}_${now}
    fi
}

function install()
{
    pack=$1
    mode=$2

    case "$pack" in
        ckeditor)
            install_ckeditor $mode
            ;;
        codemirror)
            install_codemirror $mode
            ;;
    esac
}

if [ "$pack" == "-h" -o "$pack" == "--help" ]; then
    echo "usage: $0 {ckeditor|codemirror} {git|svn|zip|custom}"
elif [ "$pack" == "all" ]; then
    for p in ckeditor codemirror; do
        backup $p
        install $p $mode
    done
else
    backup $pack
    install $pack $mode
fi
