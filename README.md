wordprocessindocs2pdf
=====================

The objective is to create a suite of scripts that helps to compare services, tools and library that
converts word processing documents (doc, docx, odt, rtf) to pdf.

* Requirements
  * Mac OS X or linux environment
  * ruby 1.9.x

Supported services and tools

* Services
  * Google Docs [http://www.google.com/apps/intl/it/business/docs.html]. Require: google app account, PHP 5.2.x or higher
  * Convert API [http://www.convertapi.com/]. Require: curl
    
* Tools
  * LibreOffice [http://www.libreoffice.org/]. Require: LibreOffice 3.x

* Setup and Run
  * Copy your .doc files in the "source" folder;
  * execute the run.rb file.
  * The generated pdf are stored in the "output" folder

* TestSuite

You may use the doc collection test powered by the b2xtranslator project here:

http://sourceforge.net/projects/b2xtranslator/files/b2xtranslator/Test%20Suite/Testsuite_doc2x.zip/download