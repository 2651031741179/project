<?php
// ตรวจสอบว่ามี ID และเป็นตัวเลขหรือไม่
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ กรุณาระบุ User ID ที่ถูกต้องใน URL เช่น ?id=1");
}

$userID = intval($_GET['id']);

// 🔗 เชื่อมต่อฐานข้อมูล
$host = "localhost";
$dbname = "test";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// 🔎 ดึงข้อมูลผู้ใช้
$userStmt = $conn->prepare("
    SELECT t.name, t.lastname, s.gender AS prefix
    FROM test t
    LEFT JOIN sex s ON t.gender = s.id
    WHERE t.id = ?
");
$userStmt->bind_param("i", $userID);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    die("❌ ไม่พบผู้ใช้ในระบบ");
}
$userRow = $userResult->fetch_assoc();
$fullName = trim($userRow['prefix'] . " " . $userRow['name'] . " " . $userRow['lastname']);

// 🔎 ดึงข้อมูลอุปกรณ์
$sql = "
    SELECT 
        d.DeviceID,
        d.DeviceName,
        d.OS,
        d.CPU,
        d.RAM,
        d.Storage,
        d.CreatedAt,
        n.MACAddress,
        n.IPAddress,
        n.ConnectionType
    FROM Devices d
    LEFT JOIN NetworkInfo n ON d.DeviceID = n.DeviceID
    WHERE d.UserID = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>ข้อมูลอุปกรณ์ของ <?= htmlspecialchars($fullName) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playpen+Sans+Thai:wght@100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body, table, th, td, h2 {
            font-family: 'Playpen Sans Thai', cursive;
        }
        body {
            background-color: #eef2f7;
            padding: 40px;
            margin: 0;
        }
        h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }
        th, td {
            padding: 12px 14px;
            border: 1px solid #ddd;
            text-align: left;
        }
        thead th {
            background-color: #28a745;
            color: white;
        }
        .table-striped-custom tbody tr:nth-child(odd) {
            background-color: #d4edda;
        }
        .table-striped-custom tbody tr:nth-child(even) {
            background-color: #a3cfbb;
        }
        .no-data {
            color: #e74c3c;
            font-weight: bold;
            margin-top: 20px;
        }
        a.button {
            display: inline-block;
            padding: 8px 16px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        a.button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<h1>📋 รายการอุปกรณ์ของผู้ใช้: <?= htmlspecialchars($fullName) ?></h1>

<!-- ปุ่มเพิ่ม -->
<a href="add_device.php?id=<?= $userID ?>" class="btn btn-success mb-3">➕ เพิ่มอุปกรณ์</a>

<?php if ($result->num_rows > 0): ?>
    <table class="table-striped-custom">
        <thead>
            <tr>
                <th>ชื่ออุปกรณ์</th>
                <th>ระบบปฏิบัติการ</th>
                <th>CPU</th>
                <th>RAM</th>
                <th>Storage</th>
                <th>MAC</th>
                <th>IP</th>
                <th>ประเภท</th>
                <th>วันที่เพิ่ม</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['DeviceName']) ?></td>
                    <td><?= htmlspecialchars($row['OS']) ?></td>
                    <td><?= htmlspecialchars($row['CPU']) ?></td>
                    <td><?= htmlspecialchars($row['RAM']) ?> GB</td>
                    <td><?= htmlspecialchars($row['Storage']) ?></td>
                    <td><?= htmlspecialchars($row['MACAddress']) ?></td>
                    <td><?= htmlspecialchars($row['IPAddress']) ?></td>
                    <td><?= htmlspecialchars($row['ConnectionType']) ?></td>
                    <td><?= htmlspecialchars($row['CreatedAt']) ?></td>
                    <td>
                        <a href="edit_device.php?device_id=<?= $row['DeviceID'] ?>&user_id=<?= $userID ?>" class="btn btn-sm btn-warning">✏️</a>
                        <a href="delete_device.php?device_id=<?= $row['DeviceID'] ?>&user_id=<?= $userID ?>" class="btn btn-sm btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบอุปกรณ์นี้?');">🗑️</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="no-data">ไม่พบข้อมูลอุปกรณ์ของผู้ใช้นี้</p>
<?php endif; ?>

<a href="javascript:history.back()" class="button">🔙 ย้อนกลับ</a>

</body>
</html>

<?php
$stmt->close();
$userStmt->close();
$conn->close();
?>
