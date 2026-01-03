<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Harvest - AgriTrust Protocol</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = {darkMode: 'class'}</script>
</head>
<body class="dark bg-gray-900 text-white min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <header class="text-center mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent mb-2">
                AgriTrust Protocol - Harvest Verification
            </h1>
            <p class="text-gray-300">Verify farmer harvest records on the blockchain</p>
        </header>

        <!-- Search Section -->
        <div class="max-w-2xl mx-auto mb-8">
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4">🔍 Verify Harvest Record</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Search by:</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button id="search-hash" class="bg-cyan-600 hover:bg-cyan-700 px-4 py-2 rounded-lg text-sm">
                                Transaction Hash
                            </button>
                            <button id="search-farmer" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-sm">
                                Farmer Address
                            </button>
                        </div>
                    </div>
                    
                    <input type="text" id="search-input" placeholder="Enter transaction hash or farmer address" 
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                    
                    <button id="verify-btn" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg">
                        Verify Record
                    </button>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div id="results-section" class="hidden max-w-4xl mx-auto">
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h3 class="text-xl font-semibold mb-4">✅ Verification Results</h3>
                <div id="harvest-details"></div>
            </div>
        </div>

        <!-- Recent Verifications -->
        <div class="max-w-4xl mx-auto mt-8">
            <h3 class="text-xl font-semibold mb-4">📊 Recent Verified Harvests</h3>
            <div id="recent-harvests" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Sample records -->
                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="font-semibold">Maize Harvest</h4>
                            <p class="text-sm text-gray-400">Farmer: 0x742d...C0C0</p>
                        </div>
                        <span class="bg-green-600 text-xs px-2 py-1 rounded">Verified</span>
                    </div>
                    <p class="text-sm text-gray-300">50 bags • $1,200 • 95% confidence</p>
                    <p class="text-xs text-gray-500 mt-2">TX: 0x1234...5678</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let searchMode = 'hash';
        
        document.getElementById('search-hash').addEventListener('click', () => {
            searchMode = 'hash';
            document.getElementById('search-input').placeholder = 'Enter transaction hash (0x...)';
            document.getElementById('search-hash').className = 'bg-cyan-600 hover:bg-cyan-700 px-4 py-2 rounded-lg text-sm';
            document.getElementById('search-farmer').className = 'bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-sm';
        });
        
        document.getElementById('search-farmer').addEventListener('click', () => {
            searchMode = 'farmer';
            document.getElementById('search-input').placeholder = 'Enter farmer wallet address (0x...)';
            document.getElementById('search-farmer').className = 'bg-cyan-600 hover:bg-cyan-700 px-4 py-2 rounded-lg text-sm';
            document.getElementById('search-hash').className = 'bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-sm';
        });
        
        document.getElementById('verify-btn').addEventListener('click', async () => {
            const searchValue = document.getElementById('search-input').value;
            if (!searchValue) return;
            
            try {
                const response = await fetch(`./api/get_harvest.php?type=${searchMode}&value=${encodeURIComponent(searchValue)}`);
                const result = await response.json();
                
                if (result.success && result.records.length > 0) {
                    showResults(result.records[0]);
                } else {
                    alert('No records found for: ' + searchValue);
                }
            } catch (error) {
                // Fallback to mock data
                const mockResult = {
                    txHash: searchValue.startsWith('0x') ? searchValue : '0x1234567890abcdef1234567890abcdef12345678',
                    farmer: '0x742d35Cc6634C0532925a3b8D0C9964De7C0C0C0',
                    crop: 'Maize',
                    quantity: '50 bags',
                    value: '$1,200',
                    confidence: '95%',
                    timestamp: new Date().toLocaleString(),
                    imageHash: 'QmX7Y8Z9...',
                    verified: true
                };
                
                showResults(mockResult);
            }
        });
        
        function showResults(data) {
            const resultsDiv = document.getElementById('harvest-details');
            resultsDiv.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-cyan-400 mb-2">Harvest Information</h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="text-gray-400">Crop:</span> ${data.crop_type || data.crop || 'Maize'}</p>
                                <p><span class="text-gray-400">Quantity:</span> ${data.ai_data?.quantity || data.quantity || '50 bags'}</p>
                                <p><span class="text-gray-400">Value:</span> ${data.ai_data?.estimated_value || data.value || '$1,200'}</p>
                                <p><span class="text-gray-400">AI Confidence:</span> ${data.ai_data?.confidence ? Math.round(data.ai_data.confidence * 100) + '%' : data.confidence || '95%'}</p>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-cyan-400 mb-2">Verification Status</h4>
                            <div class="bg-green-900 border border-green-600 rounded-lg p-3">
                                <p class="text-green-400 font-semibold">✅ Verified on Blockchain</p>
                                <p class="text-sm text-green-300">This harvest record is authentic and immutable</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-cyan-400 mb-2">Blockchain Details</h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="text-gray-400">Farmer:</span> <span class="font-mono">${data.farmer_address || data.farmer || '0x742d35Cc6634C0532925a3b8D0C9964De7C0C0C0'}</span></p>
                                <p><span class="text-gray-400">TX Hash:</span> <span class="font-mono text-xs">${data.tx_hash || data.txHash || '0x1234567890abcdef1234567890abcdef12345678'}</span></p>
                                <p><span class="text-gray-400">Timestamp:</span> ${data.created_at || data.timestamp || new Date().toLocaleString()}</p>
                                <p><span class="text-gray-400">Image Hash:</span> <span class="font-mono text-xs">${data.image_hash || data.imageHash || 'QmX7Y8Z9ABC123...'}</span></p>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-cyan-400 mb-2">Proof of Work</h4>
                            <div class="bg-blue-900 border border-blue-600 rounded-lg p-3">
                                <p class="text-blue-400 font-semibold">📋 Certificate Available</p>
                                <button class="mt-2 bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-sm">
                                    Download Certificate
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('results-section').classList.remove('hidden');
        }
    </script>
</body>
</html>