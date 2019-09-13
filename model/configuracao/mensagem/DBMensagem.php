<?php
/**
 * Classe para controle de mensagens do sistema
 * 
 * Controle de mensagens emitidas pelo sistema
 * 
 * @package     configuracao
 * @subpackage  mensagem
 * @author      Iuri Guntchnigg <iuri@dbseller.com.br>
 * @version     $Revision: 1.14 $;
 */

namespace model;

use libs\Services_JSON;
use libs\ParameterException;

class DBMensagem {

  protected $aFile;

  /**
   * Instancia unica da classe
   * @var DBMensagem
   */
  private static $oInstance = null;

  /**
   * Construtor marcado como private
   */
  protected function __construct() {

  }

  /**
   * Retorna uma instancia de DBMensagem
   * @return DBMensagem
   */
  private static function getInstance() {

    if (self::$oInstance == null) {
      self::$oInstance = new DBMensagem();
    }
    return self::$oInstance;
  }

  /**
   * Retorna a informacao de um arquivo
   * @param string $sArquivo caminho do arquivo
   */
  public static function getFile($sArquivo) {

    if (!isset(self::getInstance()->aFile[$sArquivo])) {

      $sNomeArquivo = $_SERVER['DOCUMENT_ROOT'] . "/mensagem/{$sArquivo}";
      
      if (!file_exists($sNomeArquivo)) {
        // ATENCAO
        throw new \Exception("Arquivo de mensagens '{$sNomeArquivo}' não existe no sistema");
      }
      self::getInstance()->aFile[$sArquivo] = file_get_contents($sNomeArquivo);
    }
    $sArquivo = self::getInstance()->aFile[$sArquivo];

    //metodo que abre o associacoes
    return $sArquivo;
  }

  /**
   * Clone marcado como protected, evitando criar novas instancias da classe
   */
  protected function __clone() {

  }

  /**
   * Retorna a mensagem passada como parametro para o usuario
   * @param string $sMensagem Caminho da mensagem a ser exibida
   * @param array/stdClass $mOpcoes array com variaveis para substituição de Variaveis na mensagem
   */
  public static function getMensagem($sMensagem, $aOpcoes = null) {

    if ( !is_null($aOpcoes) && !is_object($aOpcoes)) {
      throw new \Exception('Parametro $aOpcoes deve ser um objeto.');
    }

    $oJson                 = Services_JSON::create();
    $aPartesArquivo        = explode('.', $sMensagem);
    $iTamanhoPartesArquivo = count($aPartesArquivo);
    $sNomeArquivo          = $aPartesArquivo[$iTamanhoPartesArquivo - 2];
    $sNomeMensagem         = $aPartesArquivo[$iTamanhoPartesArquivo - 1];
    $sCaminhoArquivo       = implode("/", array_slice($aPartesArquivo, 0, $iTamanhoPartesArquivo - 1)).".json";
    $sArquivo              = self::getInstance()->getFile($sCaminhoArquivo);
    $oJsonArquivo          = $oJson->parse($sArquivo,'');
    $sMensagem             = 'Ocorreu um erro inesperado. Contate suporte.';

    if (isset($oJsonArquivo->{$sNomeMensagem})) {

      $sMensagem = $oJsonArquivo->{$sNomeMensagem};
      if (!empty($aOpcoes)) {
        $sMensagem = self::getInstance()->aplicarVariaveis($sMensagem, $aOpcoes);
      }

    }

    return $sMensagem;
  }

  /**
   * Guarda historico em sessao, das mensagens usadas
   * - caso usuario nao for dbseller, retorna false
   *
   * @param string $sArquivo
   * @param string $sNomeMensagem
   * @static
   * @access public
   * @return boolean
   */
  public static function guardarHistorico($sArquivo, $sNomeMensagem) {

    if ( empty($_SESSION["DB_login"]) || $_SESSION["DB_login"] != "dbseller" ) {
      return false;
    }

    $iMenuAtual = $_SESSION["DB_itemmenu_acessado"];

    if ( empty($_SESSION['oMensagensMenu']) ) {

      $oMensagensMenu = new stdClass();
      $oMensagensMenu->iMenu           = $iMenuAtual;
      $oMensagensMenu->aArquivos       = array();
      $_SESSION['oMensagensMenu'] = $oMensagensMenu;
    }

    $_SESSION['oMensagensMenu']->aArquivos[$sArquivo][] = $sNomeMensagem;
    $_SESSION['oMensagensMenu']->aArquivos[$sArquivo] = array_unique($_SESSION['oMensagensMenu']->aArquivos[$sArquivo]);

    return true;
  }

