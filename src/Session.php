<?php

namespace DeLoachTech\Session;


use DeLoachTech\Database\AbstractDatabase;
use SessionHandlerInterface;

class Session implements SessionHandlerInterface
{
    private $db;


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