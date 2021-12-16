<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

//require_once(DOCROOT."classes/libraries/user_agent.class.php");
class Channel_headerlib {

	public $title;
	public $body_id;
	public $body_function;
	public $iphone_css;
	public $safari_css;
	public $opera_css;
	public $icon_path;
	public $css_path;
	public $javascripts;
	public $javascript_path;
	public $stylesheets;
	public $javascript_plugins;
	public $login_javascript_plugins;
	public $top_javascripts;
	public $bottom_javascripts;
	public $login_javascripts;
	public $plugins;
	public $http_meta_tags;
	public $content_meta_tags;
	public $keywords;
	public $doctype;
//public $user_agent;

	public $config;

	public function __construct() {

		$this->doctype = 'XHTML1.1'; // Set the Doctype definition
		$this->title = ''; // Set the default page title
		$this->body_id = "mainbody"; // Set the default body id (leave blank for no id)
		$this->icon_path = MAIN_LOGO_IMAGE_URL; // Set default icon path for iPhone relative FRONT_URL.

		if (ALLOWS3==1) {
			$this->css_path = AWSLINK.COMMONBUCKETLINK.'/'.CSS_CHANNEL_URL; // Set default path to browser specific css files relative to FRONT_URL.
			$this->javascript_path = AWSLINK.COMMONBUCKETLINK.'/'.JS_CHANNEL_URL; // Set path to javascripts
			$this->plugin_path = AWSLINK.COMMONBUCKETLINK.'/'.PLUGIN_CHANNEL_URL; // Set default path to browser specific css files relative to FRONT_URL.
        }else{
			$this->css_path = CHANNEL_CSS_URL; // Set default path to browser specific css files relative to FRONT_URL.
			$this->javascript_path = CHANNEL_JS_URL; // Set path to javascripts
			$this->plugin_path = CHANNEL_PLUGIN_URL; // Set default path to browser specific css files relative to FRONT_URL.
		}
		$this->safari_css = TRUE; // Safari specific stylesheet
		$this->opera_css = FALSE; // Opera specific stylesheet
		$this->iphone_css = TRUE; // iPhone specific stylesheet

		$this->external_js = array();
		$this->external_css = array();
		$this->login_javascripts = array();
		$this->javascript_plugins = array();
		$this->top_javascripts = array();
		$this->bottom_javascripts = array();
		$this->login_javascript_plugins = array();
		$this->plugins = array();

		$this->external_css = array(
			'font_googleapi' => 'https://fonts.googleapis.com/css?family=Open+Sans',
			'Material_icons' => 'https://fonts.googleapis.com/icon?family=Material+Icons',
			'bootstrap.min' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
		);

		$this->stylesheets = array(
			'bootstrap-checkbox' => 'bootstrap-checkbox.css',
			'bootstrap-select' => 'bootstrap-select.css',
			'styles' => 'styles.css',
			'bootstrap-imageupload' => 'bootstrap-imageupload.css',
		);

		$this->plugins = array(
			'font-awesome' => 'font-awesome/css/font-awesome.min.css',
			'jquery.dropdown' => 'dropdown.js/jquery.dropdown.css',
			'skylo' => 'progress-skylo/skylo.css',
			'pnotify' => 'pines-notify/pnotify.css',
			'daterangepicker-bs3' => 'form-daterangepicker/daterangepicker-bs3.css',
			'fullcalendar' => 'fullcalendar/fullcalendar.css',
			'jquery-jvectormap-2.0.2' => 'jvectormap/jquery-jvectormap-2.0.2.css',
			'chartist.min' => 'chartist/dist/chartist.min.css',
			'dataTables.bootstrap' => 'datatables/dataTables.bootstrap.css',
			'dataTables.themify' => 'datatables/dataTables.themify.css',
			'sweetalert' => 'sweetalert/sweetalert.css',
			'form-select2' => 'form-select2/select2.css',
		);

		$this->javascripts = array(
			'jquery-ui' => 'jqueryui-1.10.3.min.js',
			'popper.min' => 'popper.min.js',
			'bootstrap.min' => 'bootstrap.min.js',
			'enquire.min' => 'enquire.min.js',
			'custom-script' => 'custom-script.js',
			'bootstrap-imageupload' => 'bootstrap-imageupload.js',
			'bootstrap-select' => 'bootstrap-select.js',
		);

		$this->javascript_plugins = array(
			'velocity.min' => 'velocityjs/velocity.min.js',
			'velocity.ui.min' => 'velocityjs/velocity.ui.min.js',
			'skylo' => 'progress-skylo/skylo.js',
			'wijets' => 'wijets/wijets.js',
			'jquery.sparklines.min' => 'sparklines/jquery.sparklines.min.js',
			'prettify' => 'codeprettifier/prettify.js',
			'bootstrap-tabdrop' => 'bootstrap-tabdrop/js/bootstrap-tabdrop.js',
			'jquery.nanoscroller.min' =>  'nanoScroller/js/jquery.nanoscroller.min.js',
			'jquery.dropdown' => 'dropdown.js/jquery.dropdown.js',
			'material.min' => 'bootstrap-material-design/js/material.min.js',
			'ripples.min' => 'bootstrap-material-design/js/ripples.min.js',
			'pnotify.min' => 'pines-notify/pnotify.min.js',
			'jquery.dataTables' => 'datatables/jquery.dataTables.js',
			'dataTables.bootstrap' => 'datatables/dataTables.bootstrap.js',
			'sweetalert' => 'sweetalert/sweetalert.min.js',
			'form-select2' => 'form-select2/select2.min.js',
		);

		//$this->user_agent 			= new User_agent();
		$this->keywords = '';
		$this->http_meta_tags = array(
			'Content-Type' => 'text/html; charset=utf-8',
		);

		$this->content_meta_tags = array(
			'robots' => 'noindex',
			'viewport' => 'width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no',
			'apple-mobile-web-app-capable' => 'yes',
			'apple-touch-fullscreen' => 'yes',
			'author' => COMPANY_NAME
		);
		//$this->config = $GLOBALS['config'];
	}

// End __construct function

	public function set_page_info($title, $body_id) {
		$this->title = $title;
		$this->body_id = ' id="' . $body_id . '" ';
	}

// End set_page_info function

	public function set_title($title) {
		$this->title = $title;
	}

// End set_title function

	public function set_body_id($body_id) {
		$this->body_id = ' id="' . $body_id . '"';
	}

// End set_body_id function

	public function add_stylesheet($name, $file) {
		$this->stylesheets[$name] = $file;
	}

// End add_stylesheet function

	public function add_plugin($name, $file) {
		$this->plugins[$name] = $file;
	}

// End add_plugins function

	public function add_javascript($name, $file) {
		$this->javascripts[$name] = $file;
	}

// End add_javascript function

	public function add_login_javascripts($name, $file) {
		$this->login_javascripts[$name] = $file;
	}

// End add_javascript function

	public function add_javascript_plugins($name, $file) {
		$this->javascript_plugins[$name] = $file;
	}

// End add_javascript_plugins function

	public function add_login_javascript_plugins($name, $file) {
		$this->login_javascript_plugins[$name] = $file;
	}

	// End add_login_javascript_plugins function
	public function add_top_javascripts($name, $file) {
		$this->top_javascripts[$name] = $file;
	}

	public function add_bottom_javascripts($name, $file) {
		$this->bottom_javascripts[$name] = $file;
	}

// End add_bottom_javascript_plugins function

	public function add_meta_tag($name, $content) {
		$this->meta_tags[$name] = $content;
	}

// End add_meta_tag function

	public function data() {
		$data['doctype'] = $this->_doctype();
		$data['meta_tags'] = $this->_meta_tags();
		$data['title'] = $this->title;
		$data['body_id'] = $this->body_id;
		//$data['browser'] = $this->_browser();
		//$data['os'] = $this->user_agent->platform();
		//$data['iphone_headers'] = $this->_iPhone_headers();
		$data['stylesheets'] = $this->_stylesheets();
		$data['plugins'] = $this->_plugins();
		$data['javascript'] = $this->_javascript();
		$data['javascript_plugins'] = $this->_javascript_plugins();
		$data['login_javascripts'] = $this->_login_javascripts();
		$data['iejavascript'] = $this->_iejavascript();
//        $data['gmap'] = $this->_gmap();
		$data['favicon'] = $this->_favicon();
		$data['login_javascript_plugins'] = $this->_login_javascript_plugins();
		$data['top_javascripts'] = $this->_top_javascripts();
		$data['bottom_javascripts'] = $this->_bottom_javascripts();

		return $data;
	}

// End data function

	private function _doctype() {
		switch ($this->doctype) {
		case 'Strict':
			return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"' . "\n" . '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n";
			break;
		case 'Transitional':
			return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"' . "\n" . '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n";
			break;
		case 'Frameset':
			return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"' . "\n" . '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n";
		case 'XHTML':
			return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"' . "\n" . '"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n";
		case 'XHTML1.0':
			return '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n";
		case 'XHTML1.1':
			return '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">' . "\n";
		}
	}

// End _doctype function

	private function _meta_tags() {
		$meta_tags = "<meta charset='utf-8'/>\n";
		foreach ($this->http_meta_tags as $http => $content) {
			$meta_tags .= '<meta http-equiv="' . $http . '" content="' . $content . '"/>' . "\n";
		}
		foreach ($this->content_meta_tags as $name => $content) {
			$meta_tags .= '<meta name="' . $name . '" content="' . $content . '"/>' . "\n";
		}
		return $meta_tags;
	}

// End _meta_tags function

	private function _stylesheets() {
		$stylesheets = "";
		$id = "";

		if (count($this->external_css) > 0) {
			foreach ($this->external_css as $name => $url) {
				$stylesheets .= '<link rel="stylesheet" href="' . $url . '" type="text/css" ' . $name . ' />' . "\n";
			}
		}

		//$this->_browser_specific_stylesheet();
		foreach ($this->stylesheets as $name => $file) {
			if ($name == "style-default") {
				$id = 'id="style_color"';
			}

			$stylesheets .= '<link rel="stylesheet" href="' . $this->css_path . $file . '" type="text/css" ' . $id . ' />' . "\n";
		}
		return $stylesheets;
	}

/// End _stylesheets function

	private function _plugins() {
		$plugins = "";
		//$this->_browser_specific_stylesheet();
		foreach ($this->plugins as $name => $file) {
			$plugins .= '<link rel="stylesheet" href="' . $this->plugin_path . $file . '" type="text/css" media="screen" />' . "\n";
		}
		return $plugins;
	}

/// End PLUGINS function

	private function _javascript_plugins() {
		$javascript_plugins = "";
		//$this->_browser_specific_stylesheet();
		foreach ($this->javascript_plugins as $name => $file) {
			$javascript_plugins .= '<script src="' . $this->plugin_path . $file . '?version='.rand(1,100).'" type="text/javascript" charset="utf-8"></script>' . "\n";
		}

		return $javascript_plugins;
	}

/// End JAVASCRIPT PLUGINS function

	private function _login_javascript_plugins() {
		$javascript_plugins = "";
		//$this->_browser_specific_stylesheet();
		foreach ($this->login_javascript_plugins as $name => $file) {
			$javascript_plugins .= '<script src="' . $this->plugin_path . $file . '?version='.rand(1,100).'" type="text/javascript" charset="utf-8"></script>' . "\n";
		}

		return $javascript_plugins;
	}

/// End JAVASCRIPT PLUGINS function
	private function _top_javascripts() {
		$javascript_plugins = "";
		//$this->_browser_specific_stylesheet();
		foreach ($this->top_javascripts as $name => $file) {
			$javascript_plugins .= '<script src="' . $this->plugin_path . $file . '?version='.rand(1,100).'" type="text/javascript" charset="utf-8"></script>' . "\n";
		}

		return $javascript_plugins;
	}
	private function _bottom_javascripts() {
		$javascript_plugins = "";
		//$this->_browser_specific_stylesheet();
		foreach ($this->bottom_javascripts as $name => $file) {
			$javascript_plugins .= '<script src="' . $this->javascript_path . $file . '?version='.rand(1,100).'" type="text/javascript" charset="utf-8"></script>' . "\n";
		}

		return $javascript_plugins;
	}

/// End JAVASCRIPT PLUGINS function

	private function _login_javascripts() {
		$login_javascripts = "\n";
		//$this->_browser_specific_stylesheet();
		if (count($this->external_js) > 0) {
			foreach ($this->external_js as $name => $url) {
				$login_javascripts .= '<script type="text/javascript" id="' . $name . '" src="' . $url . '"></script>' . "\n";
			}
		}
		$login_javascripts .= '<script type="text/javascript">var CHANNEL_URL = "' . CHANNEL_URL . '"; </script>' . "\n";
		foreach ($this->login_javascripts as $name => $file) {
			$login_javascripts .= '<script src="' . $this->javascript_path . $file . '?version='.rand(1,100).'" type="text/javascript" charset="utf-8"></script>' . "\n";
		}
		return $login_javascripts;
	}

/// End BOTTOM JAVASCRIPT function

	private function _iejavascript() {
		$javascript_content = "\n";
		$javascript_content .= '<!--[if lte IE 7]>
        <script src="http://ie7-js.googlecode.com/svn/version/2.0(beta3)/IE8.js" type="text/javascript">
        </script>
        <script src="http://ie7-js.googlecode.com/svn/version/2.0(beta3)/IE7-squish.js"  type="text/javascript">
        </script>
        <![endif]-->';
		return $javascript_content;
	}

	private function _gmap() {
		$javascript_content = "\n";

		$javascript_content .= '<script id="gmapscript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=' . gmapkey . '" type="text/javascript"></script>';
		return $javascript_content;
	}

	private function _javascript() {
		$javascript_content = "\n";

		if (count($this->external_js) > 0) {
			foreach ($this->external_js as $name => $url) {
				$javascript_content .= '<script type="text/javascript" id="' . $name . '" src="' . $url . '"></script>' . "\n";
			}
		}
		$javascript_content .= '<script type="text/javascript">var CHANNEL_URL = "' . CHANNEL_URL . '"; </script>' . "\n";
		foreach ($this->javascripts as $library => $file) {
			$javascript_content .= '<script src="' . $this->javascript_path . $file . '?version='.rand(1,100).'" type="text/javascript" charset="utf-8"></script>' . "\n";
		}
		return $javascript_content;
	}

// End _javascript function

	/* 	private function _browser_specific_stylesheet()
		      {
		      $browser_string = $this->user_agent->agent_string();
		      $browser = $this->user_agent->browser();
		      $style_url = $this->css_path;
		      $agent = $this->_browser();
		      if($this->safari_css)
		      {
		      if(strpos($browser_string, 'Safari'))
		      {
		      $this->stylesheets['safari'] = "safari.css"; // Safari specific stylesheet
		      }
		      }
		      if($this->opera_css)
		      {
		      if($browser == 'Opera')
		      {
		      $this->stylesheets['opera'] = "opera.css"; // Opera specific stylesheet
		      }
		      }
		      if($this->iphone_css)
		      {
		      if((strpos($browser_string, 'iPhone')) || (strpos($browser_string, 'iPod')))
		      {
		      $this->stylesheets['iphone'] = "iphone.css"; // iPhone/iPod specific stylesheet
		      }
		      }
		      if($browser == 'Internet Explorer')
		      {
		      $v = $this->user_agent->version();
		      if($v == 8)
		      {
		      $this->stylesheets['ie7'] = "ie8.css"; // Internet Explorer 8 specific stylesheet
		      }
		      if($v == 7)
		      {
		      $this->stylesheets['ie7'] = "ie7.css"; // Internet Explorer 7 specific stylesheet
		      }
		      elseif($v == 6)
		      {
		      $this->stylesheets['ie6'] = "ie6.css"; // Internet Explorer 6 specific stylesheet
		      }
		      }
		      } // End _browser_specific_stylesheet function

		      private function _browser()
		      {
		      $browser_string = $this->user_agent->agent_string();
		      $agent = $this->user_agent->browser();
		      $browsers = array('Safari', 'iPhone', 'iPod'); // Make an array of browsers to check for.
		      foreach($browsers as $browser)
		      {
		      if(strpos($browser_string, $browser))
		      {
		      $agent = $browser;
		      }
		      }
		      return $agent;
		      } // End _browser function
		      private function _iPhone_headers()
		      {
		      $browser = $this->_browser();
		      if(($browser == 'iPhone') || ($browser == 'iPod'))
		      {
		      $iphone_headers = '<meta name="viewport" content="width=device-width, user-scalable=no" />'."\n";
		      // Link to iphone icon. The icon file should be a simple 57px x 57px .png.
		      // Reflection, rounded-corners, glass and drop shadow will be generated automatically.

		      $iphone_headers .= '<link rel="apple-touch-icon" href="'.$this->icon_path.'iphone_icon.png"/>';
		      } else {
		      $iphone_headers = '';
		      }
		      return $iphone_headers;
		      } // End _iPhone_headers function
	*/

	private function _favicon() {
		$favicon = '<link rel="shortcut icon" type="image/x-icon"  href="' . $this->icon_path  . COMPANY_FAVICON . '" />';
		return $favicon;
	}

// End _favicon function

	public function debug() {
		$data = $this->data();
		$info = "";
		foreach ($data as $key => $value) {
			$info .= $key . ' - ' . htmlentities($value) . "<br />";
		}
		return $info;
	}

// End debug function
}

// End Header class.
?>
