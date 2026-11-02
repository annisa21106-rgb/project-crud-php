<?php 
session_start(); 
if (!isset($_SESSION["username"])) { 
    header("Location: login.php"); 
    exit(); 
} 
include("connection.php"); 

// Inisialisasi variabel untuk mencegah warning undefined variable
$error_message = ""; 
$nim = ""; $name = ""; $birth_city = ""; $department = ""; $gpa = ""; 
$birth_date = 1; $birth_month = "1"; $birth_year = "1990";

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

    // Validasi NIM 
    if (empty($nim)) { 
        $error_message .= "- NIM belum diisi <br>"; 
    } else if (!preg_match("/^[0-9]{8}$/", $nim)) { 
        $error_message .= "- NIM harus berupa 8 digit angka <br>"; 
    } 

    $nim_escaped = mysqli_real_escape_string($connection, $nim); 
    $query = "SELECT * FROM student WHERE nim='$nim_escaped'"; 
    $query_result = mysqli_query($connection, $query); 
    if (mysqli_num_rows($query_result) >= 1) { 
        $error_message .= "- NIM yang sama sudah digunakan <br>"; 
    } 

    // Validasi field lainnya 
    if (empty($name)) { $error_message .= "- Nama belum diisi <br>"; } 
    if (empty($birth_city)) { $error_message .= "- Tempat lahir belum diisi <br>"; } 
    if (empty($department)) { $error_message .= "- Jurusan belum diisi <br>"; } 
    if (!is_numeric($gpa) OR ($gpa <= 0)) { $error_message .= "- IPK harus diisi dengan angka <br>"; } 

    // Jika tidak ada error, simpan ke database 
    if ($error_message === "") { 
        $name_escaped = mysqli_real_escape_string($connection, $name); 
        $birth_city_escaped = mysqli_real_escape_string($connection, $birth_city); 
        $department_escaped = mysqli_real_escape_string($connection, $department); 
        $gpa_float = (float) $gpa; 
        $birth_date_full = "$birth_year-$birth_month-$birth_date"; 

        $query = "INSERT INTO student VALUES ('$nim_escaped', '$name_escaped', '$birth_city_escaped', '$birth_date_full', '$faculty', '$department_escaped', '$gpa_float')"; 
        $result = mysqli_query($connection, $query); 

        if ($result) { 
            $_SESSION["flash"] = "Mahasiswa dengan nama \"<b>$name</b>\" berhasil ditambahkan."; 
            header("Location: student_view.php"); 
            exit(); 
        } else { 
            die("Query gagal dijalankan: " . mysqli_errno($connection) . " - " . mysqli_error($connection)); 
        } 
    } 
} 

$arr_month = [ 
    "1" => "Januari", "2" => "Februari", "3" => "Maret", "4" => "April", 
    "5" => "Mei", "6" => "Juni", "7" => "Juli", "8" => "Agustus", 
    "9" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember" 
]; 
?> 

<!DOCTYPE html> 
<html lang="id"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Mahasiswa</title> 
    <link rel="stylesheet" href="assets/style.css"> 
</head> 
<body> 
    <div class="container"> 
        <div class="header"> 
            <h1>Tambah Mahasiswa</h1> 
            <div class="nav-links"> 
                <a href="student_view.php">Kembali</a> 
            </div> 
        </div> 

        <!-- Cek error hanya akan berjalan jika ada isinya --> 
        <?php if ($error_message != "") echo "<div class='error'>$error_message</div>"; ?> 

        <form action="student_add.php" method="post"> 
            <div class="form-grid"> 
                <div class="form-group"> 
                    <label>NIM (8 Digit Angka)</label> 
                    <input type="text" name="nim" placeholder="Contoh: 12345678" value="<?= $nim ?>"> 
                </div> 
                <div class="form-group"> 
                    <label>Nama Lengkap</label> 
                    <input type="text" name="name" placeholder="Nama Anda" value="<?= $name ?>"> 
                </div> 
                <div class="form-group"> 
                    <label>Tempat Lahir</label> 
                    <input type="text" name="birth_city" placeholder="Kota Kelahiran" value="<?= $birth_city ?>"> 
                </div> 
                <div class="form-group"> 
                    <label>Tanggal Lahir</label> 
                    <div class="date-group" style="display: flex; gap: 10px;"> 
                        <select name="birth_date" style="flex: 1;"> 
                            <?php for ($i = 1; $i <= 31; $i++) echo "<option value='$i'>".str_pad($i, 2, 0, STR_PAD_LEFT)."</option>"; ?> 
                        </select> 
                        <select name="birth_month" style="flex: 1;"> 
                            <?php foreach ($arr_month as $key => $val) echo "<option value='$key'>$val</option>"; ?> 
                        </select> 
                        <select name="birth_year" style="flex: 1;"> 
                            <?php for ($i = 1990; $i <= 2005; $i++) echo "<option value='$i'>$i</option>"; ?> 
                        </select> 
                    </div> 
                </div> 
                <div class="form-group"> 
                    <label>Fakultas</label> 
                    <select name="faculty"> 
                        <option value="FTIB">FTIB</option> 
                        <option value="FTEIC">FTEIC</option> 
                    </select> 
                </div> 
                <div class="form-group"> 
                    <label>Jurusan</label> 
                    <input type="text" name="department" placeholder="Contoh: Sistem Informasi" value="<?= $department ?>"> 
                </div> 
                <div class="form-group full-width"> 
                    <label>IPK</label> 
                    <input type="text" name="gpa" placeholder="Contoh: 3.75 (Gunakan titik)" value="<?= $gpa ?>"> 
                </div> 
            </div> 
            <button type="submit" name="submit" class="btn-submit">Simpan Data</button> 
        </form> 
    </div> 
</body> 
</html>