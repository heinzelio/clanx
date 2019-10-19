param(
    [Parameter(Mandatory=$true)]
    [string]$env
)

$location = Get-Location
if (-not(Test-Path -Path "./deploy")){
    cd ..
    if (-not(Test-Path "./deploy")){
        cd $location
        Write-Error "Directory /deploy does not exist. Please cd into the root of the project." -ErrorAction Stop
    }
}

if($env -ne "prod" -and $env -ne "dev"){
        cd $location
        Write-Error "Paremeter env must be either prod or dev" -ErrorAction Stop
}

$configFilePath = ".\deploy\config.ps1"
Write-Verbose "ConfigFilePath: $configFilePath"
. $configFilePath

$deploymentDirectoryPath = Resolve-Path "..\$deploymentDirectoryName"
Write-Verbose "deploymentDirectoryPath: $deploymentDirectoryPath"

Write-Verbose "cleanup deploymentDirectoryPath"
Remove-Item -Force -Recurse $deploymentDirectoryPath -ErrorAction SilentlyContinue

[console]::ForegroundColor = "Green"
Write-Verbose "Load project from github..."
git clone $githubUrl $deploymentDirectoryPath

cd $deploymentDirectoryPath
If($env -eq 'prod'){
    Write-Verbose "checkout latest version from master branch"
    git checkout master
    $latest = git describe --tags
    git checkout tags/$latest
    Write-Verbose "Latest version is $latest"
}
Else
{
    Write-Verbose "checkout latest commit from dev branch"
    git checkout dev
}

cd $location


Write-Verbose "...DONE!"
