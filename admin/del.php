 <?php
    // ตั้งค่าการเชื่อมต่อกับฐานข้อมูล
    $servername = "localhost";
    $username = "root"; // หรือชื่อผู้ใช้ของคุณ
    $password = ""; // รหัสผ่านของคุณ
    $dbname = "test";

    // เชื่อมต่อ
    $conn = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
    }
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM test WHERE id=$id";
    if ($conn->query($sql)) {
        header("Location: admin.php");
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>
