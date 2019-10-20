$location = Get-Location
if (-not(Test-Path -Path "./deploy")){
    Set-Location ..
    if (-not(Test-Path "./deploy")){
        Set-Location $location
        Write-Error "Directory /deploy does not exist. Please cd into the root of the project." -ErrorAction Stop
    }
}

$configFilePath = ".\deploy\config.ps1"
Write-Verbose "ConfigFilePath: $configFilePath"
. $configFilePath

$deploymentDirectoryPath = Resolve-Path "..\$deploymentDirectoryName"
Write-Verbose "deploymentDirectoryPath: $deploymentDirectoryPath"
Set-Location $deploymentDirectoryPath

Write-Host "Before we contiune, update 'connection string' and 'smpt' in .env!"
Write-Host "hit enter, edit, save and close the window to continue"
PAUSE #
$envFilePath = "$deploymentDirectoryPath\.env"
"# this is the .env file" | Out-File -Encoding "UTF8" $envFilePath
Add-Content $envFilePath "";
Add-Content $envFilePath "# You can copy the content of the .env.dist or .env.local file,";
Add-Content $envFilePath "# Or you can copy the content of existing local or serverside .env files.";
Start-Process -Wait $editorPath $envFilePath

[console]::ForegroundColor = "Yellow"
Write-Verbose "install all php dependencies. This takes a while..."
composer install --no-dev --optimize-autoloader

[console]::ForegroundColor = "Cyan"
Write-Verbose "install all js dependencies and other assets..."
# TODO Replace yarn with npm
yarn install --production

Set-Location $location
Write-Verbose "...DONE!"
