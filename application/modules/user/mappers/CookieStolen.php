<?php
/**
 * @copyright Ilch 2.0
 * @package ilch
 */

namespace Modules\User\Mappers;

class CookieStolen extends \Ilch\Mapper
{
    /**
     * Check if database contains a cookieStolen-message for an user.
     *
     * @param int userid
     * @return boolean
     */
    public function containsCookieStolen($userid) {
        $select = $this->db()->select('*');
        return (bool) $select->from('cookie_stolen')
            ->where(['userid' => $userid])
            ->execute()
            ->getNumRows();
    }

    /**
     * Add cookieStolen-message for an user.
     *
     * @param int userid
     */
    public function addCookieStolen($userid) {
        if ($this->containsCookieStolen($userid)) {
            return;
        }
        $insert = $this->db()->insert();
        return $insert->into('cookie_stolen')
            ->values(['userid' => $userid])
            ->execute();
    }

    /**
     * Delete the cookieStolen-message of an user.
     *
     * @param int userid
     */
    public function deleteCookieStolen($userid) {
        $delete = $this->db()->delete();
        return $delete->from('cookie_stolen')
            ->where(['userid' => $userid])
            ->execute();
    }
}
