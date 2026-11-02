<?php
session_start();
include 'connection.php';

// Jika sudah login, langsung lempar ke index
if (isset($_SESSION['status_login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password']; // Jika di DB pakai MD5, ubah jadi: md5($_POST['password'])

    // Sesuaikan nama tabel dan kolom dengan database buatan Mahasiswa A
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $_SESSION['status_login'] = true;
        $_SESSION['username'] = $data['username'];
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Sistem</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container" style="max-width: 400px; margin-top: 100px;">
        <h2>Login</h2>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        
        <form method="POST" action="">
            <div style="margin-bottom: 10px;">
                <label>Username</label><br>
                <input type="text" name="username" required style="width: 100%; padding: 8px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Password</label><br>
                <input type="password" name="password" required style="width: 100%; padding: 8px;">
            </div>
            <button type="submit" name="login" class="btn btn-tambah" style="width: 100%;">Masuk</button>
        </form>
    </div>
</body>
</html>