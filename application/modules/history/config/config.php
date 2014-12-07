<?php
/**
 * @package ilch
 */

namespace Modules\History\Config;
defined('ACCESS') or die('no direct access');

class Config extends \Ilch\Config\Install
{
    public $config = array
    (
        'key' => 'history',
        'author' => 'Veldscholten Kevin',
        'icon_small' => 'history.png',
        'languages' => array
        (
            'de_DE' => array
            (
                'name' => 'Geschichte',
                'description' => 'Hier kann die Geschichte erstellt werden.',
            ),
            'en_EN' => array
            (
                'name' => 'History',
                'description' => 'Here you can create history.',
            ),
        )
    );

    public function install()
    {
        $this->db()->queryMulti($this->getInstallSql());
    }

    public function uninstall()
    {
        $this->db()->queryMulti('DROP TABLE `[prefix]_history`');
    }

    public function getInstallSql()
    {
        return 'CREATE TABLE IF NOT EXISTS `[prefix]_history` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `date` DATE NOT NULL,
                  `title` varchar(100) NOT NULL,
                  `text` MEDIUMTEXT NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;';
    }
}