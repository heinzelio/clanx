[console]::ForegroundColor = "Yellow"
Write-Host "install all php dependencies. This takes a while..."
composer install --no-dev --optimize-autoloader

[console]::ForegroundColor = "Cyan"
Write-Host "install all js dependencies and other assets..."
yarn install --production
yarn add webpack@3
