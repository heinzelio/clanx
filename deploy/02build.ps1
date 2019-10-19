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

#This is just for the first release to sym4.0. Later we don't have any sql files.
[console]::ForegroundColor = "Red"
Write-Host "Before we contiune, update 'database name' in 018Update.sql!"
Write-Host "hit enter, edit, save and close the window to continue"
PAUSE #
$sqlFilePath = "$deploymentDirectoryPath\sql\018Update.sql"
Start-Process -Wait $editorPath $sqlFilePath

Write-Host "Before we contiune, update 'connection string' and 'smpt' in .env!"
Write-Host "hit enter, edit, save and close the window to continue"
PAUSE #
$envFilePath = "$deploymentDirectoryPath\.env"
Start-Process -Wait $editorPath $envFilePath

[console]::ForegroundColor = "Yellow"
Write-Verbose "install all php dependencies. This takes a while..."
composer install --no-dev --optimize-autoloader

[console]::ForegroundColor = "Cyan"
Write-Verbose "install all js dependencies and other assets..."
yarn install --production
yarn add webpack@3

Set-Location $location
Write-Verbose "...DONE!"
