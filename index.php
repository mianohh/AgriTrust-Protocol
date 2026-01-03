<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content="AgriTrust - Secure farmer verification platform">
    <title>AgriTrust - Farmer Verification Platform</title>
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
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <header class="text-center mb-12">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent mb-4">
                AgriTrust Protocol
            </h1>
            <p class="text-gray-300 text-lg mb-6">Secure Farmer Verification Platform</p>
            <a href="./verify.php" class="inline-block bg-cyan-600 hover:bg-cyan-700 px-6 py-3 rounded-lg font-semibold">
                🔍 Verify Harvest Records
            </a>
        </header>

        <!-- Main Content -->
        <div class="max-w-md mx-auto">
            <div class="bg-gray-800 rounded-lg p-8 shadow-xl border border-gray-700">
                <h2 class="text-2xl font-semibold mb-6 text-center">Connect Your Wallet</h2>
                
                <!-- Wallet Connection -->
                <div id="wallet-section">
                    <button id="connect-wallet" class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 mb-4">
                        🚀 Demo Wallet Connection
                    </button>
                    
                    <div id="wallet-info" class="hidden">
                        <div class="bg-gray-700 rounded-lg p-4 mb-4">
                            <p class="text-sm text-gray-300 mb-2">Connected Wallet:</p>
                            <p id="wallet-address" class="font-mono text-sm break-all"></p>
                        </div>
                        <button id="enter-dashboard" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300">
                            Enter Dashboard
                        </button>
                    </div>
                </div>

                <!-- Demo Login -->
                <div class="mt-6 pt-6 border-t border-gray-600">
                    <p class="text-center text-gray-400 mb-4">Or try the demo</p>
                    <form id="demo-login" class="space-y-4">
                        <input type="text" id="username" placeholder="Username (try: demo_farmer)" 
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-cyan-500">
                        <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300">
                            Demo Login
                        </button>
                    </form>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-8 grid grid-cols-1 gap-4">
                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                    <h3 class="font-semibold text-cyan-400 mb-2">📸 Harvest Verification</h3>
                    <p class="text-sm text-gray-300">Upload photos of your harvest for AI-powered verification</p>
                </div>
                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                    <h3 class="font-semibold text-cyan-400 mb-2">🔗 Blockchain Records</h3>
                    <p class="text-sm text-gray-300">Immutable reputation records on the blockchain</p>
                </div>
                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                    <h3 class="font-semibold text-cyan-400 mb-2">💰 Credit Building</h3>
                    <p class="text-sm text-gray-300">Build verifiable financial credit history</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.ethers.io/lib/ethers-5.7.2.umd.min.js"></script>
    <script>
        // Mock wallet connection for demo
        document.getElementById('connect-wallet').addEventListener('click', async () => {
            // Simulate wallet connection delay
            document.getElementById('connect-wallet').textContent = 'Connecting...';
            document.getElementById('connect-wallet').disabled = true;
            
            setTimeout(() => {
                // Mock wallet address
                const mockAddress = '0x742d35Cc6634C0532925a3b8D0C9964De7C0C0C0';
                
                document.getElementById('wallet-address').textContent = mockAddress;
                document.getElementById('wallet-info').classList.remove('hidden');
                document.getElementById('connect-wallet').style.display = 'none';
                
                localStorage.setItem('wallet_address', mockAddress);
                localStorage.setItem('mock_wallet', 'true');
            }, 1500);
        });

        // Enter dashboard
        document.getElementById('enter-dashboard').addEventListener('click', () => {
            const walletAddress = localStorage.getItem('wallet_address');
            if (walletAddress) {
                window.location.href = './dashboard.php?wallet=' + encodeURIComponent(walletAddress);
            } else {
                window.location.href = './dashboard.php';
            }
        });

        // Demo login
        document.getElementById('demo-login').addEventListener('submit', (e) => {
            e.preventDefault();
            const username = document.getElementById('username').value;
            if (username) {
                localStorage.setItem('demo_user', username);
                window.location.href = './dashboard.php?demo=' + encodeURIComponent(username);
            }
        });

        // Check if already connected (mock)
        window.addEventListener('load', async () => {
            const mockWallet = localStorage.getItem('mock_wallet');
            const address = localStorage.getItem('wallet_address');
            
            if (mockWallet && address) {
                document.getElementById('wallet-address').textContent = address;
                document.getElementById('wallet-info').classList.remove('hidden');
                document.getElementById('connect-wallet').style.display = 'none';
            }
        });
    </script>
</body>
</html>