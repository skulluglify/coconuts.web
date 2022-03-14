#!/usr/bin/env bash


echo >"fonts.css"


## check binary
function check_binary() {
if [ ! -f "$(which $1 | grep -iv 'not found')" ]; then
  echo -en "\x1b[1;31;40m$1 not found\x21\x1b[0m\n" && exit 1
fi
}


check_binary wget
check_binary unzip
check_binary woff2_compress


function wload() {
  wget -c $1 -O $2
}


# NOTE: italic, oblique
function gen_font_style() {
context=$(cat<<EOF
@font-face {
font-family: "$(echo $1 | sed -e 's/Bolditalic/Bold Italic/g')";
`case $2 in
light)
echo "font-weight: lighter;"
echo "font-style: normal;"
;;
regular)
echo "font-weight: normal;"
echo "font-style: normal;"
;;
medium)
echo "font-weight: normal;"
echo "font-style: normal;"
;;
bold)
echo "font-weight: bold;"
echo "font-style: normal;"
;;
semibold)
echo "font-weight: bold;"
echo "font-style: normal;"
;;
italic)
echo "font-weight: normal;"
echo "font-style: italic;"
;;
bolditalic)
echo "font-weight: bold;"
echo "font-style: italic;"
;;
thin)
echo "font-weight: normal;"
echo "font-style: normal;"
;;
esac`
src: url(`echo $3 | sed -e 's/\.ttf$/\.woff2/g' -e 's/\.otf$/woff2/g'`) format('woff2');
}
EOF
)
echo $context
}


function gen_meta_fonts() {
  IFS=
  while IFS= read -r line; do
    pathname=$(echo "$line" | sed -e 's/ /\\ /g')
    filename=$(basename $pathname)
    cdirname=$(dirname $pathname)
    filename=$(echo $filename | tr '[:upper:]' '[:lower:]')
    outfile="${cdirname}/${filename}"
    ## change, convert to woff2
    {
      echo "compress ${outfile} ..."
      mv "$line" "${outfile}" &>/dev/null
      woff2_compress "${outfile}" &>/dev/null
      rm "${outfile}"
    }
    ## generated font style
    fontselected=$(echo $filename | grep -Ei '\-(light|regular|medium|bold|semibold|italic|bolditalic|thin)\.(ttf|otf)$')
    if [ -n "${fontselected}" ]; then
      type=$(echo $fontselected | grep -Eio '(light|regular|medium|bold|semibold|italic|bolditalic|thin)')
      fontname=$(echo $cdirname $type | sed -e "s/\b\(.\)/\u\1/g")
      source=$(echo "/assets/fonts/${outfile}")
      cat<<<$(gen_font_style "$fontname" "$type" "$source")>>"fonts.css"
    fi
  done <<<$(find $1 -type f | grep -Ei '\.(ttf|otf)$')    
}


## Poppins Font
{
  if [ ! -d Poppins ]; then
    mkdir -p Poppins
    wload https://fonts.google.com/download?family=Poppins Poppins.zip
    unzip Poppins.zip -d Poppins
    gen_meta_fonts Poppins
    rm Poppins.zip
  fi
}
