param (
   [Parameter(Mandatory=$true)]
   [string]$branch,
   [string]$target = "..\.."
)

$originLocation = Get-Location
$originColor = [console]::ForegroundColor
cd $target
$targetAbs = Get-Location

. $originLocation\subScripts\cleanAndLoad.ps1
. $originLocation\subScripts\installDependencies.ps1



[console]::ForegroundColor = "Red"
Write-Host "before we contiune, update 'publicpath' in webpack.config.js!"
Write-Host "hit enter, edit, save and close the window to continue"
PAUSE #
Start-Process -Wait "C:\Program Files\EditPlus\editplus.exe" webpack.config.js
[console]::ForegroundColor = "Magenta"
Write-Host "pack the assets..."
.\node_modules\.bin\webpack

[console]::ForegroundColor = "Blue"
Write-Host "warm up the cache..."
php .\bin\console cache:warmup --env=prod

[console]::ForegroundColor = "Green"
Write-Host "remove dev files..."
Remove-Item -Force -Recurse .\.git
Remove-Item -Force -Recurse .\assets
Remove-Item -Force -Recurse .\node_modules
Remove-Item -Force -Recurse .\sql
Remove-Item -Force .env.dist
Remove-Item -Force .gitignore
#Keep composer.json. The Kernel.php needs it!
#Remove-Item -Force composer.json
Remove-Item -Force composer.lock
Remove-Item -Force mklink.bat
Remove-Item -Force package-lock.json
Remove-Item -Force package.json
Remove-Item -Force phpunit.xml.dist
Remove-Item -Force README.md
Remove-Item -Force symfony.lock
Remove-Item -Force webpack.config.js
Remove-Item -Force yarn-error.log
Remove-Item -Force yarn.lock

#Check the page if it runs locally
Start-Process -FilePath "http://localhost/clanx_deploy/info/"

# go back to where we came from
[console]::ForegroundColor = $originColor
cd $originLocation
