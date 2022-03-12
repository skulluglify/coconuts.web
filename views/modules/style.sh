#!/usr/bin/env bash

for x in `find . -type f | grep -Ei '\.css$' | grep -iv './style.css'`; do echo "@import url(\"$x\")"; done
