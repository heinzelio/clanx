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

$deploymentDirectoryPath = "$projectPath\..\$deploymentDirectory"
Write-Verbose "DeploymentDirectoryPath: $deploymentDirectoryPath"

[console]::ForegroundColor = "Green"
Write-Verbose "Load project from github: branch $branch..."

Remove-Item -Force -Recurse $deploymentDirectoryPath -ErrorAction SilentlyContinue
git clone --depth 1 -b $branch $githubUrl $deploymentDirectory

Write-Verbose "...DONE!"
