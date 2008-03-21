<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains a class representing GPG keys
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; either version 2.1 of the
 * License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Crypt_GPG
 */

/**
 * Sub-key class definition
 */
require_once 'Crypt/GPG/SubKey.php';

/**
 * User id class definition
 */
require_once 'Crypt/GPG/UserId.php';

// {{{ class Crypt_GPG_Key

/**
 * A data class for GPG key information
 *
 * This class is used to store the results of the {@link Crypt_GPG::getKeys()}
 * method.
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 * @see       Crypt_GPG::getKeys()
 */
class Crypt_GPG_Key
{
    // {{{ class properties

    /**
     * The user ids associated with this key
     *
     * This is an array of {@link Crypt_GPG_UserId} objects.
     *
     * @var array
     *
     * @see Crypt_GPG_Key::addUserId()
     * @see Crypt_GPG_Key::getUserIds()
     */
    private $_user_ids = array();

    /**
     * The subkeys of this key
     *
     * This is an array of {@link Crypt_GPG_SubKey} objects.
     *
     * @var array
     *
     * @see Crypt_GPG_Key::addSubKey()
     * @see Crypt_GPG_Key::getSubKeys()
     */
    private $_sub_keys = array();

    // }}}
    // {{{ getSubKeys()

    /**
     * Gets the sub-keys of this key
     *
     * @return array the sub-keys of this key.
     *
     * @see Crypt_GPG_Key::addSubKey()
     */
    public function getSubKeys()
    {
        return $this->_sub_keys;
    }

    // }}}
    // {{{ getUserIds()

    /**
     * Gets the user ids of this key
     *
     * @return array the user ids of this key.
     *
     * @see Crypt_GPG_Key::addUserId()
     */
    public function getUserIds()
    {
        return $this->_user_ids;
    }

    // }}}
    // {{{ getPrimaryKey()

    /**
     * Gets the primary sub-key of this key
     *
     * The primary key is the first added sub-key.
     *
     * @return Crypt_GPG_SubKey the primary sub-key of this key.
     */
    public function getPrimaryKey()
    {
        $primary_key = null;
        if (count($this->_sub_keys) > 0) {
            $primary_key = $this->_sub_keys[0];
        }
        return $primary_key;
    }

    // }}}
    // {{{ canSign()

    /**
     * Gets whether or not this key can sign data
     *
     * This key can sign data if any sub-key of this key can sign data.
     *
     * @return boolean true if this key can sign data and false if this key
     *                 cannot sign data.
     */
    public function canSign()
    {
        $can_sign = false;
        foreach ($this->_sub_keys as $sub_key) {
            if ($sub_key->canSign()) {
                $can_sign = true;
                break;
            }
        }
        return $can_sign;
    }

    // }}}
    // {{{ canEncrypt()

    /**
     * Gets whether or not this key can encrypt data
     *
     * This key can encrypt data if any sub-key of this key can encrypt data.
     *
     * @return boolean true if this key can encrypt data and false if this
     *                 key cannot encrypt data.
     */
    public function canEncrypt()
    {
        $can_encrypt = false;
        foreach ($this->_sub_keys as $sub_key) {
            if ($sub_key->canEncrypt()) {
                $can_encrypt = true;
                break;
            }
        }
        return $can_encrypt;
    }

    // }}}
    // {{{ addSubKey()

    /**
     * Adds a sub-key to this key
     *
     * The first added sub-key will be the primary key of this key.
     *
     * @param Crypt_GPG_SubKey $sub_key the sub-key to add.
     *
     * @return void
     */
    public function addSubKey(Crypt_GPG_SubKey $sub_key)
    {
        $this->_sub_keys[] = $sub_key;
    }

    // }}}
    // {{{ addUserId()

    /**
     * Adds a user id to this key
     *
     * @param Crypt_GPG_UserId $user_id the user id to add.
     *
     * @return void
     */
    public function addUserId(Crypt_GPG_UserId $user_id)
    {
        $this->_user_ids[] = $user_id;
    }

    // }}}
}

// }}}

?>
