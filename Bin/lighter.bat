@ECHO off
REM Enlight
REM
REM LICENSE
REM
REM This source file is subject to the new BSD license that is bundled
REM with this package in the file LICENSE.txt.
REM It is also available through the world-wide-web at this URL:
REM http://enlight.de/license
REM If you did not receive a copy of the license and are unable to
REM obtain it through the world-wide-web, please send an email
REM to license@shopware.de so we can send you a copy immediately.
REM
REM @category   Enlight
REM @package    Enlight_Tool
REM @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
REM @license    http://enlight.de/license     New BSD License


REM Test to see if this was installed via pear
SET ZTMPZTMPZTMPZ=@ph
SET TMPZTMPZTMP=%ZTMPZTMPZTMPZ%p_bin@
REM below @php_bin@
FOR %%x IN ("@php_bin@") DO (if %%x=="%TMPZTMPZTMP%" GOTO :NON_PEAR_INSTALLED)

GOTO PEAR_INSTALLED

:NON_PEAR_INSTALLED
REM Assume php.exe is executable, and that lighter.php will reside in the
REM same file as this one
SET PHP_BIN=php.exe
SET PHP_DIR=%~dp0
GOTO RUN

:PEAR_INSTALLED
REM Assume this was installed via PEAR and use replacements php_bin & php_dir
SET PHP_BIN=@php_bin@
SET PHP_DIR=@php_dir@
GOTO RUN

:RUN
SET LIGHTER_SCRIPT=%PHP_DIR%\lighter.php
"%PHP_BIN%" -d safe_mode=Off -f "%LIGHTER_SCRIPT%" -- %*
