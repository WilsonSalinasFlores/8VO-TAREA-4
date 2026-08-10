@echo off
SET PHPRC=C:\MAMP\bin\php\php8.2.14
SET PATH=C:\MAMP\bin\php\php8.2.14;%PATH%
echo Iniciando Laravel API en http://127.0.0.1:8000 ...
"C:\MAMP\bin\php\php8.2.14\php.exe" artisan serve --host=127.0.0.1 --port=8000
