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
cd $deploymentDirectoryPath

[console]::ForegroundColor = "Blue"
Write-Host "warm up the cache..."
php .\bin\console cache:warmup --env=prod

[console]::ForegroundColor = "Green"
Write-Host "remove dev files..."
Remove-Item -Force -Recurse .\.git
Remove-Item -Force -Recurse .\assets
Remove-Item -Force -Recurse .\node_modules
Remove-Item -Force -Recurse .\sql
Remove-Item -Force .env.dist
Remove-Item -Force .gitignore
#Keep composer.json. The Kernel.php needs it!
#Remove-Item -Force composer.json
Remove-Item -Force composer.lock
Remove-Item -Force mklink.bat
Remove-Item -Force package-lock.json -ErrorAction Ignore
Remove-Item -Force package.json
Remove-Item -Force phpunit.xml.dist
Remove-Item -Force README.md
Remove-Item -Force symfony.lock
Remove-Item -Force webpack.config.js
Remove-Item -Force yarn-error.log -ErrorAction SilentlyContinue
Remove-Item -Force yarn.lock

If($env -eq 'dev')
{
    #Check the page if it runs locally
    Start-Process -FilePath "http://localhost:81/clanx_deploy/info/"
}

cd $location
Write-Verbose "...DONE!"
