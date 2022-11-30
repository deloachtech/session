<?php
/*
 * This file is part of the deloachtech/session package.
 *
 * Copyright (c) DeLoach Tech
 * https://deloachtech.com
 *
 * This source code is protected under international copyright law. All
 * rights reserved and protected by the copyright holders. This file is
 * confidential and only available to authorized individuals with the
 * permission of the copyright holder. If you encounter this file, and do
 * not have permission, please contact the copyright holder and delete
 * this file. Unauthorized copying of this file, via any medium is strictly
 * prohibited.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DeLoachTech\Session;


use DeLoachTech\Database\AbstractDatabase;
use SessionHandlerInterface;

class Session implements SessionHandlerInterface
{
    protected $db;


    public function __construct(AbstractDatabase $db, string $name = 'PHPSESSION')
    {
        $this->db = $db;
        session_name($name);
        session_set_save_handler(
            [$this, "open"],
            [$this, "close"],
            [$this, "read"],
            [$this, "write"],
            [$this, "destroy"],
            [$this, "gc"]
        );
        register_shutdown_function('session_write_close');
        session_start();

    }


    public function close(): bool
    {
        return true;
    }


    public function destroy($id): bool
    {
        $this->db->query("delete from sessions where id = ? limit 1", [$id]);
        return true;
    }


    public function gc($max_lifetime): bool
    {
        $old = time() - $max_lifetime;
        $this->db->query("delete from sessions where access <= ?", [$old]);
        return true;
    }


    public function open($path, $name): bool
    {
        //$this->read($name);
        return true;
    }


    public function read($id)
    {
        if ($session = $this->db->query("select `data` from sessions where id = ? limit 1", [$id])) {
            return $session['data'];
        }
        return "";
    }


    public function write($id, $data): bool
    {
        $this->db->query("replace into sessions (id, access, `data`) values (?, ?, ?)", [$id, time(), $data]);
        return true;
    }
}