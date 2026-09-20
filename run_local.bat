cd /d "%~dp0"
php -S localhost:8000 -t public "%~dp0public\router.php"
