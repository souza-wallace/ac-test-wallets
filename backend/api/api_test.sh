#!/bin/bash

# API Test Script - Wallet
# Base URL
BASE_URL="http://127.0.0.1:8001/api"

echo "Starting API Tests..."
echo "=========================="

# 1. Create User
echo "1. Creating new user..."
curl -X POST "$BASE_URL/users" \
  -F "name=Test User" \
  -F "email=test@example.com" \
  -F "password=123456" \
  -w "\nStatus: %{http_code}\n\n"

# 2. Login
echo "🔐 2. Login..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
  -F "email=test@example.com" \
  -F "password=123456")

echo "$LOGIN_RESPONSE"

# Extract token (assuming jq is available)
TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.data // empty')

if [ -z "$TOKEN" ]; then
  echo "Failed to get token. Using example token..."
else
  echo "✅ Token obtained: ${TOKEN:0:50}..."
fi

echo -e "\n"

# 3. Get User Profile
echo "3. Getting user profile..."
curl -X GET "$BASE_URL/users/1" \
  -H "Authorization: Bearer $TOKEN" \
  -w "\nStatus: %{http_code}\n\n"

# 4. Deposit
echo "4. Making deposit..."
curl -X POST "$BASE_URL/wallet/deposit" \
  -H "Authorization: Bearer $TOKEN" \
  -F "amount=500.00" \
  -w "\nStatus: %{http_code}\n\n"

# 5. Transfer
echo "5. Making transfer..."
curl -X POST "$BASE_URL/wallet/transfer" \
  -H "Authorization: Bearer $TOKEN" \
  -F "email=test@example.com" \
  -F "amount=50.00" \
  -F "description=Test transfer" \
  -w "\nStatus: %{http_code}\n\n"

# 6. Get Transactions
echo "6. Getting transactions..."
curl -X GET "$BASE_URL/wallet/transactions?page=1&per_page=10" \
  -H "Authorization: Bearer $TOKEN" \
  -w "\nStatus: %{http_code}\n\n"

# 7. Reverse Transaction (using transaction ID 2 as example)
echo "7. Reversing transaction..."
curl -X POST "$BASE_URL/wallet/transactions/2/reverse" \
  -H "Authorization: Bearer $TOKEN" \
  -w "\nStatus: %{http_code}\n\n"

echo "API Tests completed!"
echo "=========================="