<?php
// AgriTrust Protocol - Harvest Verification API
require_once '../config.php';
require_once '../core/AiVerifier.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    jsonResponse(['status' => 'ok']);
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        jsonResponse(['error' => 'Invalid JSON input'], 400);
    }
    
    // Validate required fields
    if (!isset($input['image_data']) || !isset($input['user_id'])) {
        jsonResponse(['error' => 'Missing required fields: image_data, user_id'], 400);
    }
    
    $imageData = $input['image_data'];
    $userId = (int)$input['user_id'];
    $cropType = $input['crop_type'] ?? null;
    $farmerQuantity = $input['farmer_quantity'] ?? null;
    $quantityUnit = $input['quantity_unit'] ?? 'bags';
    $farmerValue = $input['farmer_value'] ?? null;
    
    // Remove data URL prefix if present
    if (strpos($imageData, 'data:image') === 0) {
        $imageData = explode(',', $imageData)[1];
    }
    
    // Verify image data
    $decodedImage = base64_decode($imageData);
    if (!$decodedImage) {
        jsonResponse(['error' => 'Invalid image data'], 400);
    }
    
    // Generate image hash
    $imageHash = AiVerifier::generateImageHash($decodedImage);
    
    // AI Verification
    $aiResult = AiVerifier::verifyHarvest($imageData, $cropType);
    
    if (!$aiResult['success']) {
        jsonResponse(['error' => 'AI verification failed'], 500);
    }
    
    // Add farmer input to AI result for comparison
    $aiResult['farmer_data'] = [
        'quantity' => $farmerQuantity,
        'unit' => $quantityUnit,
        'value' => $farmerValue
    ];
    
    // Validate database connection
    $pdo = getDbConnection();
    
    // Check if user exists, create if not
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    if (!$stmt->fetch()) {
        // Create demo user if doesn't exist
        $stmt = $pdo->prepare("INSERT INTO users (id, username, wallet_address) VALUES (?, 'demo_farmer', '0x742d35Cc6634C0532925a3b8D0C9964De7C0C0C0')");
        $stmt->execute([$userId]);
    }
    // Save harvest to database
    $stmt = $pdo->prepare("
        INSERT INTO harvests (user_id, crop_type, image_hash, ai_data, status) 
        VALUES (?, ?, ?, ?, 'verified')
    ");
    
    $stmt->execute([
        $userId,
        $aiResult['crop'],
        $imageHash,
        json_encode($aiResult)
    ]);
    
    $harvestId = $pdo->lastInsertId();
    
    // Return success response
    jsonResponse([
        'success' => true,
        'harvest_id' => $harvestId,
        'image_hash' => $imageHash,
        'ai_result' => $aiResult,
        'message' => 'Harvest verified successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Harvest verification error: ' . $e->getMessage());
    if (DEBUG_MODE) {
        jsonResponse(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
    }
    jsonResponse(['error' => 'Internal server error'], 500);
}
?>