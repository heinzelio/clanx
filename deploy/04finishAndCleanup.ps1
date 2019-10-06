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

cd $projectPath\..\$deploymentDirectory
$deploymentDirectoryPath = Get-Location
Write-Verbose "DeploymentDirectoryPath: $deploymentDirectoryPath"

[console]::ForegroundColor = "Blue"
Write-Verbose "warm up the cache..."
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
Remove-Item -Force package-lock.json
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
