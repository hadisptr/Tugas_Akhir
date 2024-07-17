<?php
session_start();
include_once('connection.php');

if (isset($_POST['Simpan'])) {
    $bahagia = $_POST['bahagia'];
    $sedih = $_POST['sedih'];
    $marah = $_POST['marah'];
    $takut = $_POST['takut'];
    $netral = $_POST['netral'];
    $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null; // Getting the user ID from the session

    if (empty($bahagia) || empty($sedih) || empty($marah) || empty($takut) || empty($netral)) {
        echo "<script>alert('All fields are required.');</script>";
        echo "<script>window.location.href='setting.php';</script>";
        exit;
    }

    if ($id_user === null) {
        echo "<script>alert('User ID is not available. Please log in again.');</script>";
        echo "<script>window.location.href='auth.php';</script>";
        exit;
    }

    $sql = "INSERT INTO `tbl_lagu`(`bahagia`, `sedih`, `marah`, `takut`, `netral`, `id_user`) VALUES ('$bahagia', '$sedih', '$marah', '$takut', '$netral', '$id_user')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo "<script>alert('Data Lagu berhasil disimpan.');</script>";
        echo "<script>window.location.href='index.php';</script>";
    } else {
        die(mysqli_error($conn));
    }
}
