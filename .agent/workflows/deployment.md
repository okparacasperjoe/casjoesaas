---
description: How to deploy updates to the production server
---
# Deployment Workflow

The user deploys via FTP using `php deploy.php` (full) or `php deploy_updates.php` (partial).

**Configuration:**

- **FTP Host**: ftp.casjoe.com
- **FTP User**: <app@casjoe.com>
- **FTP Password**: <app@casjoe.com>
- **Remote Path**: `/` (The FTP root maps correctly to the application root).
- **Web Root**: The domain `app.casjoe.com` likely serves the `public/` folder or the root index routes to it. **Script files intended for direct browser execution should be placed in `public/`.**

**Common Issues:**

- Files uploaded to root `/` might not be accessible if the web server points to `public/` or has strict routing.
- Always put migration helpers in `public/` for easy access via `https://app.casjoe.com/filename.php`.
