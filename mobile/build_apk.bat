@echo off
echo ========================================================
echo        Building Casjoe Mobile App (Android APK)
echo ========================================================

cd /d "%~dp0"

echo [1/3] Syncing Capacitor assets...
call npx cap sync android
if %errorlevel% neq 0 (
    echo [ERROR] Capacitor sync failed!
    pause
    exit /b %errorlevel%
)

echo [2/3] Building Android Debug APK...
cd android
call gradlew.bat assembleDebug
if %errorlevel% neq 0 (
    echo.
    echo [ERROR] Gradle build failed!
    echo Ensure JAVA_HOME is configured to a valid JDK 17/21 installation.
    echo Or install Android Studio and run 'npx cap open android'.
    pause
    exit /b %errorlevel%
)

echo.
echo ========================================================
echo [SUCCESS] APK Built successfully!
echo Location: %~dp0android\app\build\outputs\apk\debug\app-debug.apk
echo ========================================================
pause
