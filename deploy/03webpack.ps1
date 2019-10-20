param(
    [Parameter(Mandatory=$true)]
    [string]$env
)

$location = Get-Location
if (-not(Test-Path -Path "./deploy")){
    Set-Location ..
    if (-not(Test-Path "./deploy")){
        Set-Location $location
        Write-Error "Directory /deploy does not exist. Please cd into the root of the project." -ErrorAction Stop
    }
}

if($env -ne "prod" -and $env -ne "dev"){
    Set-Location $location
        Write-Error "Paremeter env must be either prod or dev" -ErrorAction Stop
}

$configFilePath = ".\deploy\config.ps1"
Write-Verbose "ConfigFilePath: $configFilePath"
. $configFilePath

$deploymentDirectoryPath = Resolve-Path "..\$deploymentDirectoryName"
Write-Verbose "deploymentDirectoryPath: $deploymentDirectoryPath"
Set-Location $deploymentDirectoryPath

[console]::ForegroundColor = "Red"
Write-Host "Before we contiune, update 'publicpath' in webpack.config.js!"
Write-Host "hit enter, edit, save and close the window to continue"
PAUSE #
$webpackConfigFilePath = "$deploymentDirectoryPath\webpack.config.js"
Start-Process -Wait $editorPath $webpackConfigFilePath

# TODO Replace yarn with npm
yarn add webpack@3
yarn add webpack-cli@1

[console]::ForegroundColor = "Magenta"
Write-Host "pack the assets..."
If($env -eq 'dev')
{
    .\node_modules\.bin\webpack -d --verbose
}elseif ($env -eq 'prod') {
    .\node_modules\.bin\webpack -p
}

Set-Location $location
Write-Verbose "...DONE!"
