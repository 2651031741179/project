<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// ดึงข้อมูลผู้ใช้
$user = null;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM test WHERE id=$id";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
}

// ดึงข้อมูลคำนำหน้า (sex)
$genders = [];
$sqlGender = "SELECT id, gender FROM sex";
$resultGender = $conn->query($sqlGender);
if ($resultGender) {
    while ($row = $resultGender->fetch_assoc()) {
        $genders[$row['id']] = $row['gender'];
    }
}

// ดึงข้อมูลแผนก (name)
$departments = [];
$sqlDept = "SELECT id, name FROM name";
$resultDept = $conn->query($sqlDept);
if ($resultDept) {
    while ($row = $resultDept->fetch_assoc()) {
        $departments[$row['id']] = $row['name'];
    }
}

// อัพเดตข้อมูล
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $department = (int)$_POST['department'];
    $gender = (int)$_POST['gender'];

    $sql = "UPDATE test SET name='$name', email='$email', department=$department, gender=$gender WHERE id=$id";
    if ($conn->query($sql)) {
        header("Location: admin.php");
        exit;
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>แก้ไขผู้ใช้</title>
    <link href="https://fonts.googleapis.com/css2?family=Playpen+Sans+Thai:wght@100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body, h2 {
            font-family: 'Playpen Sans Thai', cursive;
        }
        .form-container {
            max-width: 400px;
            margin: 40px auto;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="text-center mb-4">แก้ไขข้อมูลผู้ใช้</h2>
        <?php if ($user): ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']); ?>">
            
            <div class="form-group">
                <label for="gender">คำนำหน้า</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="">-- เลือกคำนำหน้า --</option>
                    <?php foreach ($genders as $gid => $gname): ?>
                        <option value="<?= $gid ?>" <?= $user['gender'] == $gid ? 'selected' : '' ?>><?= htmlspecialchars($gname) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="name">ชื่อ</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="department">แผนก</label>
                <select class="form-control" id="department" name="department" required>
                    <option value="">-- เลือกแผนก --</option>
                    <?php foreach ($departments as $did => $dname): ?>
                        <option value="<?= $did ?>" <?= $user['department'] == $did ? 'selected' : '' ?>><?= htmlspecialchars($dname) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="email">อีเมล</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
            </div>

            <button type="submit" name="update" class="btn btn-success btn-block">บันทึก</button>
            <a href="admin.php" class="btn btn-secondary btn-block">ยกเลิก</a>
        </form>
        <?php else: ?>
            <p class="text-center text-danger">ไม่พบข้อมูลผู้ใช้</p>
            <a href="admin.php" class="btn btn-primary btn-block">กลับหน้ารายชื่อ</a>
        <?php endif; ?>
    </div>
</body>
</html>
