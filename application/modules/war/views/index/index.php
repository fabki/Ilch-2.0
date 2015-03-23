<legend><?=$this->getTrans('menuGroups') ?></legend>
<?php if ($this->get('groups') != ''): ?>
    <div id="war_index">
        <?php foreach ($this->get('groups') as $group): ?>
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-xs-3 col-md-3 text-center">
                        <a href="<?=$this->getUrl(array('controller' => 'group', 'action' => 'show', 'id' => $group->getId())) ?>">
                            <img src="<?=$this->getBaseUrl($group->getGroupImage()) ?>" alt="<?=$group->getGroupName() ?>" class="thumbnail img-responsive" />
                        </a>
                    </div>
                    <div class="col-xs-9 col-md-9 section-box">
                        <h4>
                            <a href="<?=$this->getUrl(array('controller' => 'group', 'action' => 'show', 'id' => $group->getId()))?>"><?=$group->getGroupName() ?></a>
                        </h4>
                        <p>...</p>
                        <hr />
                        <div class="row rating-desc">
                            <div class="col-md-12">
                                <span>WIN</span>(36)<span class="separator">|</span>
                                <span>LOOS</span>100)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <?=$this->getTranslator()->trans('noGroup') ?>
<?php endif; ?>
<legend><?=$this->getTrans('warsOverview') ?></legend>
<?php if ($this->get('war') != ''): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <colgroup>
                <col class="col-lg-2">
                <col class="col-lg-2">
                <col class="col-lg-4">
                <col />
            </colgroup>
            <thead>
                <tr>
                    <th><?=$this->getTrans('enemyName') ?></th>
                    <th><?=$this->getTrans('groupName') ?></th>
                    <th><?=$this->getTrans('nextWarTime') ?></th>
                    <th><?=$this->getTrans('warStatus') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->get('war') as $war): ?>
                    <tr>
                        <td>
                            <?=$this->escape($war->getWarEnemy()) ?>
                        </td>
                        <td>
                            <?=$this->escape($war->getWarGroup()) ?>
                        </td>
                        <td>
                            <?=$this->escape($war->getWarTime()) ?>
                        </td>
                        <td>
                            <?php if ($this->escape($war->getWarStatus() == '1')): ?>
                                <?=$this->getTrans('warStatusOpen') ?>
                            <?php elseif ($this->escape($war->getWarStatus() == '2')): ?>
                                <?=$this->getTrans('warStatusClose') ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <?=$this->getTranslator()->trans('noWars') ?>
<?php endif; ?>

<style>
    .group-image{
        width: 100px;
        margin: 0px;
        padding: 0px;
    }
    #war_index{
        overflow: hidden;
    }
    #war_index .col-lg-12{
        border-width: 0px 0px 1px;
        border-style: none none solid;
        border-color: -moz-use-text-color -moz-use-text-color #E5E5E5;
        -moz-border-top-colors: none;
        -moz-border-right-colors: none;
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        border-image: none;
        padding-top: 5px;
        padding-bottom: 5px;
    }
    #war_index h3{
        margin-top: 0px;
    }
    #war_index .thumbnail{
        display: inline-block;
        float: left;
        margin: 5px 10px 5px 0px;
    }
</style>
