<?php


namespace FtpInstaller;


use FtpInstaller\Exception\ZipException;
use ZipArchive;

class Zipper
{
    /**
     * Add files and sub-directories in a folder to zip file.
     * @param string $folder
     * @param ZipArchive $zipFile
     * @param int $exclusiveLength Number of text to be exclusived from the file path.
     */
    private static function folderToZip($folder, &$zipFile, $exclusiveLength)
    {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipFile->addEmptyDir($localPath);
                    self::folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }

    /**
     * Zip a folder (include itself).
     * Usage:
     *   HZip::zipDir('/path/to/sourceDir', '/path/to/out.zip');
     *
     * @param string $sourcePath Path of directory to be zip.
     * @param string $outZipPath Path of output zip file.
     */
    public static function zipDir($sourcePath, $outZipPath)
    {
        $pathInfo = pathInfo($sourcePath);
        $parentPath = $pathInfo['dirname'];
        $dirName = $pathInfo['basename'];

        $z = new ZipArchive();
        $z->open($outZipPath, \ZIPARCHIVE::CREATE);
        $z->addEmptyDir($dirName);
        self::folderToZip($sourcePath, $z, strlen("$parentPath/"));
        $z->close();
    }

    /**
     * @param $sourcePath
     * @param $outPath
     * @throws ZipException
     */
    public static function unzip($sourcePath, $outPath)
    {
        $z = new ZipArchive();
        if ($z->open($sourcePath) != true)
            throw new ZipException();
        $z->extractTo($outPath);
        $z->close();
    }
}

class DBInjector{
    protected $db;

    public function __construct(string $host, string $db, string $user, string $password)
    {
        $this->db = new \PDO('mysql:host=' . $host . ';dbname=' . $db, $user, $password,array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ));
    }

    public function importSQLFile(string $name){
        if(! is_file($name))
            throw new \Exception("The sql file is not valid");
        $fp = fopen($name, 'r');

        while (($line = stream_get_line($fp, 0, ';')) !== false)
            $this->db->exec($line);
        fclose($fp);

    }
}

Zipper::unzip('data.zip', '.');
