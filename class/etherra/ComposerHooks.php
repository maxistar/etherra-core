<?php
namespace etherra;

class ComposerHooks {
	protected static $ht_content = '';
	protected static $site_php_content = '';
	
	static function addHtContent($content){
		self::$ht_content .= $content;
	}
	
	static function addSitePhpContent($content){
		self::$site_php_content .= $content;
	}
	
	static function postInstall(){
		Site::initConstants();
		Site::loadDefaults();
		
		self::createFolders();
		Hooks::callHook('ComposerHooks::postInstall');
		self::createFiles();
	}
	static function createFolders(){
		self::createDir('/inc');
		self::createDir('/inc/config');
		self::createDir('/inc/cache');
		self::createDir('/inc/cache/logs');
		
	}
	
	static function createDir($name){
		$base = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
		if (is_dir($base.$name)){
			print 'folder '.$base.$name." already exists\n";
		}
		else {
			print 'create '.$base.$name."\n";
			mkdir($base.$name);
		}
	}
	
	static function createFiles(){
		$content = '<'.'?php'."\n".
		'include __DIR__."/../vendor/autoload.php";'."\n".
		''."\n".
		'etherra\\Site::init();';
		
		self::addSitePhpContent($content);
		
		$base = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
		file_put_contents($base.'/inc/site.php', self::$site_php_content);
		print 'create '.$base.'/inc/site.php'."\n";

		$content = '
<IfModule mod_alias.c>
    RedirectMatch 403 /vendor(/|$)
    RedirectMatch 403 /composer\.(json|lock)
</IfModule>
		';
		self::addHtContent($content);
		
		
		$base = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
		file_put_contents($base.'/.htaccess', self::$ht_content);
		print 'create '.$base.'/.htaccess'."\n";
	}
}