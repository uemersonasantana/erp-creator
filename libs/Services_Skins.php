<?php
/**
 * Skins
 *
 * @package    libs
 * @author     Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

use model\PreferenciaCliente;
use model\PreferenciaEcidade;

/**
 * Classe Skins
 */
Class Services_Skins {

  const SKINS_BASE    = 'skins/';
  const COOKIE_NOME   = 'uas_skin';

  /**
   * Skin default
   * @var string
   */
  private $sSkinDefault = '';

  public function __construct() {

    $oPreferenciaCliente = new PreferenciaCliente();
    $this->sSkinDefault = $oPreferenciaCliente->getSkinDefault();
  }

  /**
   * Retorna todos as skins disponíveis
   *
   * @return Array
   */
  public function getSkins() {

    $aSkins = scandir(self::SKINS_BASE);

    $aRetornoSkins = array();
    foreach ($aSkins as $sSkin) {

      $sPathPlugin = self::SKINS_BASE . $sSkin;

      if (!in_array($sSkin, array('.', '..')) && is_dir($sPathPlugin) && file_exists("{$sPathPlugin}/config.json")) {
        $oJson = json_decode( file_get_contents("{$sPathPlugin}/config.json") );

        $aRetornoSkins[$sSkin] = utf8_decode($oJson->nome);
      }
    }

    return $aRetornoSkins;
  }

  /**
   * Retorna a skin que esta ativa para o usuário
   *
   * @return String
   */
  public function getActiveSkin() {

    if( !isset($_SESSION['DB_preferencias_usuario']) ){
      return $this->sSkinDefault;
    }

    $oPreferencias = unserialize(base64_decode($_SESSION['DB_preferencias_usuario']));

    return $oPreferencias->getSkin();
  }

  /**
   * Retorna o path do arquivo passado por parametro -- Caso não encontre na skin ativa pega da skin padrão --
   *
   * @param string $sArquivo Arquivo a ser carregado
   * @param string $sActiveSkin a Skin ativa atualmente (Caso não seja passado irá pegar da sessão)
   * @return string
   */
  public function getPathFile($subPasta, $sArquivo, $sActiveSkin = "") {

    $sPath = $_SERVER['DOCUMENT_ROOT'] . "/" . self::SKINS_BASE . (!empty($sActiveSkin) ? $sActiveSkin : $this->getActiveSkin()) . "/" . $subPasta . "/{$sArquivo}";

    if (file_exists($sPath)) {
      //return $sPath;
    }

    $sPath = $_SERVER['DOCUMENT_ROOT'] . "/" . self::SKINS_BASE . $this->sSkinDefault . "/" .$subPasta . "/{$sArquivo}";

    if (file_exists($sPath)) {
      return $sPath;
    }

    $oPreferenciaEcidade = new PreferenciaEcidade();
    return $_SERVER['DOCUMENT_ROOT'] . "/" . self::SKINS_BASE . $oPreferenciaEcidade->getSkinDefault() . "/" . $subPasta . "/{$sArquivo}";
  }

  /**
   * Retorna o cookie salvo na sessão com a skin ativa
   * @return mixed string|null
   */
  public function getCookie() {

    if ( !empty($_COOKIE[self::COOKIE_NOME]) ) {
      return $_COOKIE[self::COOKIE_NOME];
    }

    return null;
  }

  /**
   * Seta na sessão a skin ativa
   */
  public function setCookie() {
    setcookie( self::COOKIE_NOME, $this->getActiveSkin(), 0, '/' );
  }

  /**
   * Retorna a skin que esta ativa para o usuário
   *
   * @return String
   */
  public function getSkinLink() {
    $Services_Funcoes  = new \libs\Services_Funcoes;

    $oPreferenciaEcidade = new PreferenciaEcidade();

    return $Services_Funcoes->url_acesso() . self::SKINS_BASE . $oPreferenciaEcidade->getSkinDefault() . "/";
  }


}