<?php
include_once('connection.php');

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $pass = md5($_POST['password']);

    if (empty($name) || empty($username) || empty($_POST['password'])) {
        echo "<script>alert('All fields are required.');</script>";
        echo "<script>window.location.href='register.php';</script>";
        exit;
    }

    $sql = "INSERT INTO `tbl_user`(`name`, `username`, `password`) VALUES ('$name', '$username', '$pass')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo "<script>alert('New User Register Success');</script>";
        echo "<script>window.location.href='auth.php';</script>";
    } else {
        die(mysqli_error($conn));
    }
}
?>
