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
$deploymentDirectoryPath = Get-Location
Write-Verbose "DeploymentDirectoryPath: $deploymentDirectoryPath"

[console]::ForegroundColor = "Red"
Write-Verbose "Before we contiune, update 'publicpath' in webpack.config.js!"
Write-Verbose "hit enter, edit, save and close the window to continue"
PAUSE #
$webpackConfigFilePath = "$deploymentDirectoryPath\webpack.config.js"
Start-Process -Wait $editorPath $webpackConfigFilePath
[console]::ForegroundColor = "Magenta"
Write-Verbose "pack the assets..."
.\node_modules\.bin\webpack

cd $location
Write-Verbose "...DONE!"
