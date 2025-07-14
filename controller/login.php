<?php
session_start(); // เริ่มต้น session

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test"; // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าจากฟอร์ม
$id = $_POST['ID'];
$password = $_POST['password']; // ชื่อที่กรอกจากฟอร์ม

// เตรียมคำสั่ง SQL (ใช้ Prepared Statement เพื่อป้องกัน SQL Injection)
$sql = "SELECT * FROM test WHERE ID = ? AND name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $id, $name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ถ้ามีผลลัพธ์ แสดงว่า ID และ name นี้มีอยู่ในฐานข้อมูล
    $row = $result->fetch_assoc();

    // เช็คการล็อกอินสำเร็จ
    $_SESSION['user_id'] = $row['ID'];
    $_SESSION['user_name'] = $row['name'];
    $_SESSION['user_lastname'] = $row['lastname'];
    $_SESSION['user_email'] = $row['email']; // สามารถใช้ข้อมูลนี้ในภายหลัง
    echo "welcome!"; // เปลี่ยนเส้นทางไปที่หน้าต้อนรับ
    exit();
} else {
    // ถ้าไม่พบ ID และ name นี้ในฐานข้อมูล
    echo "Incorrect ID or Name!";
}

$stmt->close();
$conn->close();
?>
