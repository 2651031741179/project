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

$email = $_POST['email'];
$password = md5($_POST['password']); // ใช้ md5 ตามชื่อไฟล์ (แนะนำให้ใช้ hashing ที่ปลอดภัยกว่า เช่น bcrypt)

// คิวรีจากฐานข้อมูล
$sql = "SELECT * FROM test WHERE email = '$email' AND password = '$password'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    $_SESSION['user_id'] = $user['id']; // เก็บ id ใน session

    // เปลี่ยนเส้นทางไปหน้า device.php พร้อมส่ง id
    header("Location: ../admin/device.php?id=" . $user['id']);
    exit();
} else {
    // ถ้า login ไม่ผ่าน
    echo "<script>alert('อีเมลหรือรหัสผ่านไม่ถูกต้อง'); window.location.href='../login.php';</script>";
}

$stmt->close();
$conn->close();
?>
