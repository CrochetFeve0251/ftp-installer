<?php

namespace FtpInstaller;

use FtpInstaller\Exception\FtpException;
use FtpInstaller\Exception\ZipException;
use FtpPhp\FtpClient;
use GuzzleHttp\Client;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Class FtpInstaller
 * @package FtpInstaller
 */
class FtpInstaller
{
    /**
     *
     */
    protected const INSTALLER_NAME = 'installer.php';
    /**
     *
     */
    protected const ZIP_NAME = 'data.zip';
    /**
     * @var Configurations
     */
    protected $configurations;
    /**
     * @var Zipper
     */
    protected $zipper;
    protected $http_client;
    protected $configurations_writer;
    /**
     * @var FtpClient
     */
    protected $ftp_handler;

    /**
     * FtpInstaller constructor.
     * @param Configurations $configurations
     */
    public function __construct(Configurations $configurations)
    {
        $this->zipper = new Zipper();
        $this->ftp_handler = new FtpClient();
        $this->http_client=  new Client();
        $this->configurations_writer = new ConfigurationsWriter($configurations);
        $this->configurations = $configurations;
    }

    /**
     *
     */
    public function install(){
        $this->copyFile();
        $this->configurations_writer->rewriteFile();
        $this->zip();
        $this->transfer();
        $this->excecuteScript();
    }

    /**
     *
     */
    protected function zip(){
        try{
            $this->zipper->zipDir($this->configurations->getDirectory(),$this->configurations->getDirectory() . self::ZIP_NAME);
        }catch (\Exception $exception){
            throw new ZipException();
        }
    }

    protected function xcopy($src, $dest) {
        foreach (scandir($src) as $file) {
            if (!is_readable($src . '/' . $file)) continue;
            if (is_dir($src .'/' . $file) && ($file != '.') && ($file != '..') ) {
                mkdir($dest . '/' . $file);
                $this->xcopy($src . '/' . $file, $dest . '/' . $file);
            } else {
                copy($src . '/' . $file, $dest . '/' . $file);
            }
        }
    }

    protected function copyFile(){
        $this->xcopy($this->configurations->getDirectory(), '/tmp/site');
    }

    /**
     * @throws FtpException
     */
    protected function transfer(){
        try{
        $this->ftp_handler->connect($this->configurations->getFtpAddress());
        $this->ftp_handler->login($this->configurations->getFtpUsername(), $this->configurations->getFtpPassword());
        $this->ftp_handler->put($this->configurations->getDirectory() . self::ZIP_NAME, self::ZIP_NAME, $this->ftp_handler::BINARY);
        $this->ftp_handler->put(self::INSTALLER_NAME, self::INSTALLER_NAME, $this->ftp_handler::ASCII);
        }catch (\Exception $exception){
            throw new FtpException();
        }
    }

    /**
     *
     */
    protected function excecuteScript(){
        $this->http_client->request('GET', $this->configurations->getFtpAddress() . '/' . self::INSTALLER_NAME);
    }
}