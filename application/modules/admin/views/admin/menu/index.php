<?php
use Ilch\View;
use Modules\Admin\Mappers\Menu as MenuMapper;
use Modules\Admin\Models\MenuItem;

/* @var View $this */

$menuItems = $this->get('menuItems');
$menuMapper = $this->get('menuMapper');
$pages = $this->get('pages');
$modules = $this->get('modules');
$boxes = $this->get('boxes');
$selfBoxes = $this->get('self_boxes');

function rec(MenuItem $item, MenuMapper $menuMapper, View $view) {
    $subItems = $menuMapper->getMenuItemsByParent($view->get('menu')->getId(), $item->getId());
    $class = 'mjs-nestedSortable-branch mjs-nestedSortable-expanded';

    if (empty($subItems)) {
        $class = 'mjs-nestedSortable-leaf';
    }

    if ($item->isBox()) {
        $class .= ' mjs-nestedSortable-no-nesting';
    }

    if ($item->getBoxId() > 0) {
        $boxKey = $item->getBoxId();
    } else {
        $boxKey = $item->getBoxKey();
    }

    echo '<li id="list_'.$item->getId().'" class="'.$class.'">';
    echo '<div><span class="disclose"><i class="fa fa-minus-circle"></i>
                    <input type="hidden" class="hidden_id" name="items['.$item->getId().'][id]" value="'.$item->getId().'" />
                    <input type="hidden" class="hidden_title" name="items['.$item->getId().'][title]" value="'.$view->escape($item->getTitle()).'" />
                    <input type="hidden" class="hidden_href" name="items['.$item->getId().'][href]" value="'.$item->getHref().'" />
                    <input type="hidden" class="hidden_type" name="items['.$item->getId().'][type]" value="'.$item->getType().'" />
                    <input type="hidden" class="hidden_siteid" name="items['.$item->getId().'][siteid]" value="'.$item->getSiteId().'" />
                    <input type="hidden" class="hidden_boxkey" name="items['.$item->getId().'][boxkey]" value="'.$boxKey.'" />
                    <input type="hidden" class="hidden_modulekey" name="items['.$item->getId().'][modulekey]" value="'.$item->getModuleKey().'" />
                    <input type="hidden" class="hidden_access" name="items['.$item->getId().'][access]" value="'.$item->getAccess().'" />
                    <span></span>
                </span><span class="title">'.$view->escape($item->getTitle()).'</span><span class="item_delete"><i class="fa fa-times-circle"></i></span><span class="item_edit"><i class="fa fa-edit"></i></span></div>';

    if (!empty($subItems)) {
        echo '<ol>';

        foreach ($subItems as $subItem) {
            rec($subItem, $menuMapper, $view);
        }

        echo '</ol>';
    }

    echo '</li>';
}
?>

<link rel="stylesheet" href="<?=$this->getModuleUrl('static/css/main.css') ?>">

<form class="form-horizontal" id="menuForm" method="POST" action="<?=$this->getUrl(['action' => $this->getRequest()->getActionName(), 'menu' => $this->get('menu')->getId()]) ?>">
    <?=$this->getTokenField() ?>
    <ul class="nav nav-tabs">
        <?php $iMenu = 1; ?>
        <?php foreach ($this->get('menus') as $menu): ?>
            <?php $active = ''; ?>

            <?php if ($menu->getId() == $this->get('menu')->getId()): ?>
                <?php $active = 'active'; ?>
            <?php endif; ?>
            <li class="<?=$active ?>">
                <a href="<?=$this->getUrl(['menu' => $menu->getId()]) ?>"><?=$this->getTrans('menu') ?> <?=$iMenu ?></a>
            </li>
            <?php $iMenu++; ?>
        <?php endforeach; ?>
        <li><a href="<?=$this->getUrl(['action' => 'add']) ?>">+</a></li>
    </ul>
    <br />
    <legend><?=$this->getTrans('menuChange') ?></legend>
    <div class="form-group">
        <div class="col-lg-6">
            <ol id="sortable" class="sortable">
                <?php if (!empty($menuItems)): ?>
                    <?php foreach ($menuItems as $item): ?>
                        <?php rec($item, $menuMapper, $this); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ol>
        </div>
        <div class="col-lg-1"></div>
        <div class="col-lg-5 changeBox">
            <input type="hidden" id="id" value="" />
            <div class="form-group">
                <label for="title" class="col-lg-3 control-label">
                    <?=$this->getTrans('itemTitle') ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" id="title" />
                </div>
            </div>
            <div class="form-group">
                <label for="type" class="col-lg-3 control-label">
                    <?=$this->getTrans('itemType') ?>
                </label>
                <div class="col-lg-6">
                    <select class="form-control" id="type">
                        <option value="<?= MenuItem::TYPE_MENU ?>"><?=$this->getTrans('menu') ?></option>
                        <optgroup>
                            <option value="<?= MenuItem::TYPE_EXTERNAL_LINK ?>"><?=$this->getTrans('externalLinking') ?></option>
                            <option value="<?= MenuItem::TYPE_PAGE_LINK ?>"><?=$this->getTrans('siteLinking') ?></option>
                            <option value="<?= MenuItem::TYPE_MODULE_LINK ?>"><?=$this->getTrans('moduleLinking') ?></option>
                        </optgroup>
                        <option value="<?= MenuItem::TYPE_BOX ?>"><?=$this->getTrans('itemTypeBox') ?></option>
                    </select>
                </div>
            </div>
            <div class="dyn"></div>
            <div class="form-group"><label for="assignedGroups" class="col-lg-3 control-label"><?=$this->getTrans('notVisible') ?></label>
                <div class="col-lg-6"><select class="chosen-select form-control" id="access" name="user[groups][]" data-placeholder="<?=$this->getTrans('selectAssignedGroups') ?>" multiple>
                        <?php foreach ($this->get('userGroupList') as $groupList): ?>
                            <option value="<?=$groupList->getId() ?>"><?=$groupList->getName() ?></option>
                        <?php endforeach; ?>
                    </select></div></div>

            <div class="actions">
                <input type="button" class="btn" id="menuItemAdd" value="<?=$this->getTrans('menuItemAdd') ?>">
            </div>
        </div>
    </div>
    <input type="hidden" id="hiddenMenu" name="hiddenMenu" value="" />
    <?=$this->getSaveBar('saveButton', null, 'deleteMenu') ?>
</form>

<script>
function resetBox() {
    $(':input','.changeBox')
    .not(':button, :submit, :reset, :hidden')
    .val('')
    .removeAttr('checked')
    .removeAttr('selected');

    $('#type').change();
    $('#access').val('');
    $('#access').trigger("chosen:updated");
}

$('.deleteMenu').on('click', function(event) {
    $('#modalButton').data('clickurl', $(this).data('clickurl'));
    $('#modalText').html($(this).data('modaltext'));
});

$('#modalButton').on('click', function(event) {
    window.location = $(this).data('clickurl');
});

$(document).ready
(
    function () {
        var itemId = 999;
        $('.sortable').nestedSortable ({
            forcePlaceholderSize: true,
            handle: 'div',
            helper: 'clone',
            items: 'li',
            opacity: .6,
            placeholder: 'placeholder',
            revert: 250,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div',
            maxLevels: 8,
            isTree: true,
            expandOnHover: 700,
            startCollapsed: false,
            stop: function(event, ui) {
                val = ui.item.find('input.hidden_type').val();

                if ((val == 4 || val == 0)) {
                    if (ui.item.closest('ol').closest('li').find('input.hidden_type:first').val() != undefined) {
                        event.preventDefault();
                    }
                } else {
                    if (ui.item.closest('ol').closest('li').find('input.hidden_type:first').val() == undefined) {
                        event.preventDefault();
                    }
                }
            }
        });

        $('.disclose').on('click', function () {
            $(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
            $(this).find('i').toggleClass('fa-minus-circle').toggleClass('fa-plus-circle');
        });

        $('#menuForm').submit (
            function () {
                $('#hiddenMenu').val(JSON.stringify($('.sortable').nestedSortable('toArray', {startDepthCount: 0})));
            }
        );

        var entityMap = {
            "&": "",
            "<": "",
            ">": "",
            '"': '',
            "'": '',
            "/": '',
            "(": '',
            ")": '',
            ";": '',
            " ": ' '
        };

        function escapeHtml(string) {
            return String(string).replace(/[&<>"'\/(); ]/g, function (s) {
                return entityMap[s];
            });
        };

        $('#menuForm').on('click', '#menuItemAdd', function () {

            var title = escapeHtml($('#title').val());

            if (title == '') {
                alert(<?=json_encode($this->getTrans('missingTitle')) ?>);
                return;
            }

            append = '#sortable';

            if ($('#type').val() != 0 && $('#type').val() != 4 && $('#menukey').val() != 0) {
                id = $('#menukey').val();

                if ($('#sortable #'+id+' ol').length > 0) {

                } else {
                    $('<ol></ol>').appendTo('#sortable #'+id);
                }

                if (!isNaN(id)) {
                    append = '#sortable #list_'+id+' ol';

                    if ($(append).length == 0) {
                        $('<ol></ol>').appendTo('#sortable #list_'+id);
                    }
                } else {
                    if ($(append).length == 0) {
                        $('<ol></ol>').appendTo('#sortable #'+id);
                    }
                    append = '#sortable #'+id+' ol';
                }

            }

            var modulKey = $('#modulekey').val();
            var boxkey = $('#boxkey').val();

            if (typeof modulKey == "undefined" && typeof boxkey != "undefined")
            {
                boxkeyParts = boxkey.split('_');
                modulKey = boxkeyParts[0];
            }

            $('<li id="tmp_'+itemId+'"><div><span class="disclose"><span>'
                    +'<input type="hidden" class="hidden_id" name="items[tmp_'+itemId+'][id]" value="tmp_'+itemId+'" />'
                    +'<input type="hidden" class="hidden_title" name="items[tmp_'+itemId+'][title]" value="'+title+'" />'
                    +'<input type="hidden" class="hidden_href" name="items[tmp_'+itemId+'][href]" value="'+$('#href').val()+'" />'
                    +'<input type="hidden" class="hidden_type" name="items[tmp_'+itemId+'][type]" value="'+$('#type').val()+'" />'
                    +'<input type="hidden" class="hidden_siteid" name="items[tmp_'+itemId+'][siteid]" value="'+$('#siteid').val()+'" />'
                    +'<input type="hidden" class="hidden_boxkey" name="items[tmp_'+itemId+'][boxkey]" value="'+$('#boxkey').val()+'" />'
                    +'<input type="hidden" class="hidden_modulekey" name="items[tmp_'+itemId+'][modulekey]" value="'+modulKey+'" />'
                    +'<input type="hidden" class="hidden_menukey" name="items[tmp_'+itemId+'][menukey]" value="'+$('#menukey').val()+'" />'
                    +'<input type="hidden" class="hidden_access" name="items[tmp_'+itemId+'][access]" value="'+$('#access').val()+'" />'
                    +'</span></span><span class="title">'+title+'</span><span class="item_delete"><i class="fa fa-times-circle"></i></span><span class="item_edit"><i class="fa fa-edit"></i></span></div></li>').appendTo(append);
            itemId++;
            resetBox();
            }
        );

        $('#menuForm').on('click', '#menuItemEdit', function () {
                var title = escapeHtml($('#title').val());
                if (title == '') {
                    alert(<?=json_encode($this->getTrans('missingTitle')) ?>);
                    return;
                }

                var modulKey = $('#modulekey').val();
                var boxkey = $('#boxkey').val();

                if (typeof modulKey == "undefined" && typeof boxkey != "undefined")
                {
                    boxkeyParts = boxkey.split('_');
                    modulKey = boxkeyParts[0];
                }

                $('#'+$('#id').val()).find('.title:first').text(title);
                $('#'+$('#id').val()).find('.hidden_title:first').val(title);
                $('#'+$('#id').val()).find('.hidden_href:first').val($('#href').val());
                $('#'+$('#id').val()).find('.hidden_type:first').val($('#type').val());
                $('#'+$('#id').val()).find('.hidden_siteid:first').val($('#siteid').val());
                $('#'+$('#id').val()).find('.hidden_modulekey:first').val(modulKey);
                $('#'+$('#id').val()).find('.hidden_boxkey:first').val($('#boxkey').val());
                $('#'+$('#id').val()).find('.hidden_menukey:first').val($('#menukey').val());
                $('#'+$('#id').val()).find('.hidden_access:first').val($('#access').val());
                resetBox();
            }
        );

        $('.sortable').on('click', '.item_delete', function() {
            $(this).closest('li').remove();
        });

        $('#menuForm').on('change', '#type', function() {
            var options = '';

            $('#sortable').find('li').each(function() {
                if ($(this).find('input.hidden_type:first').val() == 0) {
                    options += '<option value="'+$(this).find('input.hidden_id:first').val()+'">'+$(this).find('input.hidden_title:first').val()+'</option>';
                }
            });

            if (options == '' && ($(this).val() == '1' || $(this).val() == '2' || $(this).val() == '3')) {
                alert(<?=json_encode($this->getTrans('missingMenu')) ?>);
                $(this).val(0);
                return;
            }

            menuHtml = '<div class="form-group"><label for="href" class="col-lg-3 control-label"><?=$this->getTrans('labelMenu') ?></label>\n\
                        <div class="col-lg-6"><select class="form-control" id="menukey">'+options+'</select></div></div>';

            if ($(this).val() == '0') {
                $('.dyn').html('');
            } else if ($(this).val() == '1') {
                $('.dyn').html('<div class="form-group"><label for="href" class="col-lg-3 control-label"><?=$this->getTrans('address') ?></label>\n\
                                <div class="col-lg-6"><input type="text" class="form-control" id="href" value="http://" /></div></div>'+menuHtml);
            } else if ($(this).val() == '2') {
                 $('.dyn').html('<div class="form-group"><label for="href" class="col-lg-3 control-label"><?=$this->getTrans('page') ?></label>\n\
                                <div class="col-lg-6"><?php if (!empty($pages)) { echo '<select class="form-control" id="siteid">'; foreach ($pages as $page) { echo '<option value="'.$page->getId().'">'.$this->escape($page->getTitle()).'</option>';} echo '</select>'; } else { echo $this->getTrans('missingSite'); } ?></div></div>'+menuHtml);
            } else if ($(this).val() == '3') {
                $('.dyn').html('<div class="form-group"><label for="href" class="col-lg-3 control-label"><?=$this->getTrans('module') ?></label>\n\
                                <div class="col-lg-6"><?php if (!empty($modules)) { echo '<select class="form-control" id="modulekey">'; foreach ($modules as $module) { $content = $module->getContentForLocale($this->getTranslator()->getLocale()); echo '<option value="'.$module->getKey().'">'.$content['name'].'</option>';} echo '</select>'; } else { echo $this->getTrans('missingModule'); } ?></div></div>'+menuHtml);
            } else if ($(this).val() == '4') {
                $('.dyn').html('<div class="form-group"><label for="href" class="col-lg-3 control-label"><?=$this->getTrans('box') ?></label>\n\
                                <div class="col-lg-6"><?='<select class="form-control" id="boxkey">';
                foreach ($boxes as $box) { echo '<option value="'.$box->getModule().'_'.$box->getKey().'">'.$box->getName().'</option>'; } foreach ($selfBoxes as $box) { echo '<option value="'.$box->getId().'">self_'.$this->escape($box->getTitle()).'</option>';} echo '</select>'; ?></div></div>');
            }
        });

        $('#menuForm').on('click', '#menuItemEditCancel', function() {
            $('.actions').html('<input type="button" class="btn" id="menuItemAdd" value="<?=$this->getTrans('menuItemAdd') ?>">');
            resetBox();
        });

        $('.sortable').on('click', '.item_edit', function() {
            $('.actions').html('<input type="button" class="btn" id="menuItemEdit" value="<?=$this->getTrans('edit') ?>">\n\
                                <input type="button" class="btn" id="menuItemEditCancel" value="<?=$this->getTrans('cancel') ?>">');
           $('#title').val($(this).parent().find('.hidden_title').val());
           $('#type').val($(this).parent().find('.hidden_type').val());
           $('#id').val($(this).closest('li').attr('id'));
           $('#type').change();
           $('#href').val($(this).parent().find('.hidden_href').val());
           $('#siteid').val($(this).parent().find('.hidden_siteid').val());
           $('#boxkey').val($(this).parent().find('.hidden_boxkey').val());
           $('#modulekey').val($(this).parent().find('.hidden_modulekey').val());
           $('#menukey').val($(this).parent().find('.hidden_menukey').val());
           $('#access').val($(this).parent().find('.hidden_access').val());
           $.each($(this).parent().find('.hidden_access').val().split(","), function(index, element) {
               $('#access > option[value=' + element + ']').prop("selected", true);
           });
           $('#access').trigger("chosen:updated");
        });

        $('#access').chosen();
        $('#access_chosen').css('width', '100%'); // Workaround for chosen resize bug.
    }
);
</script>
