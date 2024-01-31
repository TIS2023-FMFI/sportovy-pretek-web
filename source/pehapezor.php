<?php
include('funkcie.php');

$body = file_get_contents('php://input');
$query = json_decode($body)->query;
$conn = new MyDB();
if (preg_match('/^SELECT\s+.*\s+FROM\s.*$/is', $query)) {
    $result = $conn->query($query);
    $results = array();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $results[] = $row;
    }
    echo json_encode($results, JSON_PRESERVE_ZERO_FRACTION);
}
else {
    $result = $conn->exec($query);
    error_log($query);
    echo json_encode(['success' => $result]);
}