  /**
   * Aplica o valor das variaveis na Mensagem
   * @param unknown $sMensagem
   * @param unknown $oOpcoes
   * @return mixed
   */
  private static function aplicarVariaveis($sMensagem, $oOpcoes) {

    $aPropriedades = get_object_vars($oOpcoes);
    if (count($aPropriedades) > 0) {

      foreach ($aPropriedades as $sPropriedade => $sValor) {
        $sMensagem = str_replace ("[$sPropriedade]", $sValor, $sMensagem);
      }
      $sMensagem = str_replace(array("[", "]"), '', $sMensagem);
    }
    return $sMensagem;
  }

  /**
   * Adiciona um novo arquivo ao arquivo de associações (associacoes.json)
   * Ex: self::associarArquivo("patrimonial/protocolo/prot4_processodocumento001.json", 1234);
   * @param string  - Caminho completo do arquivo .json
   * @param integer - Código do Menu Acessado
   */
  public static function associarArquivo($sArquivoProcurado, $iCodigoMenuProcurado) {

    $sCaminhoArquivoAssociacao = "mensagens/associacoes/associacoes.json";
    $sArquivo = file_get_contents($sCaminhoArquivoAssociacao);
    $oJson    = new Services_JSON();
    $oArquivo = $oJson->decode($sArquivo);

    /**
     * Verifico se o arquivo e menu já existem cadastrados no arquivo de associações.
     */
    $lEncontrouMenu    = true;
    $lEncontrouArquivo = false;
    foreach ($oArquivo->associacoes as $oStdLinha) {

      if ($oStdLinha->fonte === $sArquivoProcurado) {

        $lEncontrouArquivo = true;
        if (!in_array($iCodigoMenuProcurado, $oStdLinha->menus)) {

          $lEncontrouMenu = false;
          $oStdLinha->menus[] = $iCodigoMenuProcurado;
        }
      }
    }

    /**
     * Caso não encontre o arquivo, o programa adiciona na última posição do arquivo o menu
     * acessado e o novo arquivo de mensagem
     */
    if ( !$lEncontrouArquivo ) {

      $oStdAdicionar           = new stdClass();
      $oStdAdicionar->fonte    = $sArquivoProcurado;
      $oStdAdicionar->menus    = array($iCodigoMenuProcurado);
      $oArquivo->associacoes[] = $oStdAdicionar;
    }

    /**
     * Caso não seja encontrado o arquivo ou o menu no arquivo de associações é reescrito adicionando
     * os novos menus e arquivos.
     */
    if ( !$lEncontrouArquivo || !$lEncontrouMenu ) {

      $iIdentacao = 0;
      foreach ($oArquivo->associacoes as $oStdLinhaArquivo) {

        $iTamanhoCaracterPropriedade = strlen($oStdLinhaArquivo->fonte);
        if ($iTamanhoCaracterPropriedade > $iIdentacao) {
          $iIdentacao = $iTamanhoCaracterPropriedade + 5;
        }
      }

      $aArquivoSalvar = array();
      $aArquivoSalvar[] = "{\"associacoes\" : [";
      foreach ($oArquivo->associacoes as $iIndice => $oStdLinhaArquivo) {

        $sVirgula = ",";
        if ($iIndice == 0) {
          $sVirgula = " ";
        }
        $sMenus = implode(",", $oStdLinhaArquivo->menus);
        $sFonte = str_pad("\"$oStdLinhaArquivo->fonte\",", $iIdentacao, ' ');
        $aArquivoSalvar[] = "    {$sVirgula}{  \"fonte\" : {$sFonte} \"menus\" : [{$sMenus}]  }";
      }
      $aArquivoSalvar[] = "]}";

      $oArquivo          = implode("\n", $aArquivoSalvar);
      $rsEscreverArquivo = file_put_contents($sCaminhoArquivoAssociacao, $oArquivo, LOCK_EX);
      if ( !$rsEscreverArquivo ) {
        // ATENCAO
        //throw new FileException("Não foi possível reescrever o arquivo 'associacoes.json'.");
      }
    }
    return true;
  }
}