<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ ไม่พบรหัสผู้ใช้");
}
$userID = intval($_GET['id']);

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

    // เชื่อมต่อ DB
    $conn = new mysqli("localhost", "root", "", "test");
    if ($conn->connect_error) die("เชื่อมต่อไม่ได้: " . $conn->connect_error);

    // Insert into Devices
    $stmt = $conn->prepare("INSERT INTO Devices (DeviceName, OS, CPU, RAM, Storage, UserID, CreatedAt) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssisi", $deviceName, $os, $cpu, $ram, $storage, $userID);
    $stmt->execute();
    $deviceID = $stmt->insert_id;

    // Insert into NetworkInfo
    $stmt2 = $conn->prepare("INSERT INTO NetworkInfo (DeviceID, MACAddress, IPAddress, ConnectionType, UpdatedAt) VALUES (?, ?, ?, ?, NOW())");
    $stmt2->bind_param("isss", $deviceID, $mac, $ip, $connType);
    $stmt2->execute();

    $stmt->close();
    $stmt2->close();
    $conn->close();

    header("Location: device.php?id=$userID");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มอุปกรณ์</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <h2>➕ เพิ่มอุปกรณ์</h2>
        <form method="POST">
            <div class="form-group"><label>ชื่ออุปกรณ์</label><input name="DeviceName" class="form-control" required></div>
            <div class="form-group"><label>OS</label><input name="OS" class="form-control" required></div>
            <div class="form-group"><label>CPU</label><input name="CPU" class="form-control" required></div>
            <div class="form-group"><label>RAM (GB)</label><input name="RAM" type="number" class="form-control" required></div>
            <div class="form-group"><label>Storage</label><input name="Storage" class="form-control" required></div>
            <div class="form-group"><label>MAC Address</label><input name="MACAddress" class="form-control" required></div>
            <div class="form-group"><label>IP Address</label><input name="IPAddress" class="form-control" required></div>
            <div class="form-group"><label>ประเภทการเชื่อมต่อ</label><input name="ConnectionType" class="form-control" required></div>
            <button type="submit" class="btn btn-success">บันทึก</button>
            <a href="device.php?id=<?= $userID ?>" class="btn btn-secondary">ย้อนกลับ</a>
        </form>
    </div>
</body>
</html>
