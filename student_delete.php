<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
include("connection.php");

if (isset($_GET["nim"])) {
    $nim = mysqli_real_escape_string($connection, $_GET["nim"]);

    // Ambil nama mahasiswa terlebih dahulu untuk isi pesan flash
    $query_select = "SELECT name FROM student WHERE nim = '$nim'";
    $result_select = mysqli_query($connection, $query_select);

    if ($result_select && mysqli_num_rows($result_select) > 0) {
        $data = mysqli_fetch_assoc($result_select);
        $name = $data["name"];

        // Query hapus data
        $query_delete = "DELETE FROM student WHERE nim = '$nim'";
        $result_delete = mysqli_query($connection, $query_delete);

        if ($result_delete) {
            $_SESSION["flash"] = "Mahasiswa dengan nama \"<b>$name</b>\" berhasil dihapus.";
            header("Location: student_view.php");
            exit();
        } else {
            die("Query gagal dijalankan: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
        }
    } else {
        header("Location: student_view.php");
        exit();
    }
} else {
    header("Location: student_view.php");
    exit();
}
?>