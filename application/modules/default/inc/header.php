<header class="main">
	<div class="logo">
		<a href="/mightycms/dashboard" title="mightyCMS developed by @mobinzk">
			<img src="<?= '/mightycms/'.STATIC_DIR.'/images/small-logo.png' ?>" alt="mightyCMS">
			<span>mightyCMS</span>
		</a>
	</div>

	<nav class="utility">
		<ul>
			<li>
				<a href="/mightycms/file-manager">File Manager</a>
			</li>
			<li>
				<a href="/mightycms/users">Users</a>
			</li>
			<li>
				<a href="/mightycms/snippets">Snippets / Settings</a>
			</li>
			<li class="logout">
				<a href="/mightycms/logout">Logout</a>
			</li>
		</ul>
	</nav>
</header>

<?php if ($response->ui_alert){ ?>
	<?php $alert = $response->ui_alert; ?>
	<div class="uim-alert uim-<?= $alert->type ?>">
		<span class="icon"></span>
		<div class="heading"><?= $alert->heading ?></div>
		<div class="message"><?= $alert->message ?></div>
		<span class="close"></span>
	</div>
<?php } ?>

<?php
	$request 	= Mighty::request();
	$mighty_nav = dbi::getAll("SELECT * FROM `mighty_nav` WHERE `published` = '1'");
 ?>
<section class="container">
	<nav class="main">
		<ul>
			<?php foreach ($mighty_nav as $nav) { ?>
			<li <?= ($nav->url == Mighty::request()->urlParts[1]) ? 'class="selected"' : ''?>>
				<a href="/mightycms/<?= $nav->url ?>"><?= $nav->name ?></a>
			</li>
			<?php } ?>
		</ul>
	</nav>

<h1 class="heading">
	<?= Mighty::breadcrumbs($breadcrumbs)?>
</h1>

	<?php if ($template && count($template->getSections()) > 1){ ?>
		<ul class="sub-nav">
			<?php foreach ($template->getSections() as $s){ ?>
			<li <?= ($default_section == $s->name ? 'class="selected"' : ''); ?>>
				<a data-target="<?= $s->name; ?>" href="#"><?= $s->label ?></a>
			</li>
			<?php } ?>
		</ul>
	<?php } ?>

<div class="contents">