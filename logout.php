<?php
require 'fungsi.php'; require 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_cek();
  $_SESSION = [];
  session_destroy();
}
header('Location: login.php');
