$location = Get-Location
if (-not(Test-Path -Path "./deploy")){
    cd ..
    if (-not(Test-Path "./deploy")){
        cd $location
        Write-Error "Directory /deploy does not exist. Please cd into the root of the project." -ErrorAction Stop
    }
}

$configFilePath = ".\deploy\config.ps1"
Write-Verbose "ConfigFilePath: $configFilePath"
. $configFilePath

$deploymentDirectoryPath = Resolve-Path "..\$deploymentDirectoryName"
Write-Verbose "deploymentDirectoryPath: $deploymentDirectoryPath"

[console]::ForegroundColor = "Red"
Write-Host "Before we contiune, update 'publicpath' in webpack.config.js!"
Write-Host "hit enter, edit, save and close the window to continue"
PAUSE #
$webpackConfigFilePath = "$deploymentDirectoryPath\webpack.config.js"
Start-Process -Wait $editorPath $webpackConfigFilePath

[console]::ForegroundColor = "Magenta"
Write-Host "pack the assets..."
.\node_modules\.bin\webpack

cd $location
Write-Verbose "...DONE!"
