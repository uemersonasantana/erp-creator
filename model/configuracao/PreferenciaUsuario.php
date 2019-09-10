<?php
/**
 * Classe respons�vel pela manipula��o das prefer�ncias do usu�rio.
 *
 * @package    model
 * @author Renan Melo <renan@dbseller.com.br>
 */

namespace model;

class PreferenciaUsuario {

  /**
   * Caminho onde o arquivo deve ser salvo.
   * @var String
   */
  const CAMINHO_ARQUIVO = 'cache/preferencias/';

  /**
   * Caminhos para o arquivo JSON contendo as mensagens utilizadas na fun��o _M
   */
  const MENSAGENS = 'configuracao.configuracao.preferenciaUsuario.';

  /**
   * Nome do arquivo a ser salvo
   * @var String
   */
  private $sNomeArquivo;

  /**
   * Define a ordena��o que deve ser utilizado nos menus
   * -Sequencial
   * -Alfab�tica
   * @var String
   */
  private $sOrdenacao;

  /**
   * Inst�ncia da classe UsuarioSistema
   * @var UsuarioSistema
   */
  private $oUsuarioSistema;

  /**
   * Define se a busca por menus deve ser exibida ou n�o.
   * @var String
   */
  private $sExibeBusca;

  /**
   * Define o skin que ser� utilizado pelo usu�rio
   * @var String
   */
  private $sSkin;

  /**
   * Se deve fazer cache dos menus
   * @var boolean
   */
  private $lHabilitaCacheMenu;

  /**
   * Filtros personalizados das fun��es de pesquisa
   * @var Array
   */
  private $aFiltrosPersonalizados;

    /**
     * Fun��o construtura, recebe como parametro uma inst�ncia de UsuarioSistema e
     * realiza o LazyLoad carregando as prefer�ncias do usu�rio
     * @param UsuarioSistema $oUsuarioSistema [description]
     */
    function __construct( UsuarioSistema $oUsuarioSistema ) {

      $oPreferenciaCliente = new PreferenciaCliente();

      $this->oUsuarioSistema    = $oUsuarioSistema;
      $this->sNomeArquivo       = $this->oUsuarioSistema->getLogin() . '.json';

      $this->sOrdencao          = 'sequencial';
      $this->sExibeBusca        = '0';
      $this->sSkin              = $oPreferenciaCliente->getSkinDefault();
      $this->lHabilitaCacheMenu = true;

      if (!file_exists(PreferenciaUsuario::CAMINHO_ARQUIVO . $this->sNomeArquivo)) {
        return false;
      }

      $sPreferencias = file_get_contents( PreferenciaUsuario::CAMINHO_ARQUIVO . $this->sNomeArquivo );
      $oPreferencias = json_decode($sPreferencias);

      $this->sOrdencao          = $oPreferencias->ordenacao;
      $this->sExibeBusca        = $oPreferencias->busca;
      
      if ( property_exists($oPreferencias, 'skin') ) {
        $this->sSkin = $oPreferencias->skin;
      }

      if ( property_exists($oPreferencias, 'lHabilitaCacheMenu') ) {
        $this->lHabilitaCacheMenu = $oPreferencias->lHabilitaCacheMenu;
      }
      
      if ( property_exists($oPreferencias, "oFiltrosPersonalizados") ) {
        $this->aFiltrosPersonalizados = (array) $oPreferencias->oFiltrosPersonalizados;
      }




      return true;
    }

  /**
   * Define a ordena��o utilizada nos menus
   * @param String $sOrdencao
   */
  public function setOrdenacao($sOrdencao){
    $this->sOrdencao = $sOrdencao;
  }

  /**
   * Retorna a ordena��o que deve ser utilizada nos menus
   * @return String
   */
  public function getOrdenacao(){
    return $this->sOrdencao;
  }

  /**
   * Define se deve exibir a busca de menus
   * @param string $sBusca
   */
  public function setExibeBusca($sBusca){
    $this->sExibeBusca = $sBusca;
  }

  /**
   * Retorna se ir� exibir a busca de menus
   * @return string
   */
  public function getExibeBusca(){
    return $this->sExibeBusca;
  }

  /**
   * Define o skin a ser utilizado
   * @param string $sSkin
   */
  public function setSkin($sSkin) {
    $this->sSkin = $sSkin;
  }

