<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title; ?></title>
    <meta name="description" content="<?= $description; ?>">
    <meta name="keywords" content="<?= $keywords; ?>">

	<!--[if lt IE 9]>
	<script src="/mightycms/<?= STATIC_DIR.'/js'; ?>/html5shiv.js"></script>
	<![endif]-->

    <link rel="stylesheet" type="text/css" href="/mightycms/<?= Versioning::auto(STATIC_DIR.'/css/styles.css'); ?>" />
    <!--[if lt IE 9]>
    <link rel="stylesheet" type="text/css" href="/mightycms/<?= Versioning::auto(STATIC_DIR.'/css/ie.css'); ?>" />
    <![endif]-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
    <link rel="shortcut icon" href="/mightycms/<?= Versioning::auto(STATIC_DIR.'/images/fav.ico'); ?>" />

    <script src="/mightycms/<?= Versioning::auto(STATIC_DIR."/js/jquery-1.10.2.min.js"); ?>"></script>
    <script src="/mightycms/<?= Versioning::auto(STATIC_DIR."/js/jquery-ui.js"); ?>"></script>


</head>
<body>