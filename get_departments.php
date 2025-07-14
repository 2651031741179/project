<?php
// ตั้งค่าการเชื่อมต่อ
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลจากตารางที่ชื่อว่า "name ID"
$sql = "SELECT id, name FROM name ";
$result = $conn->query($sql);

$departments = array();
while ($row = $result->fetch_assoc()) {
    $departments[] = $row;
}

header('Content-Type: application/json');
echo json_encode($departments);

$conn->close();
?>
