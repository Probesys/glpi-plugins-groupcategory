#!/bin/sh
# -*- coding: UTF8 -*-

POTFILE="./projectbridge.pot"

if [ ! -f ${POTFILE} ]; then
    echo "Error !!"
    echo "POT file ${POTFILE} not found: exiting."
    exit 1
fi

for file in $(ls -1 *po)
do
    msgmerge -U --backup=none $file projectbridge.pot
done
