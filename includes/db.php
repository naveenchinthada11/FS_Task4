<?php
require_once 'config.php';

// Create Database Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set Charset
$conn->set_charset("utf8");

// Function to Execute Query
function executeQuery($query, $params = [], $types = '') {
    global $conn;
    try {
        $stmt = $conn->prepare($query);
    } catch (Throwable $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }

    if (!$stmt) {
        return ['success' => false, 'error' => $conn->error];
    }
    
    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if ($stmt->execute()) {
        return ['success' => true, 'stmt' => $stmt];
    } else {
        return ['success' => false, 'error' => $stmt->error];
    }
}

// Function to Fetch Data
function fetchData($query, $params = [], $types = '') {
    $result = executeQuery($query, $params, $types);
    
    if (!$result['success']) {
        return [];
    }
    
    $stmt = $result['stmt'];
    $resultSet = $stmt->get_result();
    $data = [];
    
    while ($row = $resultSet->fetch_assoc()) {
        $data[] = $row;
    }
    
    $stmt->close();
    return $data;
}

// Function to Fetch Single Row
function fetchOne($query, $params = [], $types = '') {
    $result = fetchData($query, $params, $types);
    return !empty($result) ? $result[0] : null;
}

// Function to Insert/Update/Delete
function modifyData($query, $params = [], $types = '') {
    $result = executeQuery($query, $params, $types);
    
    if (!$result['success']) {
        return ['success' => false, 'error' => $result['error']];
    }
    
    $stmt = $result['stmt'];
    $affectedRows = $stmt->affected_rows;
    $insertId = $stmt->insert_id;
    $stmt->close();
    
    return [
        'success' => true,
        'affected_rows' => $affectedRows,
        'insert_id' => $insertId
    ];
}
?>
