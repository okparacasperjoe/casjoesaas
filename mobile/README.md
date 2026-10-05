# Casjoe Mobile App (Android & iOS Hybrid)

This directory contains the cross-platform native mobile application wrapper for **Casjoe SaaS** (`https://app.casjoe.com`), built using **Capacitor 7**.

---

## Architecture & Features

- **Live SaaS Sync**: Points to `https://app.casjoe.com/dashboard`. Any updates, UI fixes, or new features deployed to production immediately reflect in the mobile app without requiring users to reinstall or update from app stores.
- **Branded Splash & Icons**: Generated across all Android densities (`mdpi`, `hdpi`, `xhdpi`, `xxhdpi`, `xxxhdpi`) featuring the official Casjoe logo and navy `#000066` theme.
- **Whitelist Navigation**: In-app navigation for payment gateways (Paystack, Flutterwave) and Google OAuth login so external browser redirects are prevented.
- **Native Android Hardware Back Support**: Handled in `MainActivity.java` so tapping the device's back button navigates backward through web pages instead of abruptly exiting the app.
- **Native Permissions**: Camera (for QR codes/KYC/receipt uploads), Push Notifications, Biometric/Fingerprint authentication, and Internet access.

---

## How to Build the Android APK

### Option 1: Automatic Cloud Build via GitHub Actions (Recommended - No local SDK setup needed)
1. Push your changes to the GitHub repository:
   ```bash
   git add .
   git commit -m "Add Casjoe mobile wrapper and build workflow"
   git push origin main
   ```
2. In your GitHub repository (`github.com/okparacasperjoe/casjoesaas`), go to the **Actions** tab.
3. Select **Build Casjoe Mobile APK** and click **Run workflow**.
4. Once completed (approx. 2-3 minutes), download the generated `casjoe-app-debug.zip` containing `app-debug.apk`.
5. Install the APK on your Android phone!

---

### Option 2: Using Android Studio on your PC
1. Install [Android Studio](https://developer.android.com/studio) (or via terminal: `winget install Google.AndroidStudio`).
2. Open terminal in the `mobile` folder:
   ```bash
   cd mobile
   npx cap open android
   ```
3. Android Studio will open the `mobile/android` project.
4. Connect your Android phone via USB (with USB Debugging enabled) or use an Android Emulator.
5. Click the green **Run (▶)** button, or go to **Build > Build Bundle(s) / APK(s) > Build APK(s)** to generate the `.apk`.

---

### Option 3: Local Command-Line Build
1. Install JDK 17:
   ```powershell
   winget install Microsoft.OpenJDK.17
   ```
2. Build debug APK using Gradle wrapper:
   ```cmd
   cd mobile\android
   gradlew.bat assembleDebug
   ```
3. Your compiled APK will be located at:
   `mobile\android\app\build\outputs\apk\debug\app-debug.apk`

---

## iOS Packaging (For Apple App Store / TestFlight)
1. To add the iOS platform, run:
   ```bash
   cd mobile
   npx cap add ios
   npx cap sync ios
   ```
2. Open in Xcode on macOS:
   ```bash
   npx cap open ios
   ```
3. Archive and upload to Apple TestFlight / App Store Connect.
