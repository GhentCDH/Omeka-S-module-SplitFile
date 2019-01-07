# Split File

Split multipage files into their component pages.

For example, if have an item containing one ten-page file, you can use this
module to split it into ten one-page files assigned to the same item.

This module is not optimized to split very large multipage files. It was tested
on and should be used for files under 90 MB. Larger files should be split using
other tools and added to Omeka separately.

## PDF Splitters:

### jpg

Used to split PDF files into component JPG pages. Requires [convert](https://linux.die.net/man/1/convert),
a part of the ImageMagick suite of tools, and [pdfinfo](https://linux.die.net/man/1/pdfinfo),
a part of the poppler-utils package. If the [Extract Text](https://github.com/omeka-s-modules/ExtractText)
module is installed and active, this module will extract the page text from the
original PDF file and set them to the the component JPG pages.

### pdf

Used to split PDF files into component PDF pages. Requires [pdfseparate](https://www.mankier.com/1/pdfseparate)
and [pdfinfo](https://linux.die.net/man/1/pdfinfo), parts of the poppler-utils
package. If the [Extract Text](https://github.com/omeka-s-modules/ExtractText)
module is installed and active, it will automatically extract the text from the
component PDF pages.


## TIFF Splitters:

### jpg

Used to split TIFF files into component JPG pages. Requires [convert](https://linux.die.net/man/1/convert)
and [identify](https://linux.die.net/man/1/identify), parts of the ImageMagick
suite of tools.

