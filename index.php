<?php
// ==========================================
// TAMBAHAN DARI MAHASISWA B (PROTEKSI LOGIN)
// ==========================================
session_start();

// Cek apakah user sudah login. Jika belum, tendang ke halaman login.php
if (!isset($_SESSION['status_login'])) {
    header("Location: login.php");
    exit;
}

// PENTING: Karena nama file Anda 'connection.php', panggil dengan nama itu
include 'connection.php'; 
// ==========================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Data Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <!-- Header dengan sapaan dan tombol Logout -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Daftar Produk</h2>
            <div>
                <span>Halo, <b><?php echo $_SESSION['username']; ?></b>!</span>
                <a href="logout.php" class="btn" style="background-color: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; margin-left: 10px;">Logout</a>
            </div>
        </div>

        <!-- Tombol tambah akan berfungsi setelah dikerjakan Mahasiswa C -->
        <a href="student_add.php" class="btn btn-tambah">+ Tambah Data</a>
        <br><br>
        
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // ==========================================
                // MENAMPILKAN DATA
                // ==========================================
                // Query untuk mengambil data dari database (Asumsi nama tabel: 'produk')
                $query = "SELECT * FROM produk"; 
                $result = mysqli_query($connection, $query); // PERHATIKAN: variabel koneksinya sesuaikan dengan punya Mahasiswa A
                
                $no = 1;
                
                // Cek jika ada data
                if (mysqli_num_rows($result) > 0) { // PERBAIKAN: mysqli_num_rows pakai underscore
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td align='center'>" . $no++ . "</td>";
                        
                        // Pastikan nama kolom di dalam $row['...'] sesuai dengan database asli dari Mahasiswa A
                        echo "<td>" . $row['nama_produk'] . "</td>"; 
                        echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>"; 
                        echo "<td align='center'>" . $row['stok'] . "</td>"; 
                        
                        // Tombol Aksi (Akan diarahkan ke file buatan Mahasiswa D)
                        echo "<td align='center'>";
                        echo "<a href='student_edit.php?id=" . $row['id'] . "'>Edit</a> | ";
                        echo "<a href='student_delete.php?id=" . $row['id'] . "' onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>Hapus</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    // Jika database kosong atau belum terhubung
                    echo "<tr>";
                    echo "<td colspan='5' align='center'>Belum ada data produk di database.</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>