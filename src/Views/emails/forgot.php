<?= $this->extend('layouts/email') ?>

<?= $this->section('greeting') ?>
<p><?= lang('Auth.email.greeting') ?> </p>
<?= $this->endSection() ?>

<?= $this->section('body') ?>
<p><?= lang('Auth.email.forgot.whatIsThis', [parse_url(base_url())['host']]) ?></p>
<p><?= lang('Auth.email.forgot.whatToDo') ?></p>
<p><?= lang('Auth.email.forgot.whatToDo') ?></p>
<p><a href="<?= base_url(route_to('reset-password')) . '?token=' . $hash ?>"><?= lang('Auth.email.forgot.target') ?></a></p>
<?= $this->endSection() ?>

<?= $this->section('signature') ?>
<p><?= lang('Auth.email.signature', [$company['name']]) ?></p>
<hr>
<p><?= lang('Auth.email.forgot.ignore') ?></p>
<?= $this->endSection() ?>
