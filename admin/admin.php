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

// SQL ดึงข้อมูล
$sql = "SELECT 
    test.id, 
    test.name,
    test.lastname, 
    test.email, 
    name.name AS department_Fullname,
    test.password,
    sex.gender AS gender_name
FROM test
LEFT JOIN name ON test.department = name.id
LEFT JOIN sex ON test.gender = sex.id";

// รันคำสั่ง SQL
$result = $conn->query($sql);

if (!$result) {
    die("เกิดข้อผิดพลาดในการ query: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>แสดงข้อมูลผู้ใช้</title>

  <!-- ✅ ลิงก์ Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playpen+Sans+Thai:wght@100..800&display=swap" rel="stylesheet">

  <!-- ✅ ลิงก์ Bootstrap 4 -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <!-- ✅ CSS ฟอนต์ และตาราง -->
  <style>
    body, table, th, td, h2 {
      font-family: 'Playpen Sans Thai', cursive;
    }

    .table-striped-custom tbody tr:nth-child(odd) {
      background-color: #d4edda; /* เขียวอ่อน */
    }

    .table-striped-custom tbody tr:nth-child(even) {
      background-color: #a3cfbb; /* เขียวเข้ม */
    }

    .table thead th {
      background-color: #28a745;
      color: white;
    }

    a.button {
      padding: 5px 10px;
      background: #007BFF;
      color: white;
      text-decoration: none;
      border-radius: 3px;
    }

    a.delete {
      background: #DC3545;
    }
  </style>

    </style>
    <!-- Bootstrap CSS CDN -->
<!-- Bootstrap 4 -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">


</head>
<body>
    <h2 style="text-align:center;">รายชื่อผู้ใช้ทั้งหมด</h2>

    <?php
    if ($result->num_rows > 0) {
       echo "<table class='table table-bordered table-striped-custom'>
        <thead>
          <tr>
            <th>ID</th>
            <th>คำนำหน้า</th>
            <th>ชื่อ</th>
            <th>นามสกุล</th>
            <th>Email</th>
            <th>แผนก</th>
            <th>การจัดการ</th>
          </tr>
        </thead>
        <tbody>";
while($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>".$row["id"]."</td>
            <td>".$row["gender_name"]."</td>
            <td>".$row["name"]."</td>
            <td>".$row["lastname"]."</td>
            <td>".$row["email"]."</td>
            <td>".$row["department_Fullname"]."</td>
            <td>
                <a href='device.php?id=".$row['id']."' class='button'>ดูอุปกรณ์ของผู้ใช้<a>
                <a href='edit.php?id=".$row['id']."' class='button'>แก้ไข</a>
                <a href='del.php?id=".$row['id']."' class='button delete' onclick='return confirm(\"คุณแน่ใจหรือไม่ว่าต้องการลบ?\")'>ลบ</a>
            </td>
          </tr>";
}
echo "</tbody></table>";

    } else {
        echo "<p style='text-align:center;'>ไม่พบข้อมูล</p>";
    }

    $conn->close();
    ?>
</body>
</html>
