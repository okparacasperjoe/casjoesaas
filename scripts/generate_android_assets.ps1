Add-Type -AssemblyName System.Drawing

$baseDir = Split-Path -Parent $PSScriptRoot
$sourceIcon = Join-Path $baseDir "public\favicon.png"
$sourceLogo = Join-Path $baseDir "public\assets\casjoe_logo.png"
$resDir = Join-Path $baseDir "mobile\android\app\src\main\res"

if (-not (Test-Path $sourceIcon)) {
    Write-Error "Source icon not found: $sourceIcon"
    exit 1
}

$iconBmp = [System.Drawing.Bitmap]::FromFile($sourceIcon)
$logoBmp = [System.Drawing.Bitmap]::FromFile($sourceLogo)

function Resize-SquareImage($src, $size, $destPath, $isForeground = $false) {
    $targetBmp = New-Object System.Drawing.Bitmap($size, $size, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
    $g = [System.Drawing.Graphics]::FromImage($targetBmp)
    $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
    $g.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
    $g.Clear([System.Drawing.Color]::Transparent)

    if ($isForeground) {
        # Foreground icons need padding (approx 66% size in the center so it doesn't get cut by Android masks)
        $innerSize = [int]($size * 0.66)
        $offset = [int](($size - $innerSize) / 2)
        $g.DrawImage($src, $offset, $offset, $innerSize, $innerSize)
    } else {
        $g.DrawImage($src, 0, 0, $size, $size)
    }

    $g.Dispose()
    if (Test-Path $destPath) { Remove-Item $destPath -Force }
    $targetBmp.Save($destPath, [System.Drawing.Imaging.ImageFormat]::Png)
    $targetBmp.Dispose()
    Write-Host "Created: $destPath ($($size)x$($size))"
}

function Create-SplashScreen($logo, $width, $height, $destPath) {
    $targetBmp = New-Object System.Drawing.Bitmap($width, $height, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
    $g = [System.Drawing.Graphics]::FromImage($targetBmp)
    $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
    
    # Brand background: #000066
    $bgBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(255, 0, 0, 102))
    $g.FillRectangle($bgBrush, 0, 0, $width, $height)
    $bgBrush.Dispose()

    # Draw centered logo scaled to max 60% of width or 30% of height
    $maxLogoW = [int]($width * 0.65)
    $maxLogoH = [int]($height * 0.25)
    $aspect = $logo.Width / $logo.Height

    $drawW = $maxLogoW
    $drawH = [int]($drawW / $aspect)
    if ($drawH -gt $maxLogoH) {
        $drawH = $maxLogoH
        $drawW = [int]($drawH * $aspect)
    }

    $x = [int](($width - $drawW) / 2)
    $y = [int](($height - $drawH) / 2)

    $g.DrawImage($logo, $x, $y, $drawW, $drawH)
    $g.Dispose()

    if (Test-Path $destPath) { Remove-Item $destPath -Force }
    $targetBmp.Save($destPath, [System.Drawing.Imaging.ImageFormat]::Png)
    $targetBmp.Dispose()
    Write-Host "Created Splash: $destPath ($($width)x$($height))"
}

# 1. Launcher Mipmap Icons
$mipmapConfigs = @(
    @{ Folder = "mipmap-mdpi"; AppSize = 48; FgSize = 108 },
    @{ Folder = "mipmap-hdpi"; AppSize = 72; FgSize = 162 },
    @{ Folder = "mipmap-xhdpi"; AppSize = 96; FgSize = 216 },
    @{ Folder = "mipmap-xxhdpi"; AppSize = 144; FgSize = 324 },
    @{ Folder = "mipmap-xxxhdpi"; AppSize = 192; FgSize = 432 }
)

foreach ($c in $mipmapConfigs) {
    $dir = Join-Path $resDir $c.Folder
    if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }
    
    Resize-SquareImage $iconBmp $c.AppSize (Join-Path $dir "ic_launcher.png") $false
    Resize-SquareImage $iconBmp $c.AppSize (Join-Path $dir "ic_launcher_round.png") $false
    Resize-SquareImage $iconBmp $c.FgSize (Join-Path $dir "ic_launcher_foreground.png") $true
}

# 2. Splash Screens
$splashConfigs = @(
    @{ Folder = "drawable"; W = 480; H = 800 },
    @{ Folder = "drawable-port-mdpi"; W = 320; H = 480 },
    @{ Folder = "drawable-port-hdpi"; W = 480; H = 800 },
    @{ Folder = "drawable-port-xhdpi"; W = 720; H = 1280 },
    @{ Folder = "drawable-port-xxhdpi"; W = 960; H = 1600 },
    @{ Folder = "drawable-port-xxxhdpi"; W = 1280; H = 1920 },
    @{ Folder = "drawable-land-mdpi"; W = 480; H = 320 },
    @{ Folder = "drawable-land-hdpi"; W = 800; H = 480 },
    @{ Folder = "drawable-land-xhdpi"; W = 1280; H = 720 },
    @{ Folder = "drawable-land-xxhdpi"; W = 1600; H = 960 },
    @{ Folder = "drawable-land-xxxhdpi"; W = 1920; H = 1280 }
)

foreach ($s in $splashConfigs) {
    $dir = Join-Path $resDir $s.Folder
    if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }
    Create-SplashScreen $logoBmp $s.W $s.H (Join-Path $dir "splash.png")
}

$iconBmp.Dispose()
$logoBmp.Dispose()

Write-Host "All Android icons and splash screens successfully generated!"
