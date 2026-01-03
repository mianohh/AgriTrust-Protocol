<?php
require_once 'config.php';

// Check for wallet or demo parameters
$walletAddress = $_GET['wallet'] ?? null;
$demoUser = $_GET['demo'] ?? null;

// If no parameters, allow demo access
if (!$walletAddress && !$demoUser) {
    $demoUser = 'demo_farmer';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriTrust Protocol Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'cyber': {
                            50: '#f0f9ff',
                            500: '#06b6d4',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="dark bg-gray-900 text-white min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
                AgriTrust Protocol Dashboard
            </h1>
            <div class="flex gap-2">
                <a href="./verify.php" class="bg-cyan-600 hover:bg-cyan-700 px-4 py-2 rounded-lg text-sm">
                    🔍 Verify Records
                </a>
                <button id="logout-btn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-sm">
                    Logout
                </button>
            </div>
        </header>

        <!-- User Info -->
        <div id="user-info" class="bg-gray-800 rounded-lg p-4 mb-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Connected as:</p>
                    <p id="user-display" class="font-mono text-sm"></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-400">Credit Score:</p>
                    <p class="text-xl font-bold text-green-400">750</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Upload Section -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4">📸 Verify New Harvest</h2>
                
                <!-- Camera/Upload -->
                <div id="camera-section" class="mb-4">
                    <input type="file" id="image-input" accept="image/*" capture="environment" 
                           class="hidden">
                    <button id="camera-btn" class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 mb-4">
                        📷 Take Photo / Upload Image
                    </button>
                    
                    <!-- Preview -->
                    <div id="image-preview" class="hidden mb-4">
                        <img id="preview-img" class="w-full h-48 object-cover rounded-lg border border-gray-600">
                        <p class="text-sm text-gray-400 mt-2">Image compressed to: <span id="file-size"></span></p>
                    </div>
                    
                    <!-- Harvest Details Form -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Crop Type *</label>
                        <select id="crop-type" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white mb-3" required>
                            <option value="">Select Crop Type</option>
                            <option value="Maize">Maize</option>
                            <option value="Rice">Rice</option>
                            <option value="Cassava">Cassava</option>
                            <option value="Yam">Yam</option>
                            <option value="Beans">Beans</option>
                            <option value="Tomatoes">Tomatoes</option>
                        </select>
                        
                        <label class="block text-sm font-medium text-gray-300 mb-2">Estimated Quantity</label>
                        <input type="number" id="quantity" placeholder="e.g. 50" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white mb-3">
                        <select id="quantity-unit" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white mb-3">
                            <option value="bags">Bags</option>
                            <option value="kg">Kilograms</option>
                            <option value="tons">Tons</option>
                            <option value="pieces">Pieces</option>
                        </select>
                        
                        <label class="block text-sm font-medium text-gray-300 mb-2">Expected Value (USD)</label>
                        <input type="number" id="expected-value" placeholder="e.g. 1200" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white mb-4">
                    </div>
                    
                    <button id="verify-btn" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 disabled:bg-gray-600 disabled:cursor-not-allowed" disabled>
                        🔍 Verify Harvest
                    </button>
                </div>

                <!-- Loading -->
                <div id="loading" class="hidden text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-cyan-500 mx-auto mb-4"></div>
                    <p>AI is analyzing your harvest...</p>
                </div>

                <!-- Results -->
                <div id="verification-result" class="hidden">
                    <div class="bg-green-900 border border-green-600 rounded-lg p-4 mb-4">
                        <h3 class="font-semibold text-green-400 mb-2">✅ Verification Successful</h3>
                        <div id="ai-results"></div>
                    </div>
                    <button id="mint-btn" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300">
                        🔗 Mint on Blockchain
                    </button>
                </div>
            </div>

            <!-- Harvest History -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4">📊 Harvest History</h2>
                <div id="harvest-list" class="space-y-3">
                    <!-- Sample harvest record -->
                    <div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold">Maize Harvest</h3>
                                <p class="text-sm text-gray-400">50 bags • $1,200 value</p>
                            </div>
                            <span class="bg-green-600 text-xs px-2 py-1 rounded">Verified</span>
                        </div>
                        <p class="text-xs text-gray-500">Hash: sample_hash_123</p>
                        <p class="text-xs text-gray-500">2024-01-15 14:30</p>
                        <div class="mt-2 flex gap-2">
                            <button onclick="copyTxHash('0x1234567890abcdef1234567890abcdef12345678')" class="bg-cyan-600 hover:bg-cyan-700 px-3 py-1 rounded text-xs">
                                📋 Copy TX Hash
                            </button>
                            <button onclick="shareRecord('0x1234567890abcdef1234567890abcdef12345678')" class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-xs">
                                🔗 Share Record
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        <div id="status-message" class="fixed bottom-4 right-4 hidden">
            <div class="bg-gray-800 border border-gray-600 rounded-lg p-4 shadow-lg max-w-sm">
                <p id="status-text"></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.ethers.io/lib/ethers-5.7.2.umd.min.js"></script>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/web3-manager.js"></script>
</body>
</html>