#!/usr/bin/env bash

for x in `ls | grep -Ei '\.php$'`; do

  echo ... "$x" ...
  cat "$x" | grep -i trace | cut -d'"' -f2
done
