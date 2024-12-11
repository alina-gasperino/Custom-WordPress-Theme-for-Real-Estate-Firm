#!/usr/bin/env bash

for f in *.pdf; do
	convert -density 200 -alpha remove -quality 90 $f ${f:0:(-4)}.jpg
done
