[console]::ForegroundColor = "Green"
Write-Host "Load project from github"
Remove-Item -Force -Recurse $targetAbs\clanx.deploy -ErrorAction SilentlyContinue
git clone --depth 1 -b $branch https://github.com/chriglburri/clanx clanx.deploy
cd  $targetAbs\clanx.deploy
