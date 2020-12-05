<a class="item logo" href="<?= base_url() ?>">
	<?= img([
		'src'   => 'assets/images/logo.svg',
		'alt'   => lang('Front.logo', [$company['name'] ?? 'Company']),
		'title' => $company['name'] ?? '',
	]) ?>
</a>
