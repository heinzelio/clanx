param (
   [string]$branch = "dev",
   [string]$target = "..\.."
)

$originLocation = Get-Location
$originColor = [console]::ForegroundColor
cd $target

[console]::ForegroundColor = "Green"
Write-Host "Load project from github"
Remove-Item -Force -Recurse .\clanx.deploy
git clone --depth 1 -b $branch https://github.com/chriglburri/clanx clanx.deploy
cd .\clanx.deploy

[console]::ForegroundColor = "Yellow"
Write-Host "install all php dependencies. This takes a while..."
composer install --no-dev --optimize-autoloader

[console]::ForegroundColor = "Cyan"
Write-Host "install all js dependencies and other assets..."
yarn install --production
#Webpack is usually not used in production. we add it anyway, for packing the assets.
yarn add webpack

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
Start-Process -FilePath "http://localhost/clanx_test/info/"

# go back to where we came from
[console]::ForegroundColor = $originColor
cd $originLocation
