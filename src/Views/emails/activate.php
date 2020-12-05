<?= $this->extend('layouts/email') ?>

<?= $this->section('greeting') ?>
<p><?= lang('Auth.email.greeting') ?> </p>
<?= $this->endSection() ?>

<?= $this->section('body') ?>
<p><?= lang('Auth.email.activate.whatIsThis', [parse_url(base_url())['host']]) ?></p>
<p><?= lang('Auth.email.activate.whatToDo') ?></p>
<p><a href="<?= base_url(route_to('activate-account')) . '?token=' . $hash ?>"><?= lang('Auth.email.activate.target') ?></a></p>
<?= $this->endSection() ?>

<?= $this->section('signature') ?>
<p><?= lang('Auth.email.signature', [$company['name']]) ?></p>
<hr>
<p><?= lang('Auth.email.activate.ignore') ?></p>
<?= $this->endSection() ?>
