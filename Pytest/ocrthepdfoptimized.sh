#!/bin/sh
outvar="${1}"
echo "processing file $1"
MYFONTS=$(pdffonts -l 5 "$1" | tail -n +3 | cut -d' ' -f1 | sort | uniq)
echo $MYFONTS
if [ "$MYFONTS" = '' ] || [ "$MYFONTS" = '[none]' ]; then
    echo "NOT OCR'ed: $1"
    #making a pdf to readable format
    outvar="${1}.pdf"
    echo "ocrmypdf -l eng -v -k -d -c -f --deskew --remove-background --tesseract-pagesegmode 1 --tesseract-oem 2 $1 $outvar"
#    sudo /home/deadbrain/.local/bin/ocrmypdf $1 $outvar
#   sudo ocrmypdf --rotate-pages --deskew --remove-background --clean $1 $outvar
    sudo ocrmypdf --rotate-pages --deskew --remove-background --tesseract-timeout 180 $1 $outvar
    sudo chmod -R 777 $outvar
    #extract text from readable pdf into xml format
    xmlvar="$outvar.xml"

    #echo "pdftohtml -c -hidden -xml $outvar $xmlvar"
    pdftohtml -c -i -hidden -xml $outvar $xmlvar
        sudo chmod -R 777 $xmlvar
else
    echo "$1 is OCR'ed."
    #extract text from readable pdf into xml format
    xmlvar="$outvar.xml"
    #echo "pdftohtml -c -hidden -xml $outvar $xmlvar"
    pdftohtml -c -i -hidden -xml $outvar $xmlvar
        sudo chmod -R 777 $xmlvar
  fi
