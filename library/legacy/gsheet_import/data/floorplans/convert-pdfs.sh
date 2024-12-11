#!/usr/bin/env bash

if [[ $# -eq 0 ]]; then
	echo 'no parameters'
	exit
fi

id=${1:0:(-4)}

echo "$n converting file: $id"

if [[ -f "$id-0.jpg" ]]; then
	echo 'already exists.  skipping'
else
	MAGICK_TEMPORARY_PATH=./magick-temp convert -density 200 -alpha remove -quality 90 -resize 50% $1 $id.jpg
fi
