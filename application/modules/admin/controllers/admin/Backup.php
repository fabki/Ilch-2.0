<?php
/**
 * @copyright Ilch 2.0
 * @package ilch
 */

namespace Modules\Admin\Controllers\Admin;

use Modules\Admin\Mappers\Backup as BackupMapper;
use Modules\Admin\Models\Backup as BackupModel;
use Ilch\Config\File as File;
use Ifsnop\Mysqldump as IMysqldump;

class Backup extends \Ilch\Controller\Admin
{
    public function init()
    {
        $items = [
            [
                'name' => 'menuSettings',
                'active' => false,
                'icon' => 'fa fa-th-list',
                'url' => $this->getLayout()->getUrl(['controller' => 'settings', 'action' => 'index'])
            ],
            [
                'name' => 'menuMaintenance',
                'active' => false,
                'icon' => 'fa fa-wrench',
                'url' => $this->getLayout()->getUrl(['controller' => 'settings', 'action' => 'maintenance'])
            ],
            [
                'name' => 'menuCustomCSS',
                'active' => false,
                'icon' => 'fa fa-file-code-o',
                'url' => $this->getLayout()->getUrl(['controller' => 'settings', 'action' => 'customcss'])
            ],
            [
                'name' => 'menuBackup',
                'active' => false,
                'icon' => 'fa fa-download',
                'url' => $this->getLayout()->getUrl(['controller' => 'backup', 'action' => 'index']),
                [
                    'name' => 'add',
                    'active' => false,
                    'icon' => 'fa fa-plus-circle',
                    'url' => $this->getLayout()->getUrl(['controller' => 'backup', 'action' => 'create'])
                ],
            ],
            [
                'name' => 'menuUpdate',
                'active' => false,
                'icon' => 'fa fa-refresh',
                'url' => $this->getLayout()->getUrl(['controller' => 'settings', 'action' => 'update'])
            ]
        ];

        if ($this->getRequest()->getActionName() == 'create') {
            $items[3][0]['active'] = true; 
        } else {
            $items[3]['active'] = true; 
        }

        $this->getLayout()->addMenu
        (
            'menuSettings',
            $items
        );
    }

    public function indexAction()
    {
        $backupMapper = new BackupMapper();

        $this->getLayout()->getAdminHmenu()
                ->add($this->getTranslator()->trans('menuSettings'), ['action' => 'index'])
                ->add($this->getTranslator()->trans('menuBackup'), ['action' => 'backup']);

        if ($this->getRequest()->getPost('id')) {
            if ($this->getRequest()->getPost('action') == 'delete') {
                foreach ($this->getRequest()->getPost('id') as $backupId) {
                    $backupMapper->delete($backupId);
                }
            }
        }

        $this->getView()->set('backups', $backupMapper->getBackups());
    }

    public function createAction()
    {
        $this->getLayout()->getAdminHmenu()
                ->add($this->getTranslator()->trans('menuSettings'), ['action' => 'index'])
                ->add($this->getTranslator()->trans('menuBackup'), ['action' => 'backup'])
                ->add($this->getTranslator()->trans('add'), ['action' => 'createbackup']);

        if ($this->getRequest()->isPost()) {
            $date = new \Ilch\Date();
            $dbDate = $date->format('Y-m-d H-i-s', true);
            $filename = $date->format('Y-m-d_H-i-s', true).'_'.bin2hex(openssl_random_pseudo_bytes(32));

            $fileConfig = new File();
            $fileConfig->loadConfigFromFile(CONFIG_PATH.'/config.php');
            $dbhost = $fileConfig->get('dbHost');
            $dbuser = $fileConfig->get('dbUser');
            $dbpassword = $fileConfig->get('dbPassword');
            $dbname = $fileConfig->get('dbName');
            $compressFile = '.sql';
            if ($this->getRequest()->getPost('compress') == 'gzip') {
                $compress = 'GZIP';
                $compressFile .= '.gz';
            } else {
                $compress = 'NONE';
            }
            $dumpfile = ROOT_PATH.'/backups/'.$dbname.'_'.$filename.$compressFile;
            if ($this->getRequest()->getPost('addDatabases') == 1) {
                $addDatabases = true;
            } else {
                $addDatabases = false;
            }
            if ($this->getRequest()->getPost('dropTable') == 1) {
                $dropTable = true;
            } else {
                $dropTable = false;
            }
            if ($this->getRequest()->getPost('skipComments') == 1) {
                $skipComments = false;
            } else {
                $skipComments = true;
            }

            try {
                $dumpSettings = [
                    'compress' => $compress,
                    'add-drop-table' => $dropTable,
                    'lock-tables' => false,
                    'add-locks' => false,
                    'no-autocommit' => false,
                    'databases' => $addDatabases,
                    'skip-comments' => $skipComments
                ];

                $dump = new IMysqldump\Mysqldump('mysql:host='.$dbhost.';dbname='.$dbname,$dbuser,$dbpassword,$dumpSettings);
                $dump->start($dumpfile);

                $backupMapper = new BackupMapper();
                $backupModel = new BackupModel();

                $backupModel->setName($dbname.'_'.$filename.$compressFile);
                $backupModel->setFile('backups/'.$dbname.'_'.$filename.$compressFile);
                $backupModel->setDate($dbDate);
                $backupMapper->save($backupModel);

                $this->addMessage('saveSuccess');
                $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                echo 'mysqldump-php error: ' . $e->getMessage();
            }
        }
    }

    public function delAction()
    {
        if ($this->getRequest()->isSecure()) {
            $backupMapper = new BackupMapper();
            $backupMapper->delete($this->getRequest()->getParam('id'));

            $this->addMessage('deleteSuccess');
        }

        $this->redirect(['action' => 'index']);
    }
}
