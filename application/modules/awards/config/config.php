<?php
/**
 * @copyright Ilch 2.0
 * @package ilch
 */

namespace Modules\Awards\Config;

class Config extends \Ilch\Config\Install
{
    public $config = [
        'key' => 'awards',
        'version' => '1.0',
        'icon_small' => 'fa-trophy',
        'author' => 'Veldscholten, Kevin',
        'link' => 'http://ilch.de',
        'languages' => [
            'de_DE' => [
                'name' => 'Auszeichnungen',
                'description' => 'Hier können Auszeichnungen an Benutzer oder Teams verliehen werden.',
            ],
            'en_EN' => [
                'name' => 'Awards',
                'description' => 'Here you can award users or teams an award.',
            ],
        ],
        'ilchCore' => '2.0.0',
        'phpVersion' => '5.6'
    ];

    public function install()
    {
        $this->db()->queryMulti($this->getInstallSql());
    }

    public function uninstall()
    {
        $this->db()->queryMulti('DROP TABLE `[prefix]_awards`');
    }

    public function getInstallSql()
    {
        return 'CREATE TABLE IF NOT EXISTS `[prefix]_awards` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `date` DATE NOT NULL,
                  `rank` INT(11) NOT NULL,
                  `event` VARCHAR(100) NOT NULL,
                  `url` VARCHAR(150) NOT NULL,
                  `ut_id` INT(11) NOT NULL,
                  `typ` INT(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;';
    }

    public function getUpdate()
    {

    }
}
