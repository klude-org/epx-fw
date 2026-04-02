<?php namespace fw\types;

\class_exists(\fw::class,false) AND throw new \Exception("Voilation: improper use of fw");

final class a extends \stdClass {
    
    public static function _() { static $i; return $i ?? ($i = new static()); }
    
    protected function __construct(){
        $this->autoload(true);
    }
    
    public function autoload(string|bool|callable $en = true){
        static $fn, $fn_default;
        if(\is_string($en)){
            $fn OR $this->autoload(true);
            ($fn)($en);
        } else if(\is_callable($fn) && $en === false){
            \spl_autoload_unregister($fn);
        } else if(\is_callable($en)){
            \is_callable($fn) AND \spl_autoload_unregister($fn);
            \spl_autoload_unregister($fn);
            \spl_autoload_register($fn = $en,true,false);
        } else if(!$fn && $en === true){            
            //to reset to default first ->autoload(false) then ->autoload(true)
            \spl_autoload_register($fn = ($fn_default ?? $fn_default = function($n){
                if(\str_starts_with($n, 'fw\\')){
                    $p = \strtr($n,'\\','/');
                    switch(count($j = \explode('/', $p, 5))){
                        case 4:{
                            [$we, $wd, $wo, $wp] = $j;
                            $wx = '';
                        } break;
                        case 5:{
                            [$we, $wd, $wo, $wp, $wx] = $j;
                            $wx = "/{$wx}";
                        } break;
                        default: return;
                    }
                    $wd = \strtr($wd,'_','-'); $wo = \strtr($wo,'_','-'); $wp = \strtr($wp,'_','-');
                    if(!($url = match($wd){
                        'github' => "https://raw.githubusercontent.com/{$wo}/{$wp}/main/epx/uno{$wx}",
                        default => (\is_file($f = \FW__DIR."/.local/api/url-builder/{$wd}-fn.php")
                            ? (include $f)
                            : null
                        ),  
                    })){
                        return;
                    }
                    $args = ($api_token = (
                                \is_file($f = ($r = \FW__ROOT_DIR."/.local/api/out/{$wd}/{$wo}")."~{$wp}-key.txt")
                                || \is_file($f ="{$r}-key.txt")
                            )
                            ? \file_get_contents($f)
                            : null
                        )
                        ? [
                            false,
                            \stream_context_create(["http" => [
                                "method" => "GET",
                                "header" => "Authorization: Bearer {$api_token}\r\n",
                                //'ignore_errors' => true
                            ]])
                        ] 
                        : []
                    ;
                    \set_error_handler(fn() => true);
                    if($contents = \file_get_contents("{$url}/".\urlencode('-#.php'), ...$args)){
                        $f_path = \FW__LIB_DIR."/{$p}/-#.php";
                    } else if($contents = \file_get_contents("{$url}".\urlencode('-#.php'), ...$args)) {
                        $f_path = \FW__LIB_DIR."/{$p}-#.php";
                    }
                    \restore_error_handler();
                    if($contents){
                        \is_dir($d = \dirname($f_path)) OR @mkdir($d,0777,true);
                        \file_put_contents($f_path,$contents);
                        include $f_path;
                    }
                } else if(\str_starts_with($n,'fw__')) {
                    global $_ALT;
                    [$wb,$wm,$wn] = \explode('/',$p = \strtr($n,'\\','/'),3) + ['',null,null]; //base|module|nestling
                    if(!$wm){ return; }
                    $x = \explode('--',\strtr($wb,'_','-'));
                    if(count($x) !== 4){ return; } //fw|domain|owner|project
                    $wv = $_ALT[$n] ?? null ?: 'main';
                    [$we,$wd,$wo,$wp] = $x;
                    if($d = $this->download($wd,$wo,$wp,$wv,$wm)){
                        if(\is_file($f_path = "{$d}/{$wn}/-#.php")
                            || \is_file($f_path = "{$d}/{$wn}-#.php")
                        ){
                            include $f_path;
                            return;
                        }
                    }
                }
            }),true,false);
        }
    }
        
    public function api_token($wd,$wo,$wp){
        if(!($token = $api_key["{$wo}/{$wp}"] ?? $api_key[$wo] ?? null)){
            if(\is_file($f = $kf1 =($r = \FW__DIR."/.local/api/out/{$wd}/{$wo}")."~{$wp}-key.txt")){
                $token = $api_key["{$wo}/{$wp}"] = \file_get_contents($f);
            } else if(\is_file($f = $kf2 ="{$r}-key.txt")){
                $token = $api_key[$wo] = \file_get_contents($f);
            } if(\is_file($f = $kf1 =($r = \FW__ROOT_DIR."/.local/api/out/{$wd}/{$wo}")."~{$wp}-key.txt")){
                $token = $api_key["{$wo}/{$wp}"] = \file_get_contents($f);
            } else if(\is_file($f = $kf2 ="{$r}-key.txt")){
                $token = $api_key[$wo] = \file_get_contents($f);
            }
        }
        return $token;
    }

    private function download($wd, $wo, $wp, $wv, $wm){
        static $api_key = [];
        $mm = \strtr($wm,'/','~');
        $mv = \strtr($wv,'/','~');
        $wj = \strtr("{$wd}/{$wo}/{$wp}/{$mv}",'_','-');
        $mod_d = \FW__LIB_DIR.'/'.($mj = \strtr("fw__{$wd}__{$wo}__{$wp}.{$mv}",'-','_')."/{$wm}");
        if(\is_dir($mod_d)) {
            return \str_replace('\\','/', \realpath($mod_d));
        }
        $url = "https://api.github.com/repos/".\trim("{$wo}/{$wp}/zipball/{$wv}",'/');
        $timestamp = \date('Y-md-Hi-s');
        $mod_x = \FW__DIR."/.tmp/zx-{$mm}@{$mj}-{$timestamp}";
        $zip_f = \FW__DIR."/.tmp/zz-{$mj}-{$timestamp}/code.zip";
        $zip_x = \FW__DIR."/.tmp/zz-{$mj}-{$timestamp}/extract";
        $curl_headers = [];
        if($api_token = $this->api_token($wd,$wo,$wp)){
            $curl_headers['Authorization'] = 'Authorization: Bearer '.$api_token;
        } 
        try{
            \is_dir($zip_d = \dirname($zip_f)) OR \mkdir($zip_d,0777,true);
            try{
                if(!($ch = \curl_init($url))){
                    throw new \Exception("Failed: Unable to initialze curl");
                };
                \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects
                \curl_setopt($ch, CURLOPT_USERAGENT, 'EpxPHP');      // Set User-Agent header to avoid 403
                \curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
                \curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                \curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                if($headers = \array_values($curl_headers)){
                    \curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                }                        
                if(!($fp = \fopen($zip_f, 'w'))){
                    throw new \Exception("Failed: Unable to open tempfile for writing");
                }; 
                \curl_setopt($ch, CURLOPT_FILE, $fp);
                \curl_exec($ch);
                if (\curl_errno($ch)) {
                    throw new \Exception("Failed: cURL Error: " . \curl_error($ch));
                }
                if(($h = curl_getinfo($ch, CURLINFO_HTTP_CODE)) != 200){
                    throw new \Exception("Failed: Server responded with an {$h} error for url: {$url}");
                }
            } finally {
                empty($fp) OR \fclose($fp);
                empty($ch) OR \curl_close($ch);
            }
            if(!\is_file($zip_f)){
                throw new \Exception("Failed: Download {$n}");
            }
            if (($zip = new \ZipArchive)->open($zip_f) !== true) {
                throw new \Exception("Failed: Unable to open ZIP file");
            }
            $sub_d = \substr($s = $zip->getNameIndex(0), 0, \strpos($s, '/'));
            $zip->extractTo($zip_x);
        } finally {
            $zip AND $zip->close();
        }
        
        try {
            $sysname = 'epx/fw-lib';
            if(!\is_dir($mod_s = "{$zip_x}/{$sub_d}/{$sysname}/{$wm}")){
                throw new \Exception("Failed: Unable to locate source '{$sysname}/{$wm}' in download from '{$wj}'");
            }
            \is_dir($d = \dirname($mod_x)) OR \mkdir($d, 0777, true);
            if(\is_dir($mod_d) && !\rename($mod_d, $mod_x)){
                throw new \Exception("Failed: Unable to backup existing: {$n}");
            }
            \is_dir($d = \dirname($mod_d)) OR \mkdir($d, 0777, true);
            if(!\rename($mod_s, $mod_d)){
                throw new \Exception("Failed: Unable to install: {$n}");
            }
            \file_put_contents("{$mod_d}/.installed.json",\json_encode(
                [
                    'installed_on' => $timestamp,
                    'url' => $url, 
                    'key' => $sub_d,
                    'backup' => $mod_x,
                    'source' => $wj,
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            ));
            return $mod_d;
        } finally {
            1 AND (function ($d){
                if(\is_dir($d)){
                    foreach(new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($d, \RecursiveDirectoryIterator::SKIP_DOTS)
                        , \RecursiveIteratorIterator::CHILD_FIRST
                    ) as $f) {
                        if ($f->isDir()){
                            \rmdir($f->getRealPath());
                        } else {
                            unlink($f->getRealPath());
                        }
                    }
                    \rmdir($d);
                }
            })($zip_d);
        }
    }
    
    private function resolve($expr, $remote, &$wj) {
        static $regex = '/^(?P<wm>.+?)@(?P<wj>(?P<wd>[^\/]+)\/(?P<wo>[^\/]+)\/(?P<wp>[^\/]+)\/(?P<wv>.+))$/';
        if($remote) {
            if(($expr[0]??'')=='/' || ($expr[1]??'')==':'){
                $this->fault_count++;
                $GLOBALS['_TRACE'][] = "!!! Warning: Invalid module request: '{$expr}' from '{$remote}'";
                return null;
            }
        } else {
            if(($expr[0]??'')=='/' || ($expr[1]??'')==':'){ 
                if(\is_dir($expr)){
                    return \str_replace('\\','/', $expr);    
                } else {
                    $this->fault_count++;
                    $GLOBALS['_TRACE'][] = "!!! Warning: Unable to resolve module: '{$expr}' from local";
                    return null;
                }
            } else if(\is_dir($d = \FW__SITE_DIR.'/--epx/'.$expr)){
                return \str_replace('\\','/', $d);
            } else if(\is_dir($d = \FW__LIB_DIR.'/'.$expr)){
                return \str_replace('\\','/', $d);
            }
        }
        try{
            if(preg_match($regex, $expr, $m)){
                $wj = $m['wj'];
                return $this->download($m['wd'], $m['wo'], $m['wp'], $m['wv'], $m['wm'], $m['wj']);
            } else if($remote && preg_match($regex, "{$expr}@{$remote}", $m)){
                $wj = $m['wj'];
                return $this->download($m['wd'], $m['wo'], $m['wp'], $m['wv'], $m['wm'], $m['wj']);
            } else if($remote){
                $this->fault_count++;
                $GLOBALS['_TRACE'][] = "!!! Warning: Unable to resolve module: '{$expr}' from '{$remote}'";
                return null;
            } else {
                $this->fault_count++;
                $GLOBALS['_TRACE'][] = "!!! Warning: Unable to resolve module: '{$expr}' from local";
                return null;
            }
        } catch (\Throwable $ex) {
            if($remote){
                $this->fault_count++;
                $GLOBALS['_TRACE'][] = "!!! Warning: Unable to resolve module: '{$expr}' from '{$remote}' :: {$ex->getMessage()}";
                return null;
            } else {
                $this->fault_count++;
                $GLOBALS['_TRACE'][] = "!!! Warning: Unable to resolve module: '{$expr}' from local :: {$ex->getMessage()}";
                return null;
            }
        }
    }
    
    private function include($iterator, $remote = null){
        foreach($iterator as $k => $v){
            if($dy = $this->resolve($k, $remote, $wj)){
                if(!isset($this->modules[$dy])){
                    $this->modules[$dy] = $v;
                    if(is_file($f = "{$dy}/.cfg.php")){
                        $GLOBALS['_TRACE'][] = "Including App config: '{$f}'";
                        $e = [];
                        (function($f,&$_){ include $f; })($f,$e);
                        $this->include($e['MODULES'] ?? [], $wj);
                    }
                }
            }
        }
    }
    
    public function build_env(){
        if(!\is_file($root_file = \FW__ROOT_DIR."/.local/http-root.php")){
            $root_dom = $_SERVER['HTTP_HOST'] ?? 'fw.local';
            \is_dir($d = \dirname($root_file)) OR \mkdir($d, 0777, true);
            \file_put_contents($root_file, <<<PHP
            <?php
            1 AND empty(\$_SERVER[\$n='FW__DOMAIN']) AND \$_SERVER[\$n]  = "{$root_dom}";
            1 AND !isset(\$_ENV['DB_HOSTNAME']) AND \$_ENV['DB_HOSTNAME'] = 'localhost';
            1 AND !isset(\$_ENV['DB_DATABASE']) AND \$_ENV['DB_DATABASE'] = 'default_db';
            1 AND !isset(\$_ENV['DB_USERNAME']) AND \$_ENV['DB_USERNAME'] = 'root';
            1 AND !isset(\$_ENV['DB_PASSWORD']) AND \$_ENV['DB_PASSWORD'] = 'pass';
            1 AND !isset(\$_ENV['DB_CHAR_SET']) AND \$_ENV['DB_CHAR_SET'] = 'utf8mb4';
            PHP);
        } 
        
        global $_;
        $this->fault_count = 0;
        $this->modules = [];
        $primary = [];
        !empty($_['FW__APP']) AND \is_string($_['FW__APP']) && $primary[$_['FW__APP']] = true;
        if(!\is_dir(\FW__SITE_DIR."/--epx") && \glob(\FW__SITE_DIR."/__*", GLOB_ONLYDIR)){
            $primary[\FW__SITE_DIR] = true;
        }
        $this->include([...$primary, ...$_['MODULES'] ?? []]);
        if(empty($this->modules)){
            $this->fault_count++;
            $GLOBALS['_TRACE'][] = "!!! Warning: No user modules";
        } 
        foreach(\explode(PATH_SEPARATOR,\get_include_path()) as $d){
            $this->modules[\str_replace('\\','/', $d)] ??= true;
        }
        if(empty(\array_filter($this->modules))){
            $this->fault_count++;
            $GLOBALS['_TRACE'][] = "!!! Warning: No modules in path";
        }
        $fault_count = $this->fault_count ?? null ?: '0';
        $cts = "\n  '".\implode("'.PATH_SEPARATOR.\n  '", \array_keys(\array_filter($this->modules)))."'";
        $fw_class = __CLASS__;
        $stamp = \date('Y-md-Hi-s');
        $trace = \implode(PHP_EOL."# ", $GLOBALS['_TRACE'] ?? []);
        $contents = <<<PHP
        <?php 
        # EPX FW Version 1.00 (C) Klude Pty Ltd.
        # Generated On: {$stamp}
        \$GLOBALS['FW__CLASS'] = '{$fw_class}';
        \$GLOBALS['FW__FAULTS'] = {$fault_count};
        \$GLOBALS['FW__PATH'] = {$cts}
        ;
        # TRACE:
        # {$trace}
        PHP;
        \is_dir($d = \dirname(\FW__ENV_PHP)) OR \mkdir($d,0777,true);
        \file_put_contents(
            \FW__ENV_PHP, 
            $contents,
            LOCK_EX // prevents race when testing and you have ton of simultaneous requests
        );
        
    }
    
}