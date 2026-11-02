<?php
// Koneksi ke database MySQL (Sesuaikan dengan kredensial XAMPP kamu)
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nama_database_kamu'; // Ganti dengan nama database proyekmu

$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Ambil parameter bulan dan tahun dari URL (jika ada), atau gunakan bulan/tahun saat ini
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Query untuk mengambil data rekapitulasi booking
$query = "SELECT b.id, u.name as pemesan, f.name as fasilitas, b.created_at, b.total_price, b.status 
          FROM bookings b 
          LEFT JOIN users u ON b.user_id = u.id 
          LEFT JOIN facilities f ON b.facility_id = f.id 
          WHERE MONTH(b.created_at) = '$month' AND YEAR(b.created_at) = '$year'";

$result = $conn->query($query);

// Nama file output untuk unduhan CSV/Excel
$filename = "laporan-booking-" . $year . "-" . $month . ".csv";

// Set header agar browser langsung mendownload file tersebut sebagai CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buka output stream file
$output = fopen('php://output', 'w');

// Tulis baris header kolom ke file CSV
fputcsv($output, ['ID Booking', 'Nama Pemesan', 'Fasilitas', 'Tanggal Transaksi', 'Total Biaya', 'Status']);

// Masukkan data baris demi baris dari database ke file CSV
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['pemesan'] ?? 'Guest',
            $row['fasilitas'] ?? 'N/A',
            $row['created_at'],
            $row['total_price'],
            $row['status']
        ]);
    }
} else {
    // Jika data kosong
    fputcsv($output, ['Tidak ada data pada periode ini', '', '', '', '', '']);
}

// Tutup koneksi dan stream
fclose($output);
$conn->close();
exit;
?>