<?php
// ไม่มีช่องว่างก่อน <?php

// 1. เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าจากฟอร์ม
$name = $_POST['name'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$password = $_POST['password'];
$department = $_POST['department'];

// เข้ารหัสรหัสผ่านด้วย sha1
$hashedPassword = sha1($password);

// เตรียม SQL
$sql = "INSERT INTO test (name, lastname, email, password, department) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $name, $lastname, $email, $hashedPassword, $department);

// ถ้าบันทึกสำเร็จ → redirect
if ($stmt->execute()) {
   echo "<script>window.location.href = '../login.html';</script>";
exit();

} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
