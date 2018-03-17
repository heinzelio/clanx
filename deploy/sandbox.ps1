param(
    [string]$someParameter
)

Write-Host "Sandbox!"

if($someParameter){
    Write-Host $someParameter
}
