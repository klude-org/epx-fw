<?php //260401
########################################################################################################################
#region 
    /* 
                                               EPX-FW-INDEX
    PROVIDER : KLUDE PTY LTD
    PACKAGE  : EPX-FW
    AUTHOR   : BRIAN PINTO
    RELEASED : 2026-04-01
    
    Copyright (c) 2017-2026 Klude Pty Ltd. https://klude.com.au

    The MIT License

    Permission is hereby granted, free of charge, to any person obtaining
    a copy of this software and associated documentation files (the
    "Software"), to deal in the Software without restriction, including
    without limitation the rights to use, copy, modify, merge, publish,
    distribute, sublicense, and/or sell copies of the Software, and to
    permit persons to whom the Software is furnished to do so, subject to
    the following conditions:

    The above copyright notice and this permission notice shall be
    included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
    MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
    LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
    OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
    WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
    
    */
#endregion
# ######################################################################################################################
# i'd like to be a tree - pilu (._.) // please keep this line in all versions - BP
# ######################################################################################################################
namespace { if($f = (function(){
    \defined('FW__PSTART') OR \define('FW__PSTART', \microtime(true));
    \ini_set($k = 'display_errors', $_SERVER['FW__DX_'.\strtoupper($k)] ?? 0);
    \ini_set($k = 'display_startup_errors', $_SERVER['FW__DX_'.\strtoupper($k)] ?? 1);
    \ini_set($k = 'error_reporting', $_SERVER['FW__DX_'.\strtoupper($k)] ?? E_ALL);
    \set_exception_handler(function($ex){
        if(empty($_SERVER['HTTP_HOST'])){
            echo "\e[91m\n"
                .$ex::class.": {$ex->getMessage()}\n"
                ."File: {$ex->getFile()}\n"
                ."Line: {$ex->getLine()}\n"
                ."\e[31m{$ex}\e[0m\n"
            ; 
            exit(1);
        } else if(str_contains($_SERVER['HTTP_ACCEPT'] ?? '','application/json')) {
            \header('Content-Type: application/json');
            \http_response_code(500);
            echo \json_encode([
                'status' => "error",
                'message' => $message->getMessage(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
        } else if(str_contains($_SERVER['HTTP_ACCEPT'] ?? '','text/html')) {
            if($_SERVER['FW__DX'] ?? 0){
                $ex_text = (string) $ex;
            } else {
                $ex_text = $ex->getMessage();
            }
            echo <<<HTML
                <style>
                    body{ background-color: #121212; color: #e0e0e0; 
                        font-family: sans-serif; margin: 0; padding: 20px;}
                    pre{ overflow:auto; color:red;border:1px solid red;
                        padding:5px; background-color: #1e1e1e; max-height: calc(100vh-25px); }
                    /* Scrollbar styles for WebKit (Chrome, Edge, Safari) */
                    ::-webkit-scrollbar { width: 12px; height: 12px;}
                    ::-webkit-scrollbar-track { background: #1e1e1e; }
                    ::-webkit-scrollbar-thumb { background-color: #555; 
                        border-radius: 6px; border: 2px solid #1e1e1e; }
                    ::-webkit-scrollbar-thumb:hover { background-color: #777; }
                    /* Firefox scrollbar (limited support) */
                    * { scrollbar-width: thin; scrollbar-color: #555 #1e1e1e;}
                </style>
                <pre>{$ex_text}</pre>
            HTML;
        } else {
            \header('Content-Type: application/json');
            \http_response_code(500);
            echo \json_encode([
                'status' => "error",
                'message' => $message->getMessage(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
        }
    });
    
    \set_error_handler(function($severity, $message, $file, $line){
        throw new \ErrorException(
            $message, 
            0,
            $severity, 
            $file, 
            $line
        );
    });
    
    \define('FW__DIR', \strtr(__DIR__, '\\','/'));
    \define('FW__SITE_DIR', \rtrim(\strtr($_SERVER['FW__SITE_DIR'] ?? (empty($_SERVER['HTTP_HOST'])
        ? realpath($_SERVER['FW__SITE_DIR'] ?? \getcwd())
        : realpath(\dirname($_SERVER['SCRIPT_FILENAME']))
    ),'\\','/'),'/'));
    \define('FW__ROOT_DIR', \strtr($_SERVER['DOCUMENT_ROOT'] ?? null ?: ($_SERVER['DOCUMENT_ROOT'] = (function(){
        for (
            $i=0, $dx=\getcwd(); 
            $dx && $i < 20 ; 
            $i++, $dx = (\strchr($dx, DIRECTORY_SEPARATOR) != DIRECTORY_SEPARATOR) ? \dirname($dx) : null
        ){ 
            if(\is_file("{$dx}/.local/http-root.php")){
                return $dx;
            }
        }
    })() ?: \dirname(__DIR__)), '\\','/'));
    \define('FW__IS_HTTP', !empty($_SERVER['HTTP_HOST']));
    \define('FW__IS_CLI', !empty($_SERVER['HTTP_HOST']));
    \define('FW__INTFC', $intfc = $_SERVER['FW__INTFC']
        ?? (empty($_SERVER['HTTP_HOST']) 
            ? 'cli'
            : $_SERVER['HTTP_X_REQUEST_INTERFACE'] ?? 'web'
        )
    );
    \define('FW__ENV_PHP', \FW__SITE_DIR."/.local/.fw-env-{$intfc}.php");
    \define('FW__CFG_PHP', \FW__SITE_DIR."/.fw-cfg.php");
    \define('FW__PHP_TSP_DEFAULTS', [
        'handler' => 'spl_autoload',
        'extensions' => \spl_autoload_extensions(),
        'path' =>  \get_include_path(),
    ]);
    \spl_autoload_extensions("-#{$intfc}.php,/-#{$intfc}.php,-#.php,/-#.php");
    \spl_autoload_register();
    \spl_autoload_register($my_fn = function($n) use(&$my_fn){
        if($n == \fw::class){
            $fw_class = $GLOBALS['FW__CLASS'] ?? \fw\types\a::class;
            if(\class_exists($fw_class,false)){
                \class_alias($fw_class, $n);
            } else if(\is_file($fw_php = \FW__DIR.\strtr("/{$fw_class}/-#.php",'\\','/'))) {
                include $fw_php;  
                \class_alias($fw_class, $n);
            } else {
                if(\str_starts_with($contents = \file_get_contents(
                    "https://raw.githubusercontent.com/klude-org/epx-fw/main/epx"
                        .\strtr("/{$fw_class}/",'\\','/').\urlencode('-#.php')
                    ,
                    false,
                    \stream_context_create(["http"=> [
                        'ignore_errors' => true
                    ]])
                ),'<?php')){
                    \is_dir($d = \dirname($fw_php)) OR \mkdir($d, 0777, true);
                    \file_put_contents($fw_php, $contents);
                    include $fw_php;
                    \class_alias($fw_class, $n);
                }
            }
        } else if(\str_starts_with($n,'fw')){
            \fw::_()->autoload($n);
        }
        \class_exists(\fw::class, false) AND \spl_autoload_unregister($my_fn);
    },true,false);

    global $_;
    (isset($_) && \is_array($_)) OR $_ = [];
    
    //ELOCK
    //  1: rebuild only if not existing  -- default (keeps accidental changes to the config safe)
    //  0: rebuild if needed
    // -1: rebuild always
    if(
        (($elock = $GLOBALS['FW__ELOCK'] ?? 0) == -1) 
        || (($elock == 1)
            ? (!\is_file($f = \FW__ENV_PHP))
            : (
                (($t = \is_file($f = \FW__ENV_PHP) ? \filemtime($f) : 0) 
                    <= ($t1 = \is_file($f = \FW__CFG_PHP) ? \filemtime($f) : 0)
                )
                || ($t <= ($t2 = \filemtime($_SERVER['SCRIPT_FILENAME'])))
            )
        )
    ){
        \is_file($f = \FW__CFG_PHP) AND include $f;
        $GLOBALS['FW__CLASS'] = \fw::class."\\types\\".($_['FW__TYPE'] ?? $_SERVER['FW__TYPE'] ?? 'a');
        (\FW__DIR == \FW__SITE_DIR) AND $_['FW__APP'] ??= '#-app-260401-01@github/klude-org/epx-fw/main';
        \fw::_()->build_env();
    }

    try{
        // prevents race when testing and you have ton of simultaneous requests when ELOCK is -1
        $handle = fopen(\FW__ENV_PHP, 'r');
        if (flock($handle, LOCK_SH)) {
            try {
                include \FW__ENV_PHP;
            } finally {
                \flock($handle, LOCK_UN);
            }
        } else {
            throw new \Exception("Cache error");
        }
    } finally {
        fclose($handle);
    }
    
    !empty($GLOBALS['FW__PATH']) AND \set_include_path($GLOBALS['FW__PATH']);
    
    if(($GLOBALS['FW__FAULTS'] ?? 0 )> 0){
        throw new \Exception('Environment Build Faults');
    }
    
    if($f = \stream_resolve_include_path('.start.php')){
        return include $f;
    } else {
        return function(){
            throw new \Exception('Invalid Start');
        };
    }    
    
})->bindTo((object)[])()){
    if($f instanceof \SplFileInfo){
        $f = include $f;
    }
    if(\is_callable($f)){
        $f();
    }
}; }