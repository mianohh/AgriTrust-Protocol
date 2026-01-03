// AgriTrust Protocol - Main Application Logic
class AgriTrustApp {
    constructor() {
        this.currentUser = null;
        this.currentImageData = null;
        this.currentHarvestId = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadUserInfo();
    }

    setupEventListeners() {
        // Camera/Upload
        document.getElementById('camera-btn').addEventListener('click', () => {
            document.getElementById('image-input').click();
        });

        document.getElementById('image-input').addEventListener('change', (e) => {
            this.handleImageUpload(e.target.files[0]);
        });

        // Verification
        document.getElementById('verify-btn').addEventListener('click', () => {
            this.verifyHarvest();
        });

        // Minting
        document.getElementById('mint-btn').addEventListener('click', () => {
            this.mintOnBlockchain();
        });

        // Logout
        document.getElementById('logout-btn').addEventListener('click', () => {
            localStorage.clear();
            window.location.href = './index.php';
        });
    }

    loadUserInfo() {
        const walletAddress = localStorage.getItem('wallet_address');
        const demoUser = localStorage.getItem('demo_user');
        
        if (walletAddress) {
            this.currentUser = { type: 'wallet', address: walletAddress };
            document.getElementById('user-display').textContent = 
                walletAddress.substring(0, 6) + '...' + walletAddress.substring(38);
        } else if (demoUser) {
            this.currentUser = { type: 'demo', username: demoUser };
            document.getElementById('user-display').textContent = demoUser;
        } else {
            window.location.href = './index.php';
        }
    }

    async handleImageUpload(file) {
        if (!file) return;

        try {
            // Compress image for free hosting limits
            const compressedImage = await this.compressImage(file);
            this.currentImageData = compressedImage.data;
            
            // Show preview
            document.getElementById('preview-img').src = compressedImage.dataUrl;
            document.getElementById('file-size').textContent = 
                Math.round(compressedImage.data.length * 0.75 / 1024) + ' KB';
            document.getElementById('image-preview').classList.remove('hidden');
            
            // Enable verify button
            document.getElementById('verify-btn').disabled = false;
            
            this.showStatus('Image uploaded and compressed successfully', 'success');
        } catch (error) {
            this.showStatus('Error processing image: ' + error.message, 'error');
        }
    }

