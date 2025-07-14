<?php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูล id และ gender จากตาราง sex
$sql = "SELECT id, gender FROM sex";
$result = $conn->query($sql);

$gender = array();
while ($row = $result->fetch_assoc()) {
    $gender[] = $row;
}

// ส่งข้อมูล JSON กลับไปยัง client
header('Content-Type: application/json');
echo json_encode($gender);

// ปิดการเชื่อมต่อ
$conn->close();
?>
