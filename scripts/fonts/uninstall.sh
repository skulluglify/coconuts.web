#!/usr/bin/env bash
cdir=`pwd`
cd $(dirname $0)
rm -rvf .installed fonts.css \
Poppins \
Roboto_Condensed \
Montserrat
cd $cdir
