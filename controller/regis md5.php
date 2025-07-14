<?php
// 1. เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. รับค่าจากฟอร์ม
$name       = $_POST['name']       ?? '';
$lastname   = $_POST['lastname']   ?? '';
$email      = $_POST['email']      ?? '';
$password   = $_POST['password']   ?? '';
$department = $_POST['department'] ?? '';
$gender     = $_POST['gender']     ?? '';

// ข้อมูลอุปกรณ์
$deviceName = $_POST['deviceName'] ?? '';
$os         = $_POST['os']         ?? '';
$cpu        = $_POST['cpu']        ?? '';
$ram        = $_POST['ram']        ?? '';
$storage    = $_POST['storage']    ?? '';

// ข้อมูลเครือข่าย
$mac            = $_POST['mac']            ?? '';
$ip             = $_POST['ip']             ?? '';
$connectionType = $_POST['connectionType'] ?? '';

// 3. เข้ารหัสรหัสผ่านด้วย md5
$hashedPassword = md5($password);

// 4. เริ่ม transaction
$conn->begin_transaction();

try {
    // 5. Insert ผู้ใช้
    $sqlUser = "INSERT INTO test (name, lastname, email, password, department, gender) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtUser = $conn->prepare($sqlUser);
    if (!$stmtUser) throw new Exception("Prepare user failed: " . $conn->error);
    $stmtUser->bind_param("ssssss", $name, $lastname, $email, $hashedPassword, $department, $gender);
    if (!$stmtUser->execute()) throw new Exception("Execute user failed: " . $stmtUser->error);
    $userID = $conn->insert_id;
    $stmtUser->close();

    // 6. Insert อุปกรณ์
    $deviceID = null;
    if ($deviceName && $os && $cpu && $ram && $storage) {
        $sqlDevice = "INSERT INTO devices (UserID, DeviceName, os, CPU, ram, Storage, CreatedAt) 
                      VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmtDevice = $conn->prepare($sqlDevice);
        if (!$stmtDevice) throw new Exception("Prepare device failed: " . $conn->error);
        $ramInt = intval($ram);
        $stmtDevice->bind_param("isssis", $userID, $deviceName, $os, $cpu, $ramInt, $storage);
        if (!$stmtDevice->execute()) throw new Exception("Execute device failed: " . $stmtDevice->error);
        $deviceID = $conn->insert_id;
        $stmtDevice->close();
    }

   // 7. Insert network info ถ้ามีข้อมูลครบ
if ($deviceID && $mac && $ip && $connectionType) {
    // sanitize string (optional but good)
    $mac = trim($mac);
    $ip = trim($ip);
    $connectionType = trim($connectionType);

    $sqlNetwork = "INSERT INTO networkinfo (DeviceID, MACAddress, IPAddress, ConnectionType, UpdatedAt) 
                   VALUES (?, ?, ?, ?, NOW())";
    $stmtNetwork = $conn->prepare($sqlNetwork);
    if (!$stmtNetwork) throw new Exception("Prepare network failed: " . $conn->error);
    $stmtNetwork->bind_param("isss", $deviceID, $mac, $ip, $connectionType);
    if (!$stmtNetwork->execute()) throw new Exception("Execute network failed: " . $stmtNetwork->error);
    $stmtNetwork->close();
}

    // 8. commit transaction
    $conn->commit();

    // 9. redirect
    header("Location: ../login.html");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    echo "❌ Error: " . $e->getMessage();
}

$conn->close();
?>
