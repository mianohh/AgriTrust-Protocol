// AgriTrust Protocol - Web3 Manager (Mock Version for Demo)
class Web3Manager {
    constructor() {
        this.mockMode = true;
        this.mockAddress = '0x742d35Cc6634C0532925a3b8D0C9964De7C0C0C0';
    }

    async connectWallet() {
        // Mock wallet connection
        return this.mockAddress;
    }

    async switchToTestnet() {
        // Mock network switch
        return true;
    }

    async mintHarvestRecord(harvestData) {
        try {
            // Mock blockchain transaction
            const mockTxHash = '0x' + Math.random().toString(16).substr(2, 64);
            
            // Simulate network delay
            await new Promise(resolve => setTimeout(resolve, 2000));
            
            return mockTxHash;
        } catch (error) {
            throw new Error('Mock minting failed: ' + error.message);
        }
    }

    async getHarvestRecords(address) {
        // Mock harvest records
        return [
            {
                txHash: '0x1234567890abcdef1234567890abcdef12345678',
                crop: 'Maize',
                timestamp: Date.now() - 86400000,
                value: '$1200'
            }
        ];
    }

    formatAddress(address) {
        if (!address) return '';
        return address.substring(0, 6) + '...' + address.substring(38);
    }

    async getBalance() {
        // Mock balance
        return '2.5';
    }

    async getNetworkInfo() {
        // Mock network info
        return {
            name: 'Celo Alfajores (Demo)',
            chainId: 44787
        };
    }
}

// Initialize Web3Manager
document.addEventListener('DOMContentLoaded', () => {
    window.web3Manager = new Web3Manager();
});