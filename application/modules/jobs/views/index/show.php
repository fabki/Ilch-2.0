<?php
$job = $this->get('job');
$jobs = $this->get('jobs');
?>

<style>
.briefcase {
    padding: 8px 8px 0 8px;
    border: 1px solid #e5e5e5;
}
</style>

<legend><?=$this->getTrans('menuJob') ?></legend>

<?php if ($job != ''): ?>
    <div class="row">
        <div class="col-lg-1">
            <i class="fa fa-briefcase fa-4x briefcase"></i>
        </div>
        <div class="col-lg-11" style="margin-bottom: 35px;">
            <legend><?=$this->escape($job->getTitle()) ?></legend>
            <?=$job->getText() ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($this->getUser()): ?>
    <legend><?=$this->getTrans('apply') ?></legend>

    <?php if (!empty($this->get('errors'))): ?>
        <div class="alert alert-danger" role="alert">
            <strong> <?=$this->getTrans('errorsOccured') ?>:</strong>
            <ul>
                <?php foreach ($this->get('errors') as $error): ?>
                    <li><?= $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="" class="form-horizontal" method="POST">
        <?=$this->getTokenField() ?>
        <div class="form-group <?=in_array('title', $this->get('errorFields')) ? 'has-error' : '' ?>">
            <label for="title" class="col-lg-3 control-label">
                <div class="text-left">
                    <?=$this->getTrans('applyAs') ?>:
                </div>
            </label>
            <div class="col-lg-3">
                <select class="form-control" id="title" name="title">
                    <?php foreach ($jobs as $jobs): ?>
                        <option value="<?=$jobs->getTitle() ?>" <?=($this->getRequest()->getParam('id') == $jobs->getId()) ? 'selected="selected"' : '' ?>>
                            <?=$this->escape($jobs->getTitle()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-12">
                <textarea class="form-control ckeditor"
                          id="ck_1"
                          name="text"
                          toolbar="ilch_bbcode"
                          rows="5"></textarea>
            </div>
        </div>
        <div class="col-lg-12 text-right">
            <?=$this->getSaveBar('apply', 'Apply') ?>
        </div>
    </form>
<?php endif; ?>
