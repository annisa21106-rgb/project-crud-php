<?php 
session_start();

// Proteksi Halaman: Jika belum login, tendang ke halaman login 
if (!isset($_SESSION["username"])) { 
    header("Location: login.php"); 
    exit(); 
} 

include("connection.php"); 

// Menampilkan pesan flash (jika ada) setelah tambah/edit/hapus data 
$message = ""; 
if (isset($_SESSION["flash"])) { 
    $message = $_SESSION["flash"]; 
    unset($_SESSION["flash"]); // Hapus pesan agar hanya tampil sekali 
} 

// Query mengambil semua data mahasiswa 
$query = "SELECT * FROM student ORDER BY nim ASC"; 
$result = mysqli_query($connection, $query); 
?> 

<!DOCTYPE html> 
<html lang="id"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title> 
    <link rel="stylesheet" href="assets/style.css"> 
</head> 
<body> 
    <div class="container"> 
        <div class="header"> 
            <h1>Data Mahasiswa</h1> 
            <div class="nav-links"> 
                <a href="student_view.php">Tampil</a> 
                <a href="student_add.php">Tambah</a> 
                <a href="logout.php" class="btn-logout">Logout</a> 
            </div> 
        </div> 

        <!-- Menampilkan pesan sukses jika ada --> 
        <?php if ($message != "") echo "<div class='pesan'>$message</div>"; ?> 

        <div class="table-responsive"> 
            <table> 
                <thead> 
                    <tr> 
                        <th>NIM</th> 
                        <th>Nama</th> 
                        <th>Tempat Lahir</th> 
                        <th>Tanggal Lahir</th> 
                        <th>Fakultas</th> 
                        <th>Jurusan</th> 
                        <th>IPK</th> 
                        <th>Aksi</th> 
                    </tr> 
                </thead> 
                <tbody> 
                    <?php 
                    // Cek jika query gagal 
                    if (!$result) { 
                        die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection)); 
                    } 

                    // Looping data dari database 
                    while ($data = mysqli_fetch_assoc($result)) { 
                        // Format tanggal lahir dari YYYY-MM-DD menjadi DD-MM-YYYY 
                        $birth_date = strtotime($data["birth_date"]); 
                        $formatted_date = date("d-m-Y", $birth_date); 

                        echo "<tr>"; 
                        echo "<td><strong>$data[nim]</strong></td>"; 
                        echo "<td>$data[name]</td>"; 
                        echo "<td>$data[birth_city]</td>"; 
                        echo "<td>$formatted_date</td>"; 
                        echo "<td>$data[faculty]</td>"; 
                        echo "<td>$data[department]</td>"; 
                        echo "<td>$data[gpa]</td>"; 
                        
                        // Kolom Aksi dengan pembungkus Flexbox agar sejajar 
                        echo "<td>"; 
                        echo "<div class='action-buttons'>"; 
                        echo "<a href='student_edit.php?nim=$data[nim]' class='btn-aksi btn-edit'>Edit</a>"; 
                        echo "<a href='student_delete.php?nim=$data[nim]' class='btn-aksi btn-hapus' onclick='return confirm(\"Yakin ingin menghapus data $data[name]?\")'>Hapus</a>"; 
                        echo "</div>"; 
                        echo "</td>"; 
                        echo "</tr>"; 
                    } 
                    mysqli_free_result($result); 
                    mysqli_close($connection); 
                    ?> 
                </tbody> 
            </table> 
        </div> 
    </div> 
</body> 
</html>