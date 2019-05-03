<?php

namespace FtpInstaller;

use FtpInstaller\Exception\FtpException;
use FtpInstaller\Exception\ZipException;
use GuzzleHttp\Client;

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
        $this->configurations = $configurations;
        $this->zipper = new Zipper();
        $this->ftp_handler = new FtpClient($this->configurations->getFtpAddress(), $this->configurations->getFtpUsername(), $this->configurations->getFtpPassword(), null, $this->configurations->getFtpFolder());
        $this->http_client=  new Client();
        $this->configurations_writer = new ConfigurationsWriter($configurations);
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
        $this->removeTmpFile();
    }

    /**
     *
     */
    protected function zip(){
        try{
            $this->zipper->zipDir('/tmp/site', '/tmp/site/' . self::ZIP_NAME);
        }catch (\Exception $exception){
            throw new ZipException();
        }
    }

    protected function xcopy($src, $dest) {
        if(! is_dir($dest))
            mkdir($dest);
        foreach (scandir($src) as $file) {
            if (!is_readable($src . '/' . $file)) continue;
            if (is_dir($src .'/' . $file) && ($file != '.') && ($file != '..') ) {
                mkdir($dest . '/' . $file);
                $this->xcopy($src . '/' . $file, $dest . '/' . $file);
            } else {
                if($file == '.' || $file == '..')
                    continue;
                copy($src . '/' . $file, $dest . '/' . $file);
            }
        }
    }

    protected function copyFile(){
        $this->xcopy($this->configurations->getDirectory(), '/tmp/site');
        copy(__DIR__ . '/installer.php', '/tmp/site/installer.php');
    }

    protected function removeTmpFile(){
        rmdir('/tmp/site');
    }

    /**
     * @throws FtpException
     */
    protected function transfer(){
        try{
            $this->ftp_handler = new FtpClient($this->configurations->getFtpAddress(), $this->configurations->getFtpUsername(), $this->configurations->getFtpPassword(), null, $this->configurations->getFtpFolder());
            $this->ftp_handler->put(self::ZIP_NAME, '/tmp/site/' . self::ZIP_NAME, $this->ftp_handler::BINARY);
            $this->ftp_handler->put(self::INSTALLER_NAME, '/tmp/site/' . self::INSTALLER_NAME, $this->ftp_handler::ASCII);
        }catch (\Exception $exception){
            throw new FtpException($exception->getMessage());
        }
    }

    /**
     *
     */
    protected function excecuteScript(){
        $this->http_client->request('GET', $this->configurations->getHttpAddress() . '/' . self::INSTALLER_NAME);
    }

    public function __destruct()
    {
        @$this->removeTmpFile();
    }
}