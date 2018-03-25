param(
    [Parameter(Mandatory=$true)]
    [string]$env,
    [Parameter(Mandatory=$true)][string]$projectPath
)

$location = Get-Location
Write-Verbose "Location: $location"

$configFilePath = "$projectPath\deploy\config.ps1"
Write-Verbose "ConfigFilePath: $configFilePath"
. $configFilePath

$deploymentDirectoryPath = "$projectPath\..\$deploymentDirectory"
Write-Verbose "DeploymentDirectoryPath: $deploymentDirectoryPath"

[console]::ForegroundColor = "Green"
Write-Verbose "Load project from github..."

Remove-Item -Force -Recurse $deploymentDirectoryPath -ErrorAction SilentlyContinue
git clone $githubUrl $deploymentDirectory

cd $deploymentDirectoryPath
If($env -eq 'prod'){
    git checkout master
    $latest = git describe --tags
    git checkout tags/$latest
    Write-Verbose "Latest version is $latest"
}
Else
{
    git checkout dev
}

cd $location


Write-Verbose "...DONE!"
