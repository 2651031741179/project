<?php
// 1. เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root"; // ปรับตามที่ตั้งไว้
$password = ""; // รหัสผ่านของคุณ
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. รับค่าจาก form
$name = $_POST['name'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$password = $_POST['password'];  // รหัสผ่านที่กรอกจากฟอร์ม
$department = $_POST['department'];

// เข้ารหัสรหัสผ่าน
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// 3. เตรียมคำสั่ง SQL (ใช้ Prepared Statement เพื่อป้องกัน SQL Injection)
$sql = "INSERT INTO test (name, lastname, email, password, department) VALUES ( ?, ?, ?, ? ,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $name, $lastname, $email, $password, $department);

// 4. รันคำสั่ง SQL
if ($stmt->execute()) {
 echo "<script>window.location.href = '../login.html';</script>";
} else {
    echo "Error: " . $stmt->error;
}


$stmt->close();
$conn->close();
?>
