<?php
$pageTitle = $pageTitle ?? APP_NAME;
$user = Auth::currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(APP_NAME . ' | ' . $pageTitle) ?></title>
  <link rel="stylesheet" href="<?= e(asset('assets/adminlte/plugins/fontawesome-free/css/all.min.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('assets/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('assets/adminlte/dist/css/adminlte.min.css')) ?>">
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">
