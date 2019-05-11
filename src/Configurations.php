<?php


namespace FtpInstaller;


/**
 * Class Configurations configurations for install to the server
 * @package FtpInstaller
 */
class Configurations
{
    /**
     * @return string
     */
    public function getHttpAddress(): string
    {
        return $this->http_address;
    }
    /**
     * @var string address of the ftp server
     */
    protected $ftp_address;
    /**
     * @var string username for the ftp server
     */
    protected $ftp_username;
    /**
     * @var string string password for the ftp server
     */
    protected $ftp_password;
    /**
     * @var string name of the database
     */
    protected $database_name;
    /**
     * @var string address from the database server
     */
    protected $database_address;
    /**
     * @var string username for the database server
     */
    protected $database_username;
    /**
     * @var string password for the database server
     */
    protected $database_password;
    /**
     * @var string directory where the software is
     */
    protected $directory;

    protected $http_address;

    protected $ftp_folder;

    /**
     * Configurations constructor.
     * @param $ftp_address string address of the ftp server
     * @param $ftp_username string username for the ftp server
     * @param $ftp_password string password for the ftp server
     * @param $database_name string name of the database
     * @param $database_address string address from the database server
     * @param $database_username string username for the database server
     * @param $database_password string password for the database server
     * @param $directory string directory where the software is
     */
    public function __construct(string $ftp_address, string $ftp_username, string $ftp_password, string $ftp_folder, string $database_name, string $database_address, string $database_username, string $database_password, string $directory, string $http_address)
    {
        $this->ftp_address = $ftp_address;
        $this->ftp_username = $ftp_username;
        $this->ftp_password = $ftp_password;
        $this->ftp_folder = $ftp_folder;
        $this->database_name = $database_name;
        $this->database_address = $database_address;
        $this->database_username = $database_username;
        $this->database_password = $database_password;
        $this->directory = $directory;
        $this->http_address = $http_address;
    }

    /**
     * @return string
     */
    public function getFtpFolder()
    {
        return $this->ftp_folder;
    }

    /**
     * @return string address of the ftp server
     */
    public function getFtpAddress() : string
    {
        return $this->ftp_address;
    }

    /**
     * @return string username for the ftp server
     */
    public function getFtpUsername() : string
    {
        return $this->ftp_username;
    }

    /**
     * @return string password for the ftp server
     */
    public function getFtpPassword() : string
    {
        return $this->ftp_password;
    }

    /**
     * @return string name of the database
     */
    public function getDatabaseName() : string
    {
        return $this->database_name;
    }

    /**
     * @return string address of the database server
     */
    public function getDatabaseAddress() : string
    {
        return $this->database_address;
    }

    /**
     * @return string username of the database server
     */
    public function getDatabaseUsername() : string
    {
        return $this->database_username;
    }

    /**
     * @return string password of the database server
     */
    public function getDatabasePassword() : string
    {
        return $this->database_password;
    }

    /**
     * @return string directory where the software is
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }
}