  /**
   * Retorna o skin a ser utilizado
   * @return string
   */
  public function getSkin() {
    return $this->sSkin;
  }

  /**
   * Define se deve fazer cache dos menus
   * @param boolean $lHabilitaCacheMenu
   */
  public function setHabilitaCacheMenu($lHabilitaCacheMenu) {
    $this->lHabilitaCacheMenu = $lHabilitaCacheMenu;
  }

  /**
   * Retorna se deve fazer cache dos menus
   * @return boolean
   */
  public function getHabilitaCacheMenu() {
    return $this->lHabilitaCacheMenu;
  }

  /**
   * Adiciona um filtro personalizado a rotina especificada
   *  
   * @param String $sRotina -Fu��o de pesquisa
   * @param String $sFiltro -Nome do filtro
   * @param boolean $lAtivo -Define se o filtro vai ser exibido ou n�o
   * @return Boolean
   */
  public function adicionarFiltroPersonalizado( $sRotina, $sFiltro ) {

    if ( !array_key_exists($sRotina, $this->aFiltrosPersonalizados) ) {
      $this->aFiltrosPersonalizados[$sRotina][] = $sFiltro;
      return true;
    }

    if ( !in_array( $sFiltro, $this->aFiltrosPersonalizados[$sRotina]) ) {
      $this->aFiltrosPersonalizados[$sRotina][] = $sFiltro;
      return true;
    }

    return false;
  }

  /**
   * Limpa todos os filtros
   * 
   * @return void
   */
  public function limparFiltrosPersonalizados() {
    $this->aFiltrosPersonalizados = array();
    return;
  }

  /**
   * Remove o filtro personalizado
   *
   * @param String $sRotina -Fu��o de pesquisa
   * @param String $sFiltro -Nome do filtro
   * @return Boolean
   */
  public function removerFiltroPersonalizado( $sRotina, $sFiltro ) {
     
    if ( !array_key_exists($sRotina, $this->aFiltrosPersonalizados) ) {
      return false;
    }

    $aFiltroInvertido = array_flip($this->aFiltrosPersonalizados[$sRotina]);

    if ( !array_key_exists($sFiltro, $aFiltroInvertido) ) {
      return false;
    }
    
    $iChaveExclusao = $aFiltroInvertido[$sFiltro];
    unset($this->aFiltrosPersonalizados[$sRotina][$iChaveExclusao]);
    sort($this->aFiltrosPersonalizados[$sRotina]);

    return true;
  }

  /**
   * Salva o arquivo [login_usuario].json contendo as prefer�ncias.
   * @return boolean
   */
  public function salvar(){

    $sPreferencias = $this->toJSON();

    if (!file_exists(PreferenciaUsuario::CAMINHO_ARQUIVO)) {
      mkdir(PreferenciaUsuario::CAMINHO_ARQUIVO, 0777, TRUE);
    }

    if (!is_writable(PreferenciaUsuario::CAMINHO_ARQUIVO)) {
      throw new Exception(_M(PreferenciaUsuario::MENSAGENS . 'erro_salvar'));
    }

    $oHandle = fopen(PreferenciaUsuario::CAMINHO_ARQUIVO . $this->sNomeArquivo, 'w');
    fwrite($oHandle, $sPreferencias);
    fclose($oHandle);

    /**
     * Limpa o cache dos menus do usuario
     */
    DBMenu::limpaCache($this->oUsuarioSistema->getIdUsuario());

    if (!$oHandle) {
      throw new Exception(_M(PreferenciaUsuario::MENSAGENS . 'erro_salvar'));
    }

    db_putsession("DB_preferencias_usuario", base64_encode(serialize($this)));

    /**
     * Salva o skin no cookie
     */
    $oSkin = new SkinService();
    $oSkin->setCookie();

    return true;
  }

  /**
   * Converte um objeto com as prefer�ncias do usuario
   * para uma String JSON
   * @return String
   */
  public function toJSON(){

    $oPreferencias = new stdClass();
    $oPreferencias->ordenacao              = $this->sOrdencao;
    $oPreferencias->busca                  = $this->sExibeBusca;
    $oPreferencias->skin                   = $this->sSkin;
    $oPreferencias->lHabilitaCacheMenu     = $this->lHabilitaCacheMenu;
    $oPreferencias->oFiltrosPersonalizados = (object)$this->aFiltrosPersonalizados;
    return  json_encode($oPreferencias);
  }
}
