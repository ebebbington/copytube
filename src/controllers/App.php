<?php

class App {
    public __construct () {
        spl_autoload_register($this->autoLoadClasses);
        $this->setupErrorHandler();
    }

    private setupErrorHandler () {
        set_error_handler(function ($code, $text, $file, $line, $content) {
            $configPath = $_SERVER['DOCUMENT_ROOT']. '/copytube.ini';
            $config = parse_ini_file($configPath, true);
            $errorLogPath = $config['Logging']['error_log_file'];
            $fileSize = (filesize($errorLogPath) / 1000) / 1000; // in megabytes
            $fileSize > 10 ? $writeType = 'w' : $writeType = 'a';
            $errorArray   = ["\nError: $code", "\nDescription: $text", "\nFile with error: $file", "\nLine: $line"];
            $errorLogFile = fopen($errorLogPath, $writeType);
            for ($i = 0; $i < sizeof($errorArray); $i++) {
              fwrite($errorLogFile, $errorArray[ $i ]);
            }
            fclose($errorLogFile);
          
            return TRUE;
          }, E_ALL | E_STRICT);
    }

    private autoLoadClasses ($className) {
        return $className . '.php';
    }
}