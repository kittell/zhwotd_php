<html>
<head>
	<?php require_once('config.php'); ?>
	<?php require_once(FUNCTIONS_DIR.'/wotd-formatting.php'); ?>
	<?php require_once(FUNCTIONS_DIR.'/display-manager.php'); ?>
	<?php $d = ($_GET['date']); ?>

	<title></title>
	<?php echo get_stylesheets('wotd'); ?>

</head>

<body>
<!-- Content -->
<?php echo format_wotd_entry($d, 0, ''); ?>

<!-- WOTD navigation -->
<?php echo display_wotd_navigation($d) ?>

</body>
</html>