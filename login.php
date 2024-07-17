<?php
session_start();
include_once('connection.php');

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    if (empty($username) && empty($password)) {
        echo "<script>alert('Please fill in both username and password.');</script>";
        echo "<script>window.location.href='auth.php';</script>";
        exit;
    } elseif (empty($password)) {
        echo "<script>alert('Please fill in the password.');</script>";
        echo "<script>window.location.href='auth.php';</script>";
        exit;
    } elseif (empty($username)) {
        echo "<script>alert('Please fill in the username.');</script>";
        echo "<script>window.location.href='auth.php';</script>";
        exit;
    }

    $sql = "SELECT * FROM `tbl_user` WHERE `username`='$username' AND `password`='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $id_user = $row['id'];
        $name = $row['name'];
        $username = $row['username'];

        $_SESSION['id_user'] = $id_user;
        $_SESSION['name'] = $name;
        $_SESSION['username'] = $username;

        header('location:index.php');
    } else {
        echo "<script>alert('Invalid Username or Password');</script>";
        echo "<script>window.location.href='auth.php';</script>";
        exit;
    }
}
