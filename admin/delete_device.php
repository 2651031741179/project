<?php
if (!isset($_GET['device_id']) || !isset($_GET['user_id'])) {
    die("❌ ข้อมูลไม่ครบ");
}

$deviceID = intval($_GET['device_id']);
$userID = intval($_GET['user_id']);

$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) die("เชื่อมต่อไม่ได้: " . $conn->connect_error);

// ลบจาก NetworkInfo ก่อน
$stmt1 = $conn->prepare("DELETE FROM NetworkInfo WHERE DeviceID = ?");
$stmt1->bind_param("i", $deviceID);
$stmt1->execute();

// ลบจาก Devices
$stmt2 = $conn->prepare("DELETE FROM Devices WHERE DeviceID = ?");
$stmt2->bind_param("i", $deviceID);
$stmt2->execute();

$stmt1->close();
$stmt2->close();
$conn->close();

header("Location: device.php?id=$userID");
exit();
?>
