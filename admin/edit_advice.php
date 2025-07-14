<?php
if (!isset($_GET['device_id']) || !is_numeric($_GET['device_id']) || !isset($_GET['user_id'])) {
    die("❌ ไม่พบข้อมูล");
}
$deviceID = intval($_GET['device_id']);
$userID = intval($_GET['user_id']);

$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) die("เชื่อมต่อไม่ได้: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // รับค่าจากฟอร์ม
    $deviceName = $_POST['DeviceName'];
    $os = $_POST['OS'];
    $cpu = $_POST['CPU'];
    $ram = $_POST['RAM'];
    $storage = $_POST['Storage'];
    $mac = $_POST['MACAddress'];
    $ip = $_POST['IPAddress'];
    $connType = $_POST['ConnectionType'];

    // อัปเดต Devices
    $stmt = $conn->prepare("UPDATE Devices SET DeviceName=?, OS=?, CPU=?, RAM=?, Storage=? WHERE DeviceID=?");
    $stmt->bind_param("sssisi", $deviceName, $os, $cpu, $ram, $storage, $deviceID);
    $stmt->execute();

    // อัปเดต NetworkInfo
    $stmt2 = $conn->prepare("UPDATE NetworkInfo SET MACAddress=?, IPAddress=?, ConnectionType=?, UpdatedAt=NOW() WHERE DeviceID=?");
    $stmt2->bind_param("sssi", $mac, $ip, $connType, $deviceID);
    $stmt2->execute();

    $stmt->close();
    $stmt2->close();
    $conn->close();

    header("Location: device.php?id=$userID");
    exit();
}

// ดึงข้อมูลเดิม
$stmt = $conn->prepare("SELECT d.*, n.MACAddress, n.IPAddress, n.ConnectionType FROM Devices d LEFT JOIN NetworkInfo n ON d.DeviceID = n.DeviceID WHERE d.DeviceID = ?");
$stmt->bind_param("i", $deviceID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) die("❌ ไม่พบอุปกรณ์");
$data = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขอุปกรณ์</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <h2>✏️ แก้ไขอุปกรณ์</h2>
        <form method="POST">
            <div class="form-group"><label>ชื่ออุปกรณ์</label><input name="DeviceName" class="form-control" value="<?= htmlspecialchars($data['DeviceName']) ?>"></div>
            <div class="form-group"><label>OS</label><input name="OS" class="form-control" value="<?= htmlspecialchars($data['OS']) ?>"></div>
            <div class="form-group"><label>CPU</label><input name="CPU" class="form-control" value="<?= htmlspecialchars($data['CPU']) ?>"></div>
            <div class="form-group"><label>RAM (GB)</label><input name="RAM" type="number" class="form-control" value="<?= htmlspecialchars($data['RAM']) ?>"></div>
            <div class="form-group"><label>Storage</label><input name="Storage" class="form-control" value="<?= htmlspecialchars($data['Storage']) ?>"></div>
            <div class="form-group"><label>MAC Address</label><input name="MACAddress" class="form-control" value="<?= htmlspecialchars($data['MACAddress']) ?>"></div>
            <div class="form-group"><label>IP Address</label><input name="IPAddress" class="form-control" value="<?= htmlspecialchars($data['IPAddress']) ?>"></div>
            <div class="form-group"><label>ประเภทการเชื่อมต่อ</label><input name="ConnectionType" class="form-control" value="<?= htmlspecialchars($data['ConnectionType']) ?>"></div>
            <button type="submit" class="btn btn-warning">อัปเดต</button>
            <a href="device.php?id=<?= $userID ?>" class="btn btn-secondary">ย้อนกลับ</a>
        </form>
    </div>
</body>
</html>
