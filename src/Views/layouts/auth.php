<?= doctype(); ?>
<html lang="en">

<head>
	<?= $this->include('partials/gtm_script') ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="description" content="<?= $metaDesc ?? '' ?>">
	<meta name="keywords" content="<?= $metaKeys ?? '' ?>">
	<meta name="author" content="<?= $metaAuthor ?? '' ?>">
	<link rel="icon" href="<?= base_url('favicon.ico') ?>">
	<title><?= isset($sections['tab']) ? ($tabPrefix ?? '') . $sections['tab'] . ($tabSuffix ?? '') : $company['name'] ?></title>
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
    <script {csp-script-nonce} src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js" integrity="sha512-UDJtJXfzfsiPPgnI5S1000FPLBHMhvzAMX15I+qG2E2OAzC9P1JzUwJOfnypXiOH7MRPaqzhPbBGDNNj7zBfoA==" crossorigin="anonymous"></script>
    <script {csp-script-nonce} src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv-printshiv.js" integrity="sha512-kvbHKHtR0/vWvOYkfthvwJx+/QhRY1TCF5Xd9keNhB1CENeowAW0RpbfyLWjN5+6483burKetEhjpkT0jNp1LQ==" crossorigin="anonymous"></script>
    <script {csp-script-nonce} src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js" integrity="sha512-qWVvreMuH9i0DrugcOtifxdtZVBBL0X75r9YweXsdCHtXUidlctw7NXg5KVP3ITPtqZ2S575A0wFkvgS2anqSA==" crossorigin="anonymous"></script>
    <![endif]-->
	<link {csp-style-nonce} rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>" type="text/css">
</head>

<body>
	<?= $this->include('partials/gtm_noscript') ?>
	<div class="wrapper">
		<?= $this->renderSection('form') ?>
	</div>

	<?= $this->include('scripts/jquery') ?>
	<?= $this->include('scripts/recaptcha') ?>
	<script {csp-script-nonce} src="<?= base_url('assets/js/auth.js') ?>" async defer></script>
</body>

</html>
