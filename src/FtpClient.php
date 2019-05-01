<?php


namespace FtpInstaller;


use FtpPhp\Exception;

class FtpClient extends \FtpPhp\FtpClient
{
    public function __construct($host, $user, $password, $port = null, $path = null)
    {
        if (!extension_loaded('ftp')) {
            throw new Exception("PHP extension FTP is not loaded.");
        }
        if ($host) {
            $this->connect($host, isset($port) ? null : (int) $port);
            $this->login($user, $password);
            $this->pasv(true);
            if (isset($path)) {
                $this->chdir($path);
            }
        }
    }
}