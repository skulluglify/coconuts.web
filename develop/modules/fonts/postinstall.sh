#!/usr/bin/env bash

cdir=`pwd`
cd $(dirname $0)

if [ ! -f ".installed" ]; then
echo -en >"fonts.css"
fi


## check binary
function check_binary() {
if [ ! -f "$(which $1 | grep -iv 'not found')" ]; then
  echo -en "\x1b[1;31;40m$1 not found\x21\x1b[0m\n" && exit 1
fi
}


check_binary wget
check_binary unzip
check_binary sha256sum
check_binary woff2_compress


function wload() {
  wget -c $1 -O $2
}


# NOTE: italic, oblique
function gen_font_style() {
context=$(cat<<EOF
@font-face {
font-family: "$(echo $1 | sed -e 's/Bolditalic/Bold Italic/g' -e 's/Semibold/Semi Bold/g')";
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
src: url("`echo $3`") format('woff2');
}

EOF
)
echo $context
}


function gen_meta_fonts() {
  IFS=
  cdirname=
  while IFS= read -r line; do
    pathname=$(echo "$line" | sed -e 's/ /\\ /g')
    filename=$(basename $pathname)
    cdirname=$(dirname $pathname)
    pdirname=$(echo $cdirname | cut -d\/ -f1)
    # filename=$(echo $filename | tr '[:upper:]' '[:lower:]')
    outfile="${cdirname}/${filename}"
    src=
    ## create mp
    {
    ## change, convert to woff2
    {
      echo "compress ${outfile} ..."
      # mv "$line" "${outfile}" &>/dev/null
      woff2_compress "${outfile}" &>/dev/null
      rm "${outfile}"
      outfile=$(echo "${outfile}" | sed -e 's/\.ttf$/\.woff2/g' -e 's/\.otf$/woff2/g')
      cryptname=$(sha256sum $outfile | awk '{print $1}')
      cryptfile="${cryptname}.woff2"
      src="${pdirname}/${cryptfile}"
      mv "${outfile}" "${src}"
    }

    ## generated font style
    fontselected=$(echo $filename | grep -Ei '\-(light|regular|medium|bold|semibold|italic|bolditalic|thin)\.(ttf|otf)$')
    if [ -n "${fontselected}" ]; then
      type=$(echo $fontselected | grep -Eio '(light|regular|medium|bold|semibold|italic|bolditalic|thin)' | tr '[:upper:]' '[:lower:]')
      if [ -n "$(echo $type | grep -Eiv 'regular|bold|italic')" ]; then
        ## include types
        fontname=$(echo $(echo $cdirname | cut -d\/ -f1 | cut -d\_ -f1) $type | sed -e "s/\b\(.\)/\u\1/g")
      else
        ## not include types
        fontname=$(echo $(echo $cdirname | cut -d\/ -f1 | cut -d\_ -f1) | sed -e "s/\b\(.\)/\u\1/g")
      fi
      ## URL no-root
      # src=$(echo "/modules/fonts/${src}")
      src=$(echo "./${src}")
      cat<<<$(gen_font_style "$fontname" "$type" "$src")>>"fonts.css"
    else
      ## remove unused files
      if [ -f "${src}" ]; then
        rm "${src}"
      fi
    fi	
    } #& ## disable mp

  done <<<$(find $1 -type f | grep -Ei '\.(ttf|otf)$')

  # delete unused subdir
  if [ -n "$(echo $cdirname)" ]; then
    if [ -d "$(echo $cdirname)" ]; then
      for x in `find $cdirname -type d | grep -Eiv "${cdirname}\$" | uniq -u`; do
        # sleep 2 # wait last processing
        echo delete "$x" ...
        rm -rf "$x"
      done
    fi
  fi
}


function wfont_install() {
  if [ ! -d "$2" ]; then
    mkdir -p "$2"
    echo collect "$2" ... 
    wload "$1" "$2".zip &>/dev/null
    echo unpack "$2" ...
    unzip "$2".zip -d "$2" &>/dev/null
    gen_meta_fonts "$2"
    rm "$2".zip
  fi
}


## was installed
touch ".installed"


## Poppins Font
{
  wfont_install https://fonts.google.com/download?family=Poppins Poppins
}


## Roboto Condensed Font
{
  wfont_install https://fonts.google.com/download?family=Roboto%20Condensed Roboto
}


## montserrat Font
{
  wfont_install https://fonts.google.com/download?family=Montserrat Montserrat
}

## sansita swashed
{
  wfont_install https://fonts.google.com/download?family=Sansita%20Swashed Sansita
}

cd $cdir
