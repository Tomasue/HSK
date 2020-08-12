<?php

namespace Models;

class Log {

    private static function getPath(): string
    {
        $path = realpath($_SERVER['DOCUMENT_ROOT'].'/../logs');
        $path .= DIRECTORY_SEPARATOR.date('Y_m_d').'.log';
        return $path;
    }

    public static function write(string $text) {
        file_put_contents(self::getPath(), $text."\n", FILE_APPEND);
    }

}