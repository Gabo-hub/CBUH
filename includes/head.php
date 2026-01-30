<?php
// includes/head.php
if (!isset($page_title)) {
    $page_title = "CBUH - Sistema Académico";
}

// Adjust asset paths based on depth
$base_path = (isset($path_depth) && $path_depth == 1) ? "../" : "../../";
if (isset($is_root) && $is_root)
    $base_path = "";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $page_title; ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="<?php echo $base_path; ?>assets/img/libro.png">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/dashboard.css">
    <?php if (isset($extra_head))
        echo $extra_head; ?>
</head>

<body class="bg-cbuh-dark text-white font-body min-h-screen flex flex-col">