<?php
$forumMapper = $this->get('forumMapper');
$posts = $this->get('posts');
$cat = $this->get('cat');
$topicpost = $this->get('post');
$readAccess = $this->get('readAccess');
$forum = $this->get('forum');
$adminAccess = null;
if ($this->getUser()) {
    $adminAccess = $this->getUser()->isAdmin();
    $userAccess = $this->get('userAccess');
}

$forumPrefix = $forumMapper->getForumByTopicId($topicpost->getId());
$prefix = '';
if ($forumPrefix->getPrefix() != '' AND $topicpost->getTopicPrefix() > 0) {
    $prefix = explode(',', $forumPrefix->getPrefix());
    array_unshift($prefix, '');

    foreach ($prefix as $key => $value) {
        if ($topicpost->getTopicPrefix() == $key) {
            $value = trim($value);
            $prefix = '['.$value.'] ';
        }
    }
}
?>

<link href="<?=$this->getModuleUrl('static/css/forum.css') ?>" rel="stylesheet">

<legend>
    <a href="<?=$this->getUrl(['controller' => 'index', 'action' => 'index']) ?>"><?=$this->getTrans('forum') ?></a> 
    <i class="forum fa fa-chevron-right"></i> <a href="<?=$this->getUrl(['controller' => 'showcat', 'action' => 'index', 'id' => $cat->getId()]) ?>"><?=$cat->getTitle() ?></a> 
    <i class="forum fa fa-chevron-right"></i> <a href="<?=$this->getUrl(['controller' => 'showtopics', 'action' => 'index', 'forumid' => $forum->getId()]) ?>"><?=$forum->getTitle() ?></a> 
    <i class="forum fa fa-chevron-right"></i> <?=$prefix.$topicpost->getTopicTitle() ?>
</legend>
<?php if (is_in_array($readAccess, explode(',', $forum->getReadAccess())) || $adminAccess == true): ?>
    <div id="forum">
        <div class="topic-actions">
            <?php if ($topicpost->getStatus() == 0): ?>
                <?php if ($this->getUser()): ?>
                    <?php if (is_in_array($readAccess, explode(',', $forum->getReplayAccess())) || $adminAccess == true): ?>
                        <div class="buttons">
                            <a href="<?=$this->getUrl(['controller' => 'newpost', 'action' => 'index','topicid' => $this->getRequest()->getParam('topicid')]) ?>" class="btn btn-labeled bgblue">
                                <span class="btn-label">
                                    <i class="fa fa-plus"></i>
                                </span><?=$this->getTrans('createNewPost') ?>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?php $_SESSION['redirect'] = $this->getRouter()->getQuery(); ?>
                    <div class="buttons">
                        <a href="<?=$this->getUrl(['module' => 'user', 'controller' => 'login', 'action' => 'index']) ?>" class="btn btn-labeled bgblue">
                            <span class="btn-label">
                                <i class="fa fa-user"></i>
                            </span><?=$this->getTrans('loginPost') ?>
                        </a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="btn btn-labeled bgblue">
                    <span class="btn-label">
                        <i class="fa fa-lock"></i>
                    </span><?=$this->getTrans('lockPost') ?>
                </div>
            <?php endif; ?>
            <?=$this->get('pagination')->getHtml($this, ['action' => 'index', 'topicid' => $this->getRequest()->getParam('topicid')]); ?>
        </div>
        <?php foreach ($posts as $post): ?>
            <?php $date = new \Ilch\Date($post->getDateCreated()) ?>
            <div id="<?=$post->getId() ?>" class="post bg1">
                <div class="delete">
                    <?php if ($this->getUser()): ?>
                        <?php if ($this->getUser()->isAdmin()): ?>
                            <p class="delete-post">
                                <a href="<?=$this->getUrl(['controller' => 'showposts', 'action' => 'delete', 'id' => $post->getId(), 'topicid' => $this->getRequest()->getParam('topicid'), 'forumid' => $forum->getId()]) ?>" class="btn btn-xs btn-labeled bgblue">
                                    <span class="btn-label">
                                        <i class="fa fa-trash"></i>
                                    </span><?=$this->getTrans('delete') ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="edit">
                    <?php if ($this->getUser()): ?>
                        <?php if ($this->getUser()->getId() == $post->getAutor()->getId() || $this->getUser()->isAdmin() || $userAccess->hasAccess('forum')): ?>
                            <p class="edit-post">
                                <a href="<?=$this->getUrl(['controller' => 'showposts', 'action' => 'edit', 'id' => $post->getId(), 'topicid' => $this->getRequest()->getParam('topicid')]) ?>" class="btn btn-xs btn-labeled bgblue">
                                    <span class="btn-label">
                                        <i class="fa fa-pencil"></i>
                                    </span><?=$this->getTrans('edit') ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="postbody">
                    <p class="author">
                        <a href="#<?=$post->getId() ?>"><img src="<?=$this->getModuleUrl('static/img/icon_post_target.png') ?>" alt="Post" title="Post" height="9" width="11"></a>
                        <?=$this->getTrans('by') ?>
                        <strong>
                            <a href="<?=$this->getUrl(['module' => 'user', 'controller' => 'profil', 'action' => 'index', 'user' => $post->getAutor()->getId()]) ?>" style="color: #AA0000;" class="username-coloured"><?=$this->escape($post->getAutor()->getName()) ?></a>
                        </strong>
                        »
                        <?=$date->format("d.m.Y H:i:s", true) ?>
                    </p>
                    <div class="content"><?=nl2br($this->getHtmlFromBBCode($post->getText())) ?></div>
                    <div class="signature"><?=nl2br($this->getHtmlFromBBCode($post->getAutor()->getSignature())) ?></div>
                </div>
                <dl class="postprofile">
                    <dt>
                        <a href="<?=$this->getUrl(['module' => 'user', 'controller' => 'profil', 'action' => 'index', 'user' => $post->getAutor()->getId()]) ?>"><img src="<?=$this->getBaseUrl($post->getAutor()->getAvatar()) ?>" alt="User avatar" height="100" width="100"></a>
                        <br>
                        <a href="<?=$this->getUrl(['module' => 'user', 'controller' => 'profil', 'action' => 'index', 'user' => $post->getAutor()->getId()]) ?>" style="color: #AA0000;" class="username-coloured">
                            <?=$this->escape($post->getAutor()->getName()) ?>
                        </a>
                    </dt>
                    <dd>
                        <?php foreach ($post->getAutor()->getGroups() as $group): ?>
                            <?=$group->getName() ?><br>
                        <?php endforeach; ?>
                    </dd>
                    <dd>&nbsp;</dd>
                    <dd><strong><?=$this->getTrans('posts') ?>:</strong> <?=$post->getAutorAllPost() ?></dd><dd><strong><?=$this->getTrans('joined') ?>:</strong> <?=$post->getAutor()->getDateCreated() ?></dd>
                </dl>
            </div>
        <?php endforeach; ?>
        <div class="topic-actions">
            <?php if ($topicpost->getStatus() == 0): ?>
                <?php if ($this->getUser()): ?>
                    <?php if (is_in_array($readAccess, explode(',', $forum->getReplayAccess())) || $adminAccess == true): ?>
                        <a href="<?=$this->getUrl(['controller' => 'newpost', 'action' => 'index','topicid' => $this->getRequest()->getParam('topicid')]) ?>" class="btn btn-labeled bgblue">
                            <span class="btn-label">
                                <i class="fa fa-plus"></i>
                            </span><?=$this->getTrans('createNewPost') ?>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php $_SESSION['redirect'] = $this->getRouter()->getQuery(); ?>
                    <a href="<?=$this->getUrl(['module' => 'user', 'controller' => 'login', 'action' => 'index']) ?>" class="btn btn-labeled bgblue">
                        <span class="btn-label">
                            <i class="fa fa-user"></i>
                        </span><?=$this->getTrans('loginPost') ?>
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <div class="btn btn-labeled bgblue">
                    <span class="btn-label">
                        <i class="fa fa-lock"></i>
                    </span><?=$this->getTrans('lockPost') ?>
                </div>
            <?php endif; ?>
            <?=$this->get('pagination')->getHtml($this, ['action' => 'index', 'topicid' => $this->getRequest()->getParam('topicid')]); ?>
        </div>
    </div>
<?php else: ?>
    <?php
    header("location: ".$this->getUrl(['controller' => 'index', 'action' => 'index', 'access' => 'noaccess']));
    exit;
    ?>
<?php endif; ?>

<script>
$(document).ready(function() {
    $('a[href^="#"]').on('click',function (e) {
        e.preventDefault();

        var target = this.hash;
        var $target = $(target);
    });
});
</script>
