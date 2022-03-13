#!/usr/bin/env bash

CDIR=$(dirname $0)

SCALES=(16 32 48 64 72 96 128 144 152 180 196 256 384 480 512)

META_TEMPLATE_ICONS=""
META_TEMPLATE_SHORTCUT_ICONS=""
HTML_TEMPLATE_HEADER=""

## check binary magick
if [ ! -f `which magick` ]; then
  echo -en "\x1b[1;31;40mmagick not found\x21\n" && exit 1
fi

function scale_img() {
  magick convert $1 -strip -resize $2 -units PixelsPerInch -density 96 -quality 4 -antialias -type TrueColorAlpha $3 
}

function gen_meta_icons() {
context=$(cat<<EOF
{
"src": "$2",
"type": "image/png",
"sizes": "$1"
}
EOF
)
echo $context
}

function gen_link_html() {
context=$(cat<<EOF
<link rel="$2" href="$3" type="image/png" sizes="$1" crossorigin="anonymous"/>
EOF
)
echo $context
}

## favicon
{
  mkdir -p ${CDIR}/assets/icons
  favout=${CDIR}/assets/favicon.ico
  if [ ! -f $favout ]; then
    scale_img ${CDIR}/icon.png 256x256 $favout
  fi
}

for scale in ${SCALES[@]}; do
  resolution=${scale}x${scale}
  path=assets/icons/$resolution.png
  source=${CDIR}/${path}
  if [ ! -f $source ]; then
    scale_img ${CDIR}/icon.png $resolution $source
  fi
  ## manifest.icons
  {
    META_TEMPLATE_ICONS+=$(
      gen_meta_icons $resolution \/$path
    )
    META_TEMPLATE_ICONS+="\n"
  }
  case $scale in
    72|96|128|144|152|180|196|256|384)
      HTML_TEMPLATE_HEADER+=$(
        gen_link_html $resolution "icon" \/$path
      )
      HTML_TEMPLATE_HEADER+="\n"
    ;;
  esac
  case $scale in
    144|152|180|196|256|384)
      ## manifest.icons
      {
        META_TEMPLATE_SHORTCUT_ICONS+=$(
          gen_meta_icons $resolution \/$path
        )
        META_TEMPLATE_SHORTCUT_ICONS+="\n"
      }
      ## apple-touch-icon
      {
        HTML_TEMPLATE_HEADER+=$(
          gen_link_html $resolution "apple-touch-icon" \/$path
        )
        HTML_TEMPLATE_HEADER+="\n"
      }
      ## shortcut icon
      {      	
        HTML_TEMPLATE_HEADER+=$(
      	  gen_link_html $resolution "shortcut icon" \/$path
        )
        HTML_TEMPLATE_HEADER+="\n"
      }
    ;;
  esac
done

META_TEMPLATE_ICONS=$(
  cat<<<$(echo -en $META_TEMPLATE_ICONS)
)

META_TEMPLATE_SHORTCUT_ICONS=$(
  cat<<<$(echo -en $META_TEMPLATE_SHORTCUT_ICONS)
)

HTML_TEMPLATE_HEADER=$(
  cat<<<$(echo -en $HTML_TEMPLATE_HEADER)
)

# manifest.icons
META_TEMPLATE_ICONS=$(cat<<<$(
{
  len=$(cat<<<$META_TEMPLATE_ICONS | wc -l)
  for i in `seq ${len}`; do
    if [ $i -eq $len ]; then
      cat<<<$META_TEMPLATE_ICONS | head -n $i | tail -n 1
    else
      echo -en $(
      	cat<<<$META_TEMPLATE_ICONS | head -n $i | tail -n 1
      )\,
    fi
  done
}
))

# manifest.shortcut.icons
META_TEMPLATE_SHORTCUT_ICONS=$(cat<<<$(
{
  len=$(cat<<<$META_TEMPLATE_SHORTCUT_ICONS | wc -l)
  for i in `seq ${len}`; do
    if [ $i -eq $len ]; then
      cat<<<$META_TEMPLATE_SHORTCUT_ICONS | head -n $i | tail -n 1
    else
      echo -en $(
      	cat<<<$META_TEMPLATE_SHORTCUT_ICONS | head -n $i | tail -n 1
      )\,
    fi
  done
}
))

cat<<EOF>.webmanifest
{
  "short_name": "coconuts",
  "name": "coconuts app",
  "description": "coconuts discuss community forum",
  "icons": $(cat<<<$META_TEMPLATE_ICONS),
  "start_url": "/?viewport=desktop",
  "background_color": "#F4F4F4",
  "display_override": [
    "window-control-overlay",
    "minimal-ui"
  ],
  "scope": "/",
  "theme_color": "#F4F4F4",
  "shortcuts": [
    {
      "short_name": "coconuts",
      "name": "coconuts app",
      "description": "coconuts discuss community forum",
      "url": "/?viewport=desktop&install=true",
      "icons": $(cat<<<$META_TEMPLATE_SHORTCUT_ICONS)
    }
  ]
}
EOF

cat<<EOF>index.html
<!DOCTYPE html PUBLIC>
<html lang="en">
<head>
$(cat<<<$HTML_TEMPLATE_HEADER)
<link rel="manifest" href="/.webmanifest" type="application/manifest+json" crossorigin="anonymous">
</head>
</html>
EOF