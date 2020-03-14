<html>
<head>
	<?php require_once('config.php'); ?>
	<?php require_once(FUNCTIONS_DIR.'/wotd-formatting.php'); ?>
	<?php require_once(FUNCTIONS_DIR.'/display-manager.php'); ?>
	<?php $today = date('Y-m-d'); ?>
	
	<title></title>
	<?php echo get_stylesheets('index'); ?>

</head>

<body>

<!-- Header -->
<?php display_header(); ?>

<!-- Today's WOTD -->
<?php echo format_wotd_entry($today, 0, 'front-page-today') ?>

<!-- Yesterday-s WOTD -->
<?php echo format_wotd_entry($today, -1, 'front-page-yesterday') ?>

<!-- Previous entries -->
<?php echo format_series_of_teasers($today, -2, 5, false, ', ', '', 'Previous entries: ') ?>

</body>
</html>