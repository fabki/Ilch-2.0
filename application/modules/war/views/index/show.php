<link href="<?=$this->getModuleUrl('static/css/style.css') ?>" rel="stylesheet">

<?php
$gamesMapper = $this->get('gamesMapper');
$war = $this->get('war');
$group = $this->get('group');
$enemy = $this->get('enemy');
?>

<legend><?=$this->getTrans('warPlay') ?></legend>
<div class="row">
    <div class="center-text row no_margin">
        <div class="col-md-5">
            <img class="thumbnail img-responsive" src="<?=$this->getBaseUrl($group->getGroupImage()) ?>" alt="<?=$group->getGroupName() ?>">
            <h4><span><?=$this->escape($group->getGroupName()) ?></span></h4>
        </div>
        <div class="col-md-2 plays-vs">
            <h4>
                <span class="fa fa-arrow-circle-left "></span>
                <span>VS</span>
                <span class="fa fa-arrow-circle-right"></span>
            </h4>
        </div>
        <div class="col-md-5">
            <img class="thumbnail img-responsive" src="<?=$this->getBaseUrl($enemy->getEnemyImage()) ?>" alt="<?=$enemy->getEnemyName() ?>">
            <h4><span><?=$this->escape($enemy->getEnemyName()) ?></span></h4>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$this->getTrans('warStatusFrom') ?> <?=$war->getWarTime() ?></h3>
            </div>
            <div class="panel-body">
                <?php if ($war->getWarStatus() == '1'): ?>
                    <?=$this->getTrans('warStatusOpen') ?>
                <?php elseif ($war->getWarStatus() == '2'): ?>
                    <?=$this->getTrans('warStatusClose') ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$this->getTrans('warResult') ?></h3>
            </div>
            <div class="panel-body">
                <?php
                $games = $gamesMapper->getGamesByWarId($war->getId());
                $enemyPoints = '';
                $groupPoints = '';
                $class = '';
                $ergebniss = '';
                if ($games != '') {

                    foreach ($games as $game) {
                        $groupPoints += $game->getGroupPoints();
                        $enemyPoints += $game->getEnemyPoints();
                    }
                    if ($groupPoints > $enemyPoints) {
                        $class = 'class="war_win"';
                        $ergebniss = $this->getTrans('warWin');
                    }
                    if ($groupPoints < $enemyPoints) {
                        $class = 'class="war_lost"';
                        $ergebniss = $this->getTrans('warLost');
                    }
                    if ($groupPoints == $enemyPoints) {
                        $class = 'class="war_drawn"';
                        $ergebniss = $this->getTrans('warDrawn');
                    }
                }
                ?>
                <span <?=$class ?>><?=$groupPoints ?>:<?=$enemyPoints ?> <?=$ergebniss ?></span>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$this->getTrans('warMap') ?></h3>
            </div>
            <div class="panel-body">
                <?php if ($this->get('games') != ''): ?>
                    <?php foreach ($this->get('games') as $game): ?>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <span class=""><?=$this->escape($game->getMap()) ?></span>
                                <span class="pull-right"><?=$game->getGroupPoints() ?> : <?=$game->getEnemyPoints() ?></span>
                            </li>
                        </ul>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$this->getTrans('warReport') ?></h3>
            </div>
            <div class="panel-body">
                <?=nl2br($this->getHtmlFromBBCode($war->getWarReport())) ?>
            </div>
        </div>
    </div>
</div>
