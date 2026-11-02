<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
include("connection.php");

$arr_month = [
    "1" => "Januari", "2" => "Februari", "3" => "Maret", "4" => "April",
    "5" => "Mei", "6" => "Juni", "7" => "Juli", "8" => "Agustus",
    "9" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
];

$error_message = "";

// PROSES POST: Ketika tombol submit diklik
if (isset($_POST["submit"])) {
    $nim = htmlentities(strip_tags(trim($_POST["nim"])));
    $name = htmlentities(strip_tags(trim($_POST["name"])));
    $birth_city = htmlentities(strip_tags(trim($_POST["birth_city"])));
    $faculty = htmlentities(strip_tags(trim($_POST["faculty"])));
    $department = htmlentities(strip_tags(trim($_POST["department"])));
    $gpa = htmlentities(strip_tags(trim($_POST["gpa"])));
    $birth_date = htmlentities(strip_tags(trim($_POST["birth_date"])));
    $birth_month = htmlentities(strip_tags(trim($_POST["birth_month"])));
    $birth_year = htmlentities(strip_tags(trim($_POST["birth_year"])));

    // Validasi input
    if (empty($name)) { $error_message .= "- Nama belum diisi <br>"; }
    if (empty($birth_city)) { $error_message .= "- Tempat lahir belum diisi <br>"; }
    if (empty($department)) { $error_message .= "- Jurusan belum diisi <br>"; }
    if (!is_numeric($gpa) || ($gpa <= 0)) { $error_message .= "- IPK harus diisi dengan angka <br>"; }

    // Jika tidak ada error, eksekusi UPDATE
    if ($error_message === "") {
        $nim_escaped = mysqli_real_escape_string($connection, $nim);
        $name_escaped = mysqli_real_escape_string($connection, $name);
        $birth_city_escaped = mysqli_real_escape_string($connection, $birth_city);
        $department_escaped = mysqli_real_escape_string($connection, $department);
        $gpa_float = (float) $gpa;
        $birth_date_full = "$birth_year-$birth_month-$birth_date";

        $query = "UPDATE student SET 
                    name = '$name_escaped',
                    birth_city = '$birth_city_escaped',
                    birth_date = '$birth_date_full',
                    faculty = '$faculty',
                    department = '$department_escaped',
                    gpa = $gpa_float
                  WHERE nim = '$nim_escaped'";

        $result = mysqli_query($connection, $query);

        if ($result) {
            $_SESSION["flash"] = "Data mahasiswa \"<b>$name</b>\" berhasil diperbarui.";
            header("Location: student_view.php");
            exit();
        } else {
            die("Query gagal dijalankan: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
        }
    }
} 
// PROSES GET: Mengambil data lama saat halaman pertama kali dibuka
else if (isset($_GET["nim"])) {
    $nim = mysqli_real_escape_string($connection, $_GET["nim"]);
    $query = "SELECT * FROM student WHERE nim = '$nim'";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) === 0) {
        header("Location: student_view.php");
        exit();
    }

    $data = mysqli_fetch_assoc($result);
    $name = $data["name"];
    $birth_city = $data["birth_city"];
    $faculty = $data["faculty"];
    $department = $data["department"];
    $gpa = $data["gpa"];

    // Mengurai tanggal lahir (YYYY-MM-DD)
    $date_parts = explode("-", $data["birth_date"]);
    $birth_year = $date_parts[0];
    $birth_month = (string)(int)$date_parts[1];
    $birth_date = (int)$date_parts[2];
} else {
    header("Location: student_view.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Mahasiswa</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Edit Mahasiswa</h1>
            <div class="nav-links">
                <a href="student_view.php">Kembali</a>
            </div>
        </div>

        <?php if ($error_message != "") echo "<div class='error'>$error_message</div>"; ?>

        <form action="student_edit.php" method="post">
            <div class="form-grid">
                <div class="form-group">
                    <label>NIM (Tidak dapat diubah)</label>
                    <input type="text" name="nim" value="<?= htmlspecialchars($nim) ?>" readonly style="background-color: #f1f1f1; cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" placeholder="Nama Anda" value="<?= htmlspecialchars($name) ?>">
                </div>
                <div class="form-group">
                    <label>Tempat Lahir</label>
                    <input type="text" name="birth_city" placeholder="Kota Kelahiran" value="<?= htmlspecialchars($birth_city) ?>">
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <div class="date-group" style="display: flex; gap: 10px;">
                        <select name="birth_date" style="flex: 1;">
                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                <option value="<?= $i ?>" <?= ((int)$birth_date === $i) ? 'selected' : '' ?>>
                                    <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <select name="birth_month" style="flex: 1;">
                            <?php foreach ($arr_month as $key => $val): ?>
                                <option value="<?= $key ?>" <?= ((string)$birth_month === (string)$key) ? 'selected' : '' ?>>
                                    <?= $val ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="birth_year" style="flex: 1;">
                            <?php for ($i = 1990; $i <= 2005; $i++): ?>
                                <option value="<?= $i ?>" <?= ((int)$birth_year === $i) ? 'selected' : '' ?>>
                                    <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Fakultas</label>
                    <select name="faculty">
                        <option value="FTIB" <?= ($faculty === "FTIB") ? 'selected' : '' ?>>FTIB</option>
                        <option value="FTEIC" <?= ($faculty === "FTEIC") ? 'selected' : '' ?>>FTEIC</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jurusan</label>
                    <input type="text" name="department" placeholder="Contoh: Sistem Informasi" value="<?= htmlspecialchars($department) ?>">
                </div>
                <div class="form-group full-width">
                    <label>IPK</label>
                    <input type="text" name="gpa" placeholder="Contoh: 3.75 (Gunakan titik)" value="<?= htmlspecialchars($gpa) ?>">
                </div>
            </div>
            <button type="submit" name="submit" class="btn-submit">Perbarui Data</button>
        </form>
    </div>
</body>
</html>