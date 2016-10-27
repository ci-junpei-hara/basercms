<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Lib
 * @since			baserCMS v 3.0.7
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcAuthComponent', 'Controller/Component');
App::uses('Router', 'Routing');

/**
 * Class BcUtil
 *
 * @package Baser.Lib
 */
class BcUtil extends Object {

/**
 * 管理システムかチェック
 * 
 * 《注意》by ryuring
 * 処理の内容にCakeRequest や、Router::parse() を使おうとしたが、
 * Router::parse() を利用すると、Routing情報が書き換えられてしまうので利用できない。
 * Router::reload() や、Router::setRequestInfo() で調整しようとしたがうまくいかなかった。
 * 
 * @return boolean
 */
	public static function isAdminSystem($url = null) {
		if(!$url) {
			$request = Router::getRequest(true);
			if($request) {
				$url = $request->url;
			} else {
				return false;
			}
		}
		$adminPrefix = Configure::read('Routing.prefixes.0');
		return (boolean)(preg_match('/^(|\/)' . $adminPrefix . '\//', $url) || preg_match('/^(|\/)' . $adminPrefix . '$/', $url));
	}

/**
 * 管理ユーザーかチェック
 * 
 * @return boolean
 */
	public static function isAdminUser() {
		$user = self::loginUser('admin');
		if (empty($user['UserGroup']['name'])) {
			return false;
		}
		return ($user['UserGroup']['name'] == 'admins');
	}

/**
 * ログインユーザーのデータを取得する
 * 
 * @return array
 */
	public static function loginUser($prefix = 'admin') {
		$Session = new CakeSession();
		$sessionKey = BcUtil::authSessionKey($prefix);
		$user = $Session->read('Auth.' . $sessionKey);
		if (!$user) {
			if (!empty($_SESSION['Auth'][$sessionKey])) {
				$user = $_SESSION['Auth'][$sessionKey];
			}
		}
		return $user;
	}

/**
 * 認証用のキーを取得
 * 
 * @param string $prefix
 * @return mixed
 */
	public static function authSessionKey($prefix = 'admin') {
		return Configure::read('BcAuthPrefix.' . $prefix . '.sessionKey');
	}
	
/**
 * ログインしているユーザーのセッションキーを取得
 * 
 * @return string
 */
	public static function getLoginUserSessionKey() {
		list(, $sessionKey) = explode('.', BcAuthComponent::$sessionKey);
		return $sessionKey;
	}
/**
 * ログインしているユーザー名を取得
 * 
 * @return string
 */
	public static function loginUserName() {
		$user = self::loginUser();
		if (!empty($user['name'])) {
			return $user['name'];
		} else {
			return '';
		}
	}

/**
 * テーマ梱包プラグインのリストを取得する
 * 
 * @return array
 */
	public static function getCurrentThemesPlugins() {
		$theme = Configure::read('BcSite.theme');
		$path = BASER_THEMES . $theme . DS . 'Plugin';
		if(is_dir($path)) {
			$Folder = new Folder($path);
			$files = $Folder->read(true, true, false);
			if(!empty($files[0])) {
				return $files[0];
			}
		}
		return array();
	}
	
/**
 * スキーマ情報のパスを取得する
 * 
 * @param string $plugin プラグイン名
 * @return string Or false
 */
	public static function getSchemaPath($plugin = null) {
		
		if(!$plugin) {
			$plugin = 'Core';
		} else {
			$plugin = Inflector::camelize($plugin);
		}
		
		if($plugin == 'Core') {
			return BASER_CONFIGS . 'Schema';
		}
		
		$paths = App::path('Plugin');
		foreach ($paths as $path) {
			$_path = $path . $plugin . DS . 'Config' . DS . 'Schema';
			if (is_dir($_path)) {
				return $_path;
			}
		}
		
		return false;
		
	}
	
/**
 * 初期データのパスを取得する
 * 
 * 初期データのフォルダは アンダースコア区切り推奨
 * 
 * @param string $plugin プラグイン名
 * @param string $theme テーマ名
 * @param string $pattern 初期データの類型
 * @return string Or false
 */
	public static function getDefaultDataPath($plugin = null, $theme = null, $pattern = null) {
		
		if(!$plugin) {
			$plugin = 'Core';
		} else {
			$plugin = Inflector::camelize($plugin);
		}
		
		if(!$theme) {
			$theme = 'core';
		}
		
		if(!$pattern) {
			$pattern = 'default';
		}
		
		if($plugin == 'Core') {
			$paths = array(BASER_CONFIGS . 'data' . DS . $pattern);
			if($theme != 'core') {
				$paths = array_merge(array(
					BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . $pattern,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . Inflector::camelize($pattern),
					BASER_CONFIGS . 'theme' . DS . $theme . DS . 'Config' . DS . 'data' . DS . $pattern,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . 'default',
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . 'default',
				), $paths);
			}
		} else {
			$pluginPaths = App::path('Plugin');
			foreach($pluginPaths as $pluginPath) {
				$pluginPath .= $plugin;
				if(is_dir($pluginPath)) {
					break;
				}
				$pluginPath = null;
			}
			if(!$pluginPath) {
				return false;
			}
			$paths = array(
				$pluginPath . DS . 'Config' . DS . 'data' . DS . $pattern,
				$pluginPath . DS . 'Config' . DS . 'Data' . DS . $pattern,
				$pluginPath . DS . 'Config' . DS . 'Data' . DS . Inflector::camelize($pattern),
				$pluginPath . DS . 'sql',
				$pluginPath . DS . 'Config' . DS . 'data' . DS . 'default',
				$pluginPath . DS . 'Config' . DS . 'Data' . DS . 'default',
			);
			if($theme != 'core') {
				$paths = array_merge(array(
					BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern . DS . $plugin,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . $pattern . DS . $plugin,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . Inflector::camelize($pattern) . DS . $plugin,
					BASER_CONFIGS . 'theme' . DS . $theme . DS . 'config' . DS . 'data' . DS . $pattern . DS . $plugin,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . 'default' . DS . $plugin,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . 'default' . DS . $plugin,
				), $paths);
			}
		}
		
		foreach ($paths as $path) {
			if (is_dir($path)) {
				return $path;
			}
		}
		return false;
		
	}
	
/**
 * シリアライズ
 * 
 * @param mixed $value 対象文字列
 * @return string
 */
	public static function serialize($value) {
		return base64_encode(serialize($value));
	}

/**
 * アンシリアライズ
 * base64_decode が前提
 * 
 * @param mixed $value 対象文字列
 * @return mixed
 */
	public static function unserialize($value) {
		$_value = $value;
		$value = @unserialize(base64_decode($value));
		// 下位互換の為、しばらくの間、失敗した場合の再変換を行う v.3.0.2
		if($value === false) {
			$value = unserialize($_value);
		}
		return $value;
	}

/**
 * URL用に文字列を変換する
 *
 * できるだけ可読性を高める為、不要な記号は除外する
 *
 * @param $value
 * @return string
 */
	public static function urlencode($value) {
		$value = str_replace(array(
			' ', '　', '\\', '\'','|', '`', '^', '"', ')', '(', '}', '{', ']', '[', ';',
			'/', '?', ':', '@', '&', '=', '+', '$', ',', '%', '<', '>', '#', '!'
		), '_', $value);
		$value = preg_replace('/\_{2,}/', '_', $value);
		$value = preg_replace('/(^_|_$)/', '', $value);
		return urlencode($value);
	}

/**
 * レイアウトテンプレートのリストを取得する
 *
 * @param string $path
 * @param string $plugin
 * @param string $theme
 * @return array
 */
	public static function getTemplateList($path, $plugin, $theme) {
		
		if($plugin) {
			$templatesPathes = App::path('View', $plugin);
		} else {
			$templatesPathes = App::path('View');	
		}
		if ($theme) {
			array_unshift($templatesPathes, WWW_ROOT . 'theme' . DS . $theme . DS);
		}
		$_templates = array();
		foreach ($templatesPathes as $templatesPath) {
			$templatesPath .= $path . DS;
			$folder = new Folder($templatesPath);
			$files = $folder->read(true, true);
			$foler = null;
			if ($files[1]) {
				if ($_templates) {
					$_templates = array_merge($_templates, $files[1]);
				} else {
					$_templates = $files[1];
				}
			}
		}
		$templates = array();
		foreach ($_templates as $template) {
			$ext = Configure::read('BcApp.templateExt');
			if ($template != 'installations' . $ext) {
				$template = basename($template, $ext);
				$templates[$template] = $template;
			}
		}
		return $templates;
	}

/**
 * テーマリストを取得する
 *
 * @return array
 */
	public static function getThemeList() {
		$path = WWW_ROOT . 'theme';
		$folder = new Folder($path);
		$files = $folder->read(true, true);
		$themes = array();
		foreach ($files[0] as $theme) {
			if ($theme != 'core' && $theme != '_notes') {
				$themes[$theme] = $theme;
			}
		}
		return $themes;
	}

/**
 * サブドメインを取得する
 *
 * @return string
 */
	public static function getSubDomain() {
		if(isConsole()) {
			return '';
		}
		if(strpos($_SERVER['HTTP_HOST'], '.') === false) {
			return '';
		}
		$host = BcUtil::getMainFullDomain();
		if($_SERVER['HTTP_HOST'] == $host) {
			return '';
		}
		if(strpos($_SERVER['HTTP_HOST'], $host) === false) {
			return '';
		}
		$subDomain = str_replace($host, '', $_SERVER['HTTP_HOST']);
		if($subDomain) {
			return preg_replace('/\.$/', '', $subDomain);
		}
		return '';

	}
	
	public static function getDomain($url) {
		$mainUrlInfo = parse_url($url);
		$host = $mainUrlInfo['host'];
		if(!empty($mainUrlInfo['port'])) {
			$host .= ':' . $mainUrlInfo['port'];
		}
		return $host;
	}
	
	public static function getMainFullDomain() {
		return BcUtil::getDomain(Configure::read('BcEnv.siteUrl'));
	}
	
	public static function getFullDomain() {
		return $_SERVER['HTTP_HOST'];
	}
	
}