    compressImage(file) {
        return new Promise((resolve, reject) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();
            
            img.onload = () => {
                // Calculate new dimensions (max 800px width)
                const maxWidth = 800;
                const ratio = Math.min(maxWidth / img.width, maxWidth / img.height);
                canvas.width = img.width * ratio;
                canvas.height = img.height * ratio;
                
                // Draw and compress
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                
                // Convert to base64 with compression
                canvas.toBlob((blob) => {
                    const reader = new FileReader();
                    reader.onload = () => {
                        const dataUrl = reader.result;
                        const base64Data = dataUrl.split(',')[1];
                        resolve({
                            data: base64Data,
                            dataUrl: dataUrl
                        });
                    };
                    reader.readAsDataURL(blob);
                }, 'image/jpeg', 0.7); // 70% quality
            };
            
            img.onerror = () => reject(new Error('Failed to load image'));
            img.src = URL.createObjectURL(file);
        });
    }

    async verifyHarvest() {
        if (!this.currentImageData) {
            this.showStatus('Please upload an image first', 'error');
            return;
        }

        const cropType = document.getElementById('crop-type').value;
        const quantity = document.getElementById('quantity').value;
        const quantityUnit = document.getElementById('quantity-unit').value;
        const expectedValue = document.getElementById('expected-value').value;
        
        if (!cropType) {
            this.showStatus('Please select a crop type', 'error');
            return;
        }

        // Show loading
        document.getElementById('camera-section').classList.add('hidden');
        document.getElementById('loading').classList.remove('hidden');

        try {
            const response = await fetch('./api/verify_harvest.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    image_data: this.currentImageData,
                    user_id: 1,
                    crop_type: cropType,
                    farmer_quantity: quantity,
                    quantity_unit: quantityUnit,
                    farmer_value: expectedValue
                })
            });

            const result = await response.json();

            if (result.success) {
                this.currentHarvestId = result.harvest_id;
                this.showVerificationResult(result.ai_result, {
                    crop: cropType,
                    quantity: quantity + ' ' + quantityUnit,
                    value: expectedValue
                });
                this.showStatus('Harvest verified successfully!', 'success');
            } else {
                throw new Error(result.error || 'Verification failed');
            }
        } catch (error) {
            this.showStatus('Verification failed: ' + error.message, 'error');
            document.getElementById('camera-section').classList.remove('hidden');
        } finally {
            document.getElementById('loading').classList.add('hidden');
        }
    }

    showVerificationResult(aiResult, farmerData) {
        const resultsDiv = document.getElementById('ai-results');
        
        // Calculate verification status
        const cropMatch = aiResult.crop.toLowerCase() === farmerData.crop.toLowerCase();
        const quantityDiff = Math.abs(parseInt(aiResult.quantity) - parseInt(farmerData.quantity)) / parseInt(farmerData.quantity) * 100;
        const valueDiff = Math.abs(parseInt(aiResult.estimated_value.replace('$', '')) - parseInt(farmerData.value)) / parseInt(farmerData.value) * 100;
        
        resultsDiv.innerHTML = `
            <div class="space-y-4">
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div class="text-center">
                        <p class="text-gray-400">Source</p>
                        <p class="font-semibold">Farmer</p>
                        <p class="font-semibold">AI</p>
                        <p class="font-semibold">Status</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-400">Crop</p>
                        <p>${farmerData.crop}</p>
                        <p>${aiResult.crop}</p>
                        <p class="${cropMatch ? 'text-green-400' : 'text-yellow-400'}">${cropMatch ? '✓ Match' : '⚠ Different'}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-400">Quantity</p>
                        <p>${farmerData.quantity}</p>
                        <p>${aiResult.quantity}</p>
                        <p class="${quantityDiff < 20 ? 'text-green-400' : 'text-yellow-400'}">${quantityDiff < 20 ? '✓ Close' : '⚠ Different'}</p>
                    </div>
                </div>
                <div class="bg-gray-700 rounded p-3">
                    <p class="text-sm text-gray-300">AI Confidence: ${Math.round(aiResult.confidence * 100)}%</p>
                    <p class="text-sm text-gray-300">Verification Score: ${cropMatch && quantityDiff < 20 ? '95%' : '75%'}</p>
                </div>
            </div>
        `;
        
        document.getElementById('verification-result').classList.remove('hidden');
    }

    async mintOnBlockchain() {
        if (!this.currentHarvestId) {
            this.showStatus('No verified harvest to mint', 'error');
            return;
        }

        try {
            this.showStatus('Preparing blockchain transaction...', 'info');
            
            // Use Web3Manager to mint
            const txHash = await window.web3Manager.mintHarvestRecord({
                harvestId: this.currentHarvestId,
                crop: document.querySelector('#ai-results').textContent,
                timestamp: Date.now()
            });

            if (txHash) {
                // Update database with transaction hash
                await fetch('./api/update_harvest.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        harvest_id: this.currentHarvestId,
                        tx_hash: txHash
                    })
                });
                
                this.showStatus('Successfully minted on blockchain!', 'success');
                this.addToHarvestHistory(txHash);
                this.resetForm();
            }
        } catch (error) {
            this.showStatus('Minting failed: ' + error.message, 'error');
        }
    }

    addToHarvestHistory(txHash) {
        const historyList = document.getElementById('harvest-list');
        const newRecord = document.createElement('div');
        newRecord.className = 'bg-gray-700 rounded-lg p-4 border border-gray-600';
        newRecord.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="font-semibold">New Harvest</h3>
                    <p class="text-sm text-gray-400">Just verified</p>
                </div>
                <span class="bg-purple-600 text-xs px-2 py-1 rounded">Minted</span>
            </div>
            <p class="text-xs text-gray-500">TX: ${txHash.substring(0, 20)}...</p>
            <p class="text-xs text-gray-500">${new Date().toLocaleString()}</p>
            <div class="mt-2 flex gap-2">
                <button onclick="copyTxHash('${txHash}')" class="bg-cyan-600 hover:bg-cyan-700 px-3 py-1 rounded text-xs">
                    📋 Copy TX Hash
                </button>
                <button onclick="shareRecord('${txHash}')" class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-xs">
                    🔗 Share Record
                </button>
            </div>
        `;
        historyList.insertBefore(newRecord, historyList.firstChild);
    }

    resetForm() {
        this.currentImageData = null;
        this.currentHarvestId = null;
        document.getElementById('image-preview').classList.add('hidden');
        document.getElementById('verification-result').classList.add('hidden');
        document.getElementById('camera-section').classList.remove('hidden');
        document.getElementById('verify-btn').disabled = true;
        document.getElementById('crop-type').value = '';
        document.getElementById('quantity').value = '';
        document.getElementById('expected-value').value = '';
        document.getElementById('image-input').value = '';
    }

    showStatus(message, type = 'info') {
        const statusDiv = document.getElementById('status-message');
        const statusText = document.getElementById('status-text');
        
        statusText.textContent = message;
        statusDiv.classList.remove('hidden');
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            statusDiv.classList.add('hidden');
        }, 3000);
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.agriTrustApp = new AgriTrustApp();
});

// Global functions for copy/share
function copyTxHash(txHash) {
    navigator.clipboard.writeText(txHash).then(() => {
        showTempMessage('Transaction hash copied to clipboard!');
    }).catch(() => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = txHash;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showTempMessage('Transaction hash copied!');
    });
}

function shareRecord(txHash) {
    const shareUrl = `${window.location.origin}/AgriTrust/verify.php?hash=${txHash}`;
    
    if (navigator.share) {
        navigator.share({
            title: 'AgriTrust Harvest Verification',
            text: 'Verify my harvest record on the blockchain',
            url: shareUrl
        });
    } else {
        navigator.clipboard.writeText(shareUrl).then(() => {
            showTempMessage('Verification link copied to clipboard!');
        });
    }
}

function showTempMessage(message) {
    const msgDiv = document.createElement('div');
    msgDiv.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    msgDiv.textContent = message;
    document.body.appendChild(msgDiv);
    
    setTimeout(() => {
        document.body.removeChild(msgDiv);
    }, 3000);
}