param(
    [Parameter(Mandatory=$true)]
    [string]$env,
    [Parameter(Mandatory=$true)][string]$projectPath
)

$location = Get-Location
Write-Verbose "Location: $location"

$configFilePath = "$projectPath\deploy\config.$env.ps1"
Write-Verbose "ConfigFilePath: $configFilePath"
. $configFilePath

cd $projectPath\..\$deploymentDirectory

#This is just for the first release to sym4.0. Later we don't have any sql files.
[console]::ForegroundColor = "Red"
Write-Verbose "Before we contiune, update 'database name' in 018Update.sql!"
Write-Verbose "hit enter, edit, save and close the window to continue"
PAUSE #
$sqlFilePath = "$deploymentDirectoryPath\sql\018Update.sql"
Start-Process -Wait $editorPath $sqlFilePath

Write-Verbose "Before we contiune, update 'connection string' and 'smpt' in .env!"
Write-Verbose "hit enter, edit, save and close the window to continue"
PAUSE #
$envFilePath = "$deploymentDirectoryPath\.env"
Start-Process -Wait $editorPath $envFilePath

[console]::ForegroundColor = "Yellow"
Write-Verbose "install all php dependencies. This takes a while..."
composer install $composerEnv --optimize-autoloader

[console]::ForegroundColor = "Cyan"
Write-Verbose "install all js dependencies and other assets..."
yarn install $yarnEnv
yarn add webpack@3

cd $location
Write-Verbose "...DONE!"
