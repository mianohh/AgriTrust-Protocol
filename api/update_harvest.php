<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['harvest_id']) || !isset($input['tx_hash'])) {
        jsonResponse(['error' => 'Missing required fields: harvest_id, tx_hash'], 400);
    }
    
    $harvestId = (int)$input['harvest_id'];
    $txHash = $input['tx_hash'];
    
    // Update harvest record with transaction hash
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare("
        UPDATE harvests 
        SET tx_hash = ?, status = 'minted', updated_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    
    $stmt->execute([$txHash, $harvestId]);
    
    if ($stmt->rowCount() === 0) {
        jsonResponse(['error' => 'Harvest record not found'], 404);
    }
    
    jsonResponse([
        'success' => true,
        'message' => 'Harvest record updated with transaction hash'
    ]);
    
} catch (Exception $e) {
    if (DEBUG_MODE) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
    jsonResponse(['error' => 'Internal server error'], 500);
}
?>