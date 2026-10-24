<?php
$host = getenv('DB_HOST') ?: 'db';
$db   = getenv('MYSQL_DATABASE') ?: 'student_db';
$user = getenv('MYSQL_USER') ?: 'student_user';
$pass = getenv('MYSQL_PASSWORD') ?: '';

$conn = new mysqli($host, $user, $pass, $db);

if (!$conn->connect_error) {
    $conn->query("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_code VARCHAR(20) NOT NULL UNIQUE,
        full_name VARCHAR(100) NOT NULL,
        class_name VARCHAR(50) NOT NULL,
        gpa FLOAT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $check = $conn->query("SELECT COUNT(*) as total FROM students");
    $row = $check->fetch_assoc();
    if ($row['total'] == 0) {
        $conn->query("INSERT INTO students (student_code, full_name, class_name, gpa) VALUES 
            ('DTC245200065', 'Hoang Linh', 'KTPM-K23A', 3.85),
            ('DTC245200001', 'Nguyen Van An', 'KTPM-K23A', 3.50),
            ('DTC245200002', 'Tran Thi Binh', 'CNTT-K23B', 3.70)");
    }

    if (isset($_GET['delete'])) {
        $del_id = intval($_GET['delete']);
        $conn->query("DELETE FROM students WHERE id = $del_id");
        header("Location: /");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
        $code  = trim($_POST['code']);
        $name  = trim($_POST['name']);
        $class = trim($_POST['class']);
        $gpa   = floatval($_POST['gpa']);
        if ($code && $name) {
            $stmt = $conn->prepare("INSERT INTO students (student_code, full_name, class_name, gpa) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssd", $code, $name, $class, $gpa);
            $stmt->execute();
            header("Location: /");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ Thống Quản Lý Sinh Viên - SIS (Đề 2)</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 30px; background-color: #f0f2f5; }
        .container { max-width: 950px; margin: auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1 { color: #0056b3; border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-top: 0; }
        .badge { display: inline-block; padding: 5px 10px; background-color: #28a745; color: white; border-radius: 4px; font-size: 13px; margin-bottom: 15px; }
        .form-box { background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #e9ecef; }
        .form-box input { padding: 8px 12px; margin-right: 8px; border: 1px solid #ced4da; border-radius: 4px; }
        .form-box button { padding: 8px 16px; background: #0056b3; color: white; border: none; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #dee2e6; padding: 10px 12px; text-align: left; }
        th { background-color: #e9ecef; color: #495057; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .btn-del { color: #dc3545; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h1>Hệ Thống Quản Lý Sinh Viên (SIS)</h1>
    <span class="badge">Trạng thái: Đã kết nối MariaDB thành công!</span>

    <div class="form-box">
        <h3>Thêm mới sinh viên</h3>
        <form method="POST">
            <input type="text" name="code" placeholder="Mã SV (VD: DTC245...)" required>
            <input type="text" name="name" placeholder="Họ và tên" required>
            <input type="text" name="class" placeholder="Lớp học" required>
            <input type="number" step="0.01" min="0" max="4" name="gpa" placeholder="Điểm GPA (0-4)" required>
            <button type="submit" name="add_student">Lưu sinh viên</button>
        </form>
    </div>

    <h3>Danh sách sinh viên hiện có</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Mã Sinh Viên</th>
                <th>Họ và Tên</th>
                <th>Lớp</th>
                <th>Điểm GPA</th>
                <th>Thời Gian Khởi Tạo</th>
                <th>Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM students ORDER BY id DESC");
            if ($result && $result->num_rows > 0):
                while($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($row['student_code']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                <td><?php echo htmlspecialchars($row['gpa']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td><a href="/?delete=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Xóa sinh viên này?');">Xóa</a></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="7" style="text-align: center;">Chưa có dữ liệu sinh viên.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
