rest-api
========

A Symfony project created on November 23, 2016, 9:14 am.

Symfony Installer

The recommended way to install a Symfony project is to use the installer . This utility will allow us to install Symfony with the version we want.
Installing on Linux

Just launch in the console: 
  Curl -LsS https://symfony.com/installer -o ~ / bin / symfony
  Chmod a + x ~ / bin / symfony


This will download the installer and place it in the bin directory of the logged in user.
Installation in Windows:

First of all, make sure that the PHP executable is available in the command prompt. The installation instructions are available on the official website of PHP . Then, just run it in the command prompt: 

    c: \> php -r "readfile ( 'https://symfony.com/installer');"  > symfony

Then it makes sense to create a file symfony.bat containing @php "%~dp0symfony" %* .


When the installation is finished, run the symfony command to verify the correct operation.
			