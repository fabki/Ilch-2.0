<link href="<?=$this->getModuleUrl('static/css/forum.css') ?>" rel="stylesheet">
<?php
$forumItems = $this->get('forumItems');
$readAccess = $this->get('groupIdsArray');
$edittopicitems[] = $this->get('edittopicitems');

function rec($item, $obj, $readAccess, $i)
{
    $subItems = $item->getSubItems();
    $adminAccess = null;
    if ($obj->getUser()) {
        $adminAccess = $obj->getUser()->isAdmin();
    }
    $subItemsFalse = false;
    if ($item->getType() === 0) {
        if (!empty($subItems)) {
            foreach ($subItems as $subItem) {
                if (is_in_array($readAccess, explode(',', $subItem->getReadAccess())) || $adminAccess == true) {
                     $subItemsFalse = true;
                }
            }
        }
    }
?>
    <?php if ($item->getType() === 0 && $subItemsFalse == true): ?>
        <optgroup label="<?=$item->getTitle() ?>"></optgroup>
    <?php endif; ?>

    <?php if (is_in_array($readAccess, explode(',', $item->getReadAccess())) || $adminAccess == true): ?>
        <?php if ($item->getType() != 0): ?>
            <?php $selected = ''; ?>
            <?php if ($item->getId() == $obj->getRequest()->getParam('forumid')): ?>
                <?php $selected = 'selected="selected"'; ?>
            <?php endif; ?>
                <option value="<?=$item->getId() ?>" <?=$selected?>><?=$item->getTitle() ?></option>
        <?php endif; ?>
    <?php endif; ?>
    <?php
    if (!empty($subItems) && $i == 0) {
        $i++;
        foreach ($subItems as $subItem) {
            rec($subItem, $obj, $readAccess, $i);
        }
    }
}
?>
<div id="forum" class="col-lg-12">
    <legend><?=$this->getTrans('topicMoveTo') ?></legend>
    <?php if (!empty($forumItems)): ?>
        <form class="form-horizontal" method="POST" action="<?=$this->getUrl(['action' => 'edittopic']) ?>">
            <?php echo $this->getTokenField(); ?>
            <div class="form-group">
                <label for="selectForum" class="col-lg-2 control-label">
                    <?=$this->getTrans('selectForum') ?>:
                </label>
                <div class="col-lg-8">
                    <select class="form-control" id="selectForum" name="edit">
                        <?php foreach ($forumItems as $item): ?>
                            <?php rec($item, $this, $readAccess, $i = null) ?>
                        <?php endforeach; ?>
                    </select>
                    <?php foreach ($edittopicitems as $editid): ?>
                        <input type="hidden" name="topicids[]" value="<?=$editid ?>">
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-offset-2 col-lg-10">
                <button type="submit" class="btn btn-labeled bgblue" name="edittopic" value="edittopic">
                    <?=$this->getTrans('move') ?>
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>
