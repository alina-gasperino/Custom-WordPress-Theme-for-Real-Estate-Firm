#!/usr/bin/env bash

while read id; do
	wget -O $id.pdf "https://drive.google.com/uc?export=download&id=$id"
done < sample-pdf-ids.txt
