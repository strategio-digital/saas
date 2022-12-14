<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Helper;

class Path
{
    private static string $projectPath;
    
    public static function setProjectPath(string $projectPath): void
    {
        self::$projectPath = $projectPath;
    }
    
    public static function logDir(): string
    {
        return self::$projectPath . '/log';
    }
    
    public static function tempDir(): string
    {
        return self::$projectPath . '/temp';
    }
    
    public static function wwwDir(): string
    {
        return self::$projectPath . '/www';
    }
    
    public static function publicDir(): string
    {
        return self::$projectPath . '/public';
    }
    
    public static function srcDir(): string
    {
        return self::$projectPath . '/src';
    }
    
    public static function configDir(): string
    {
        return self::$projectPath . '/config';
    }
    
    public static function viewDir(): string
    {
        return self::$projectPath . '/view';
    }
    
    public static function saasVendorDir(): string
    {
        return __DIR__ . '/../../';
    }
}