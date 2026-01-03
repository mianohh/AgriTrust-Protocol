# AgriTrust Protocol 🌾

> Decentralized farmer verification platform combining AI-powered harvest analysis with blockchain immutability to create verifiable agricultural credit records for smallholder farmers.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://mysql.com)

## 🚀 Live Demo

- **Platform**: [agritrustprotocol.42web.io](https://agritrustprotocol.42web.io/AgriTrust/)
- **Verification**: [Verify Harvest Records](https://agritrustprotocol.42web.io/AgriTrust/verify.php)

## 📋 Features

### Core Functionality
- 📸 **Mobile Harvest Capture** - Camera integration with image compression
- 🤖 **AI Verification** - Crop analysis and validation (mock with API-ready structure)
- 🔗 **Blockchain Minting** - Immutable records on Celo Alfajores testnet
- 🔍 **Public Verification** - Anyone can verify harvest authenticity
- 💰 **Credit Building** - Verifiable financial history for farmers

### Technical Features
- 🌐 **Zero Dependencies** - Pure PHP/JS, works on shared hosting
- 📱 **Mobile Optimized** - Progressive Web App with offline capabilities
- 🔒 **Security First** - Environment-based config, HTTPS enforcement
- ⚡ **Performance** - Client-side compression, optimized queries
- 🎨 **Modern UI** - Dark mode, responsive design with TailwindCSS

## 🛠️ Technology Stack

### Backend
- **PHP 7.4+** - Vanilla PHP for maximum compatibility
- **MySQL** - Relational database for harvest records
- **Environment Config** - Secure credential management

### Frontend
- **Vanilla JavaScript** - No frameworks, pure JS
- **TailwindCSS** - Utility-first CSS framework
- **Ethers.js v5** - Web3 blockchain interactions
- **Canvas API** - Image compression and processing

### Blockchain
- **Celo Alfajores** - Testnet for low-cost transactions
- **Self-Transactions** - No smart contracts needed
- **Hex Data Storage** - Harvest records in transaction data

## 🏗️ Architecture

```
┌─────────────┐    ┌──────────────┐    ┌─────────────┐
│   Farmer    │───▶│  AgriTrust   │───▶│ Blockchain  │
│   Mobile    │    │   Platform   │    │   Network   │
└─────────────┘    └──────────────┘    └─────────────┘
       │                   │                   │
       ▼                   ▼                   ▼
┌─────────────┐    ┌──────────────┐    ┌─────────────┐
│ Compressed  │    │   MySQL DB   │    │ Immutable   │
│ Image Data  │    │   Records    │    │  Records    │
└─────────────┘    └──────────────┘    └─────────────┘
```

## 🚀 Quick Start

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)
- Modern browser with camera support

### Installation

1. **Clone Repository**
```bash
git clone https://github.com/mianohh/AgriTrust-Protocol.git
cd AgriTrust-Protocol
```

2. **Database Setup**
```bash
# Import database schema
mysql -u username -p database_name < sql/schema.sql
```

3. **Environment Configuration**
```bash
# Copy environment template
cp .env.example .env

# Edit with your credentials
nano .env
```

4. **Configure Database**
```env
DB_HOST=your_db_host
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
DB_NAME=your_db_name
```

5. **Deploy Files**
```bash
# Upload to web server
# Ensure proper permissions
chmod 755 -R /path/to/webroot/
```

### Local Development

1. **Start PHP Server**
```bash
php -S localhost:8000
```

2. **Access Application**
- Main App: `http://localhost:8000/index.php`
- Verification: `http://localhost:8000/verify.php`

## 📁 Project Structure

```
AgriTrust/
├── 📄 index.php              # Landing page & wallet connection
├── 📄 dashboard.php          # Main farmer interface
├── 📄 verify.php             # Public verification system
├── 📄 config.php             # Database & environment config
├── 📄 .env                   # Environment variables
├── 📄 .htaccess              # Security & routing rules
├── 📁 api/
│   ├── verify_harvest.php    # Harvest verification endpoint
│   ├── update_harvest.php    # Transaction hash updates
│   └── get_harvest.php       # Verification data retrieval
├── 📁 core/
│   ├── AiVerifier.php        # AI analysis engine
│   └── EnvLoader.php         # Environment variable loader
├── 📁 assets/js/
│   ├── app.js                # Main application logic
│   └── web3-manager.js       # Blockchain interactions
└── 📁 sql/
    └── schema.sql            # Database structure
```

## 🔧 Configuration

### Environment Variables
```env
# Database
DB_HOST=sql306.infinityfree.com
DB_USERNAME=if0_40818497
DB_PASSWORD=your_password
DB_NAME=if0_40818497_agritrust

# Security
DEBUG_MODE=false
JWT_SECRET=your_jwt_secret
ENCRYPTION_KEY=your_32_char_key

# Domain
DOMAIN=yourdomain.com
BASE_URL=https://yourdomain.com
```

### Hosting Compatibility
- ✅ **InfinityFree** - Free shared hosting
- ✅ **cPanel Hosting** - Standard shared hosting
- ✅ **Render/Heroku** - Cloud PaaS platforms
- ✅ **VPS/Dedicated** - Full server control

## 🔐 Security Features

- **HTTPS Enforcement** - Required for camera/wallet access
- **Environment Variables** - Secure credential storage
- **SQL Injection Prevention** - Prepared statements
- **Input Validation** - Server-side data validation
- **CORS Protection** - Proper API access control
- **File Protection** - Sensitive files blocked via .htaccess

## 🌐 API Endpoints

### POST `/api/verify_harvest.php`
Verify farmer harvest submission
```json
{
  "image_data": "base64_encoded_image",
  "user_id": 1,
  "crop_type": "Maize",
  "farmer_quantity": "50",
  "quantity_unit": "bags",
  "farmer_value": "1200"
}
```

### GET `/api/get_harvest.php`
Retrieve harvest verification data
```
?type=hash&value=0x1234567890abcdef...
?type=farmer&value=0x742d35Cc6634C0532925a3b8D0C9964De7C0C0C0
```

### POST `/api/update_harvest.php`
Update harvest with blockchain transaction hash
```json
{
  "harvest_id": 123,
  "tx_hash": "0x1234567890abcdef..."
}
```

## 🚀 Deployment

### Shared Hosting (InfinityFree)
1. Upload files via File Manager
2. Import SQL via phpMyAdmin
3. Update `.env` with hosting credentials
4. Access via your domain

### Cloud Platforms (Render/Heroku)
1. Connect GitHub repository
2. Set environment variables in dashboard
3. Configure database connection
4. Deploy automatically

## 🧪 Testing

### Database Connection Test
```bash
# Access test script
https://yourdomain.com/test_db.php
```

### Mock Wallet Testing
- Demo wallet connection available
- No MetaMask required for testing
- Full workflow functional in demo mode

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **African Farmers** - The inspiration behind this platform
- **Web3 Community** - For decentralized technology advancement
- **Open Source** - Built with and for the community

## 📞 Support

- **Issues**: [GitHub Issues](https://github.com/mianohh/AgriTrust-Protocol/issues)
- **Discussions**: [GitHub Discussions](https://github.com/mianohh/AgriTrust-Protocol/discussions)
- **Email**: support@agritrustprotocol.com

---

**Built with ❤️ for African smallholder farmers**

*Empowering agricultural communities through blockchain technology and AI verification.*