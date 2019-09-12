<?php
/**
 * Classe de funções gerais.
 *
 * @package    libs
 * @author     Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

/**
 * Services_Funcoes
 */
class Services_Funcoes 
{

// Retorna url do Sistema
public function url_acesso() {
	if ( $_SERVER['SERVER_NAME'] == 'uas.lan' ) {
		return 'http://uas.lan/';
	} else {
		return 'http://'.$_SERVER['SERVER_NAME'].'/sistema/';
	}
}

/**
 * retorna o browser do usuário
 * @return array
 */
function getBrowser()  {
     $var = $_SERVER['HTTP_USER_AGENT'];
     $info['browser'] = "OTHER";

     $browser = array ("MSIE", "OPERA", "CHROME", "FIREFOX", "MOZILLA",
                       "NETSCAPE", "SAFARI", "LYNX", "KONQUEROR");

     $bots = array('GOOGLEBOT', 'MSNBOT', 'SLURP');

     foreach ($bots as $bot) {
         if (strpos(strtoupper($var), $bot) !== FALSE) {
             return $info;
         }
     }

     foreach ($browser as $parent) {
         $s = strpos(strtoupper($var), $parent);
         $f = $s + strlen($parent);
         $version = substr($var, $f, 10);
         $version = preg_replace('/[^0-9,.]/','',$version);
         if (strpos(strtoupper($var), $parent) !== FALSE) {
             $info['browser'] = $parent;
             $info['version'] = $version;
             return $info;
         }
     }
     return $info;
}

function db_compara_conf_php($sValorIni, $sValorConfig, $lBoolean, $sOperacao='==') {

  if ($lBoolean == 'false') {
    $sValorIni    = preg_replace('[^0-9]', '', $sValorIni);
    $sValorConfig = preg_replace('[^0-9]', '', $sValorConfig);
  }

  $nValorIni    = (trim($sValorIni)=='')?0:$sValorIni;
  $nValorConfig = (trim($sValorConfig)=='')?0:$sValorConfig;

  switch ($sOperacao) {
    case "==":
    case "=":
      $lRetorno = ($nValorIni == $nValorConfig);
      break;
    case ">":
      $lRetorno = ($nValorIni > $nValorConfig);
      break;
    case ">=":
      $lRetorno = ($nValorIni >= $nValorConfig);
      break;
    case "<":
      $lRetorno = ($nValorIni < $nValorConfig);
      break;
    case "<=":
      $lRetorno = ($nValorIni <= $nValorConfig);
      break;
    default:
      $lRetorno = false;
      break;
  }

  return $lRetorno;

}

//  Redireciona para uma url
public function redireciona($url) {
  echo "<script>location.href='$url'</script>\n";
  exit;
}

}