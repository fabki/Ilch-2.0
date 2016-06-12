<?php
/**
 * @copyright Ilch 2.0
 * @package ilch
 */

namespace Modules\Awards\Controllers\Admin;

use Modules\Awards\Mappers\Awards as AwardsMapper;
use Modules\Awards\Models\Awards as AwardsModel;
use Modules\User\Mappers\User as UserMapper;

class Index extends \Ilch\Controller\Admin
{
    public function init()
    {
        $items = [
            [
                'name' => 'manage',
                'active' => false,
                'icon' => 'fa fa-th-list',
                'url' => $this->getLayout()->getUrl(['controller' => 'index', 'action' => 'index'])
            ],
            [
                'name' => 'add',
                'active' => false,
                'icon' => 'fa fa-plus-circle',
                'url' => $this->getLayout()->getUrl(['controller' => 'index', 'action' => 'treat'])
            ]
        ];

        if ($this->getRequest()->getActionName() == 'treat') {
            $items[1]['active'] = true;
        } else {
            $items[0]['active'] = true;
        }

        $this->getLayout()->addMenu
        (
            'menuAwards',
            $items
        );
    }

    public function indexAction()
    {
        $awardsMapper = new AwardsMapper();

        $this->getLayout()->getAdminHmenu()
                ->add($this->getTranslator()->trans('menuAwards'), ['action' => 'index'])
                ->add($this->getTranslator()->trans('manage'), ['action' => 'index']);

        if ($this->getRequest()->getPost('check_entries')) {
            if ($this->getRequest()->getPost('action') == 'delete') {
                foreach ($this->getRequest()->getPost('check_entries') as $awardsId) {
                    $awardsMapper->delete($awardsId);
                }
            }
        }

        $this->getView()->set('awards', $awardsMapper->getAwards());
    }

    public function treatAction()
    {
        $awardsMapper = new AwardsMapper();
        $userMapper = new UserMapper();

        if ($this->getRequest()->getParam('id')) {
            $this->getLayout()->getAdminHmenu()
                    ->add($this->getTranslator()->trans('menuAwards'), ['action' => 'index'])
                    ->add($this->getTranslator()->trans('edit'), ['action' => 'treat']);
        
            $this->getView()->set('awards', $awardsMapper->getAwardsById($this->getRequest()->getParam('id')));
        } else {
            $this->getLayout()->getAdminHmenu()
                    ->add($this->getTranslator()->trans('menuAwards'), ['action' => 'index'])
                    ->add($this->getTranslator()->trans('add'), ['action' => 'treat']);
        }

        if ($this->getRequest()->isPost()) {
            $model = new AwardsModel();

            if ($this->getRequest()->getParam('id')) {
                $model->setId($this->getRequest()->getParam('id'));
            }
            
            $date = new \Ilch\Date(trim($this->getRequest()->getPost('date')));
            $rank = trim($this->getRequest()->getPost('rank'));
            $utId = trim($this->getRequest()->getPost('utId'));
            $typ = trim($this->getRequest()->getPost('typ'));
            
            if (empty($date)) {
                $this->addMessage('missingDate', 'danger');
            } elseif (empty($rank)) {
                $this->addMessage('missingRank', 'danger');
            } elseif (empty($typ)) {
                $this->addMessage('missingTyp', 'danger');
            } elseif (empty($utId)) {
                $this->addMessage('missingUTId', 'danger');
            } else {
                $model->setDate($date);
                $model->setRank($rank);
                $model->setEvent($this->getRequest()->getPost('event'));
                $model->setURL($this->getRequest()->getPost('url'));
                $model->setUTId($utId);
                $model->setTyp($typ);
                $awardsMapper->save($model);
                
                $this->addMessage('saveSuccess');
                
                $this->redirect(['action' => 'index']);
            }
        }

        $this->getView()->set('users', $userMapper->getUserList(['confirmed' => 1]));
    }

    public function delAction()
    {
        if ($this->getRequest()->isSecure()) {
            $awardsMapper = new AwardsMapper();
            $awardsMapper->delete($this->getRequest()->getParam('id'));

            $this->addMessage('deleteSuccess');
        }

        $this->redirect(['action' => 'index']);
    }
}
