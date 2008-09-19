<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Encryption tests for the Crypt_GPG package.
 *
 * These tests require the PHPUnit 3.2 package to be installed. PHPUnit is
 * installable using PEAR. See the
 * {@link http://www.phpunit.de/pocket_guide/3.2/en/installation.html manual}
 * for detailed installation instructions.
 *
 * To run these tests, use:
 * <code>
 * $ phpunit EncryptTestCase
 * </code>
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
 * @copyright 2005-2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Crypt_GPG
 */

/**
 * Base test case.
 */
require_once 'TestCase.php';

/**
 * Tests encryption abilities of Crypt_GPG.
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2005-2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */
class EncryptTestCase extends TestCase
{
    // string
    // {{{ testEncrypt()

    /**
     * @group string
     */
    public function testEncrypt()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $keyId = 'first-keypair@example.com';
        $passphrase = 'test1';

        $this->gpg->addEncryptKey($keyId);
        $encryptedData = $this->gpg->encrypt($data);

        $this->gpg->addDecryptKey($keyId, $passphrase);
        $decryptedData = $this->gpg->decrypt($encryptedData);

        $this->assertEquals($data, $decryptedData);
    }

    // }}}
    // {{{ testEncryptDual()

    /**
     * @group string
     */
    public function testEncryptDual()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';

        $firstKeyId       = 'first-keypair@example.com';
        $firstPassphrase  = 'test1';
        $secondKeyId      = 'second-keypair@example.com';
        $secondPassphrase = 'test2';

        $this->gpg->addEncryptKey($firstKeyId);
        $this->gpg->addEncryptKey($secondKeyId);
        $encryptedData = $this->gpg->encrypt($data);

        $this->gpg->addDecryptKey($firstKeyId, $firstPassphrase);
        $this->gpg->addDecryptKey($secondKeyId, $secondPassphrase);
        $decryptedData = $this->gpg->decrypt($encryptedData);

        $this->assertEquals($data, $decryptedData);
    }

    // }}}
    // {{{ testEncryptKeyNotFoundException()

    /**
     * @expectedException Crypt_GPG_KeyNotFoundException
     *
     * @group string
     */
    public function testEncryptNotFoundException()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $keyId = 'non-existent-key@example.com';

        $this->gpg->addEncryptKey($keyId);

        $encryptedData = $this->gpg->encrypt($data);
    }

    // }}}

    // file
    // {{{ testEncryptFile()

    /**
     * @group file
     */
    public function testEncryptFile()
    {
        $expectedMd5Sum    = 'f96267d87551ee09bfcac16921e351c1';
        $originalFilename  = TestCase::DATADIR . '/testFileMedium.plain';
        $encryptedFilename = TestCase::TEMPDIR . '/testEncryptFile.asc';
        $decryptedFilename = TestCase::TEMPDIR . '/testEncryptFile.plain';

        $this->gpg->addEncryptKey('first-keypair@example.com');
        $this->gpg->encryptFile($originalFilename, $encryptedFilename);

        $this->gpg->addDecryptKey('first-keypair@example.com', 'test1');
        $this->gpg->decryptFile($encryptedFilename, $decryptedFilename);

        $md5Sum = $this->getMd5Sum($decryptedFilename);
        $this->assertEquals($expectedMd5Sum, $md5Sum);
    }

    // }}}
    // {{{ testEncryptFileDual()

    /**
     * @group file
     */
    public function testEncryptFileDual()
    {
        $expectedMd5Sum    = 'f96267d87551ee09bfcac16921e351c1';
        $originalFilename  = TestCase::DATADIR . '/testFileMedium.plain';
        $encryptedFilename = TestCase::TEMPDIR . '/testEncryptFile.asc';
        $decryptedFilename = TestCase::TEMPDIR . '/testEncryptFile.plain';

        $this->gpg->addEncryptKey('first-keypair@example.com');
        $this->gpg->addEncryptKey('second-keypair@example.com');
        $this->gpg->encryptFile($originalFilename, $encryptedFilename);

        $this->gpg->addDecryptKey('first-keypair@example.com', 'test1');
        $this->gpg->addEncryptKey('second-keypair@example.com', 'test2');
        $this->gpg->decryptFile($encryptedFilename, $decryptedFilename);

        $md5Sum = $this->getMd5Sum($decryptedFilename);
        $this->assertEquals($expectedMd5Sum, $md5Sum);
    }

    // }}}
    // {{{ testEncryptFileToString()

    /**
     * @group file
     */
    public function testEncryptFileToString()
    {
        $expectedData     = 'Hello, Alice! Goodbye, Bob!';
        $originalFilename = TestCase::DATADIR . '/testFileSmall.plain';

        $this->gpg->addEncryptKey('first-keypair@example.com');
        $encryptedData = $this->gpg->encryptFile($originalFilename);

        $this->gpg->addDecryptKey('first-keypair@example.com', 'test1');
        $decryptedData = $this->gpg->decrypt($encryptedData);

        $this->assertEquals($expectedData, $decryptedData);
    }

    // }}}
    // {{{ testEncryptFileFileException_input()

    /**
     * @group file
     *
     * @expectedException Crypt_GPG_FileException
     */
    public function testEncryptFileFileException_input()
    {
        // input file does not exist
        $filename = TestCase::DATADIR .
            '/testEncryptFileFileException_input.plain';

        $this->gpg->addEncryptKey('first-keypair@example.com');
        $this->gpg->encryptFile($filename);
    }

    // }}}
    // {{{ testEncryptFileFileException_output()

    /**
     * @group file
     *
     * @expectedException Crypt_GPG_FileException
     */
    public function testEncryptFileFileException_output()
    {
        // output file does not exist
        $inputFilename  = TestCase::DATADIR . '/testFileMedium.plain';
        $outputFilename = './non-existent' .
            '/testEncryptFileFileException_output.asc';

        $this->gpg->addEncryptKey('first-keypair@example.com');
        $this->gpg->encryptFile($inputFilename, $outputFilename);
    }

    // }}}
}

?>
