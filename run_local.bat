@echo off
echo Starting PHP Built-in Server...
echo listening on http://localhost:8000
echo using document root: ./public_html
echo.
echo Press Ctrl+C to stop.
echo.

php -S localhost:8000 -t public_html
