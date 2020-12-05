<?= $this->extend($auth->viewLayout) ?>

<?= $this->section('form') ?>
<?= form_open(route_to('login')) ?>
<?= $this->include('partials/auth/logo') ?>
<h1 class="item"><?= lang('Auth.form.login.title') ?></h1>
<?= $this->include('partials/auth/session_messages') ?>
<?php if ($auth->validFields === ['email']) : ?>
	<div class="item">
		<?= form_input([
			'name'     => 'email',
			'id'       => 'email',
			'type'     => 'email',
			'class'    => session('errors.email') ? 'is-invalid' : '',
			'required' => 'required',
		]) ?>
		<?= form_label(lang('Auth.form.email'), 'email') ?>
		<div class="badges">
			<?php if (!empty(session('errors.email'))) : ?>
				<div class="icon alert" tabindex="0">
					<p class="bubble"><?= session('errors.email') ?></p>
				</div>
			<?php endif ?>
		</div>
	</div>
<?php else : ?>
	<div class="item">
		<?= form_input([
			'name'     => 'login',
			'id'       => 'login',
			'type'     => 'text',
			'class'    => session('errors.login') ? 'is-invalid' : '',
			'required' => 'required',
		]) ?>
		<?= form_label(lang('Auth.form.emailOrUsername'), 'login') ?>
		<div class="badges">
			<?php if (!empty(session('errors.login'))) : ?>
				<div class="icon alert" tabindex="0">
					<p class="bubble"><?= session('errors.login') ?></p>
				</div>
			<?php endif ?>
		</div>
	</div>
<?php endif ?>
<div class="item">
	<?= form_input([
		'name'     => 'password',
		'id'       => 'password',
		'type'     => 'password',
		'class'    => session('errors.password') ? 'is-invalid' : '',
		'required' => 'required',
	]) ?>
	<?= form_label(lang('Auth.form.password'), 'password') ?>
	<div class="badges">
		<?php if (!empty(session('errors.password'))) : ?>
			<div class="icon alert" tabindex="0">
				<p class="bubble"><?= session('errors.password') ?></p>
			</div>
		<?php endif ?>
	</div>
</div>
<?php if ($auth->allowRemembering) : ?>
	<div class="item checkbox">
		<?= form_checkbox([
			'name'    => 'remember',
			'id'      => 'remember',
			'class'   => session('errors.password') ? 'is-invalid' : '',
			'checked' => old('remember') ?? false,
		]) ?>
		<?= form_label(lang('Auth.form.rememberMe'), 'remember') ?>
	</div>
<?php endif ?>
<?php if (isset($keys['recaptcha'])) : ?>
	<?= form_input([
		'type'         => 'hidden',
		'name'         => 'recaptcha',
		'class'        => 'recaptcha',
		'data-sitekey' => $keys['recaptcha'] ?? '',
	]) ?>
<?php endif ?>
<?= form_button([
	'name'  => 'submit',
	'type'  => 'submit',
	'class' => 'item g-recaptcha',
], lang('Auth.form.login.action')) ?>
<ul class="item">
	<?php if ($auth->allowRegistration) : ?>
		<li><?= anchor(route_to('register'), lang('Auth.form.needAnAccount')) ?></li>
	<?php endif; ?>
	<?php if ($auth->activeResetter) : ?>
		<li><?= anchor(route_to('forgot'), lang('Auth.form.forgotYourPassword')) ?></li>
	<?php endif; ?>
</ul>
<?= form_close() ?>
<?= $this->endSection() ?>
