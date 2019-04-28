<?php


namespace FtpInstaller;


use FtpInstaller\Exception\WriteException;

class ConfigurationsWriter
{
    protected $configurations;

    public function __construct(Configurations $configurations)
    {
        $this->configurations = $configurations;
    }

    protected function rewriteDatabaseFile(){
        $fp = fopen($this->configurations->getDirectory() . '/application/config/database.php', 'r');
        if(! $fp)
            throw new WriteException();
        $fields = array(
            'hostname' => $this->configurations->getDatabaseAddress(),
            'username' => $this->configurations->getDatabaseUsername(),
            'password' => $this->configurations->getDatabasePassword(),
            'database' => $this->configurations->getDatabaseName()
        );
        $lines = array();
        while (($line = fgets($fp)) !== false)
            $lines[] = array_reduce(array_keys($fields), function ($line, $field) use ($fields){
                return preg_match("/'${field}' => '(.*)',/", $line) ? "'username' => '${fields[$field]}'," : $line;
            }, $line);
        fclose($fp);
        $fp = fopen('/tmp/site/database.php', 'w');
        array_walk($lines, function ($line) use ($fp){
            fwrite($fp, $line);
        });
        fclose($fp);
    }

    protected function rewriteInstaller(){
        $fp = fopen('/tmp/site/installer.php', 'a');
        if(! $fp)
            throw new WriteException();
        $host = $this->configurations->getDatabaseAddress();
        $db = $this->configurations->getDatabaseName();
        $user = $this->configurations->getDatabaseUsername();
        $password = $this->configurations->getDatabasePassword();
        fwrite($fp, "(new DBInjector('$host', '$db', '$user', '$password'))->importSQLFile('database.sql');");
        fclose($fp);

    }

    public function rewriteFile(){
        $this->rewriteDatabaseFile();
        $this->rewriteInstaller();
    }
}