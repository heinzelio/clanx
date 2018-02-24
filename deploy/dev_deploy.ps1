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
Write-Host "check requirements..."
php ./bin/symfony_requirements

[console]::ForegroundColor = "Blue"
Write-Host "remove dev files..."
Remove-Item -Force -Recurse .\.git
Remove-Item -Force -Recurse .\assets
Remove-Item -Force -Recurse .\node_modules
Remove-Item -Force -Recurse .\sql

# go back to where we came from
[console]::ForegroundColor = $originColor
cd $originLocation
