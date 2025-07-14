<?php
session_start();

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่า email และ password จากฟอร์ม
$id = $_POST['ID'];
$password = $_POST['password']; // plaintext จากฟอร์ม

// ตรวจสอบว่ามี email นี้ในระบบหรือไม่
$sql = "SELECT * FROM test WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

// ถ้ามีผู้ใช้งานนี้
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    // ตรวจสอบรหัสผ่านด้วย password_verify()
    if (password_verify($password, $row['password'])) {
        // รหัสผ่านถูกต้อง → เข้าสู่ระบบ
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_email'] = $row['email'];
        echo "✅ เข้าสู่ระบบสำเร็จ ยินดีต้อนรับ " . $row['name'];
        exit();
    } else {
        echo "❌ รหัสผ่านไม่ถูกต้อง";
    }
} else {
    echo "❌ ไม่พบผู้ใช้นี้ในระบบ";
}

$stmt->close();
$conn->close();
?>
