<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    $searchType = $_GET['type'] ?? 'hash'; // 'hash' or 'farmer'
    $searchValue = $_GET['value'] ?? '';
    
    if (!$searchValue) {
        jsonResponse(['error' => 'Search value required'], 400);
    }
    
    $pdo = getDbConnection();
    
    if ($searchType === 'hash') {
        // Search by transaction hash
        $stmt = $pdo->prepare("
            SELECT h.*, u.username, u.wallet_address 
            FROM harvests h 
            JOIN users u ON h.user_id = u.id 
            WHERE h.tx_hash = ? OR h.image_hash = ?
        ");
        $stmt->execute([$searchValue, $searchValue]);
    } else {
        // Search by farmer address
        $stmt = $pdo->prepare("
            SELECT h.*, u.username, u.wallet_address 
            FROM harvests h 
            JOIN users u ON h.user_id = u.id 
            WHERE u.wallet_address = ?
            ORDER BY h.created_at DESC
        ");
        $stmt->execute([$searchValue]);
    }
    
    $results = $stmt->fetchAll();
    
    if (empty($results)) {
        jsonResponse(['error' => 'No records found'], 404);
    }
    
    // Format results
    $formattedResults = array_map(function($row) {
        $aiData = json_decode($row['ai_data'], true);
        return [
            'id' => $row['id'],
            'farmer_address' => $row['wallet_address'],
            'farmer_username' => $row['username'],
            'crop_type' => $row['crop_type'],
            'image_hash' => $row['image_hash'],
            'tx_hash' => $row['tx_hash'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'ai_data' => $aiData,
            'verification_url' => BASE_URL . '/verify.php?hash=' . $row['tx_hash']
        ];
    }, $results);
    
    jsonResponse([
        'success' => true,
        'count' => count($formattedResults),
        'records' => $formattedResults
    ]);
    
} catch (Exception $e) {
    if (DEBUG_MODE) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
    jsonResponse(['error' => 'Internal server error'], 500);
}
?>