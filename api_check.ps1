$ErrorActionPreference = "Stop"
$baseUrl = "http://127.0.0.1:8000/api"
$baseHeaders = @{ "Accept" = "application/json" }

function Test-Endpoint {
    param (
        [string]$Method,
        [string]$Path,
        [hashtable]$Headers,
        [object]$Body = $null,
        [string]$Description
    )
    Write-Host "`n--- $Description [$Method $Path] ---" -ForegroundColor Cyan
    try {
        $params = @{
            Uri = "$baseUrl$Path"
            Method = $Method
            Headers = $Headers
            ContentType = "application/json"
        }
        if ($Body) { $params.Body = ($Body | ConvertTo-Json -Depth 5) }
        
        $response = Invoke-RestMethod @params
        Write-Host "Success!" -ForegroundColor Green
        # Convert to JSON for pretty printing partial output
        echo $response | ConvertTo-Json -Depth 2 | Select-Object -First 20
        return $response
    } catch {
        Write-Host "Failed!" -ForegroundColor Red
        Write-Host $_.Exception.Message
        if ($_.Exception.Response) {
             $stream = $_.Exception.Response.GetResponseStream()
             $reader = New-Object System.IO.StreamReader($stream)
             Write-Host $reader.ReadToEnd()
        }
        return $null
    }
}

# 1. Register
$rand = Get-Random
$email = "user$rand@example.com"
$registerBody = @{
    name = "Test User $rand"
    email = $email
    password = "password123"
    password_confirmation = "password123"
    role = "resident"
}

$authData = Test-Endpoint -Method "Post" -Path "/register" -Headers $baseHeaders -Body $registerBody -Description "Registering New User"

if (-not $authData) { exit }

$token = $authData.access_token
$authHeaders = $baseHeaders.Clone()
$authHeaders.Add("Authorization", "Bearer $token")
Write-Host "Got Token: $token" -ForegroundColor Yellow

# 2. Get User
Test-Endpoint -Method "Get" -Path "/user" -Headers $authHeaders -Description "Getting User Profile"

# 3. Create Invitation
$inviteBody = @{ expiration_hours = 48 }
$invite = Test-Endpoint -Method "Post" -Path "/invitations" -Headers $authHeaders -Body $inviteBody -Description "Creating Invitation"

# 4. List Invitations
Test-Endpoint -Method "Get" -Path "/invitations" -Headers $authHeaders -Description "Listing Invitations"

# 5. Validate QR (if invite created)
if ($invite) {
    $validateBody = @{ qr_code = $invite.qr_code; mark_used = $false }
    Test-Endpoint -Method "Post" -Path "/invitations/validate" -Headers $authHeaders -Body $validateBody -Description "Validating QR Code"
}

# 6. Marketplace List
Test-Endpoint -Method "Get" -Path "/marketplace" -Headers $authHeaders -Description "Listing Marketplace Products"

# 7. Create Product (Skipping file upload for simple JSON test, verifying validation or success if nullable)
# Note: Real endpoint needs multipart/form-data. Invoke-RestMethod is tricky with that.
# We will skip Create Product for this script unless we want to use curl.exe specifically for this one.
# Let's try listing Amenities instead.

# 8. List Amenities
Test-Endpoint -Method "Get" -Path "/amenities" -Headers $authHeaders -Description "Listing Amenities"

# 9. Create Gate Access
$accessBody = @{ guest_name = "Uber Driver"; entry_date = (Get-Date).ToString("yyyy-MM-dd HH:mm:ss") }
Test-Endpoint -Method "Post" -Path "/gate-access" -Headers $authHeaders -Body $accessBody -Description "Registering Gate Access"

Write-Host "`nTest Script Completed."
