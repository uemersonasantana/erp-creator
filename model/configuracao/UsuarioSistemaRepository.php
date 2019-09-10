<?php
/**
 * UsuarioSistemaRepository.
 *
 * @package    libs
 * @author     Uemerson A. Santana <uemerson@icloud.com>
 */

namespace model;

/**
 * Class UsuarioSistemaRepository
 */
class UsuarioSistemaRepository {

  /**
   * @var array
   */
  private $aItens = array();

  /**
   * @var UsuarioSistemaRepository
   */
  private static $oInstancia;

  /**
   * @param $iCodigo
   * @return UsuarioSistema
   */
  public static function getPorCodigo($iCodigo) {

    if ( !array_key_exists($iCodigo, UsuarioSistemaRepository::getInstancia()->aItens)) {
      UsuarioSistemaRepository::getInstancia()->aItens[$iCodigo] = new UsuarioSistema($iCodigo);
    }
    return UsuarioSistemaRepository::getInstancia()->aItens[$iCodigo];
  }

  /**
   * @return UsuarioSistemaRepository
   */
  private static function getInstancia() {

    if (self::$oInstancia == null) {
      self::$oInstancia = new UsuarioSistemaRepository();
    }
    return self::$oInstancia;
  }

  /**
   * Retorna as Lotações que o Usuário do sistema ãinda não possuí vínculo.
   * 
   * @param  UsuarioSistema   $oUsuarioSistema 
   * @param  Instituicao|null $oInstituicao    
   * @return Array Toddas as Lotações ainda disponíveis para o usuário.
   */
  public static function getLotacoesPermitidas(UsuarioSistema $oUsuarioSistema, Instituicao $oInstituicao = null) {

    if (is_null($oInstituicao)) {
      $oInstituicao = InstituicaoRepository::getInstituicaoSessao();
    }

    $aLotacoesIntituicao      = LotacaoRepository::getLotacoesByInstituicao($oInstituicao, true);
    $aLotacoesUsuario         = LotacaoRepository::getLotacoesByUsuario($oUsuarioSistema, $oInstituicao);
    $iTotalLotacaoInstituicao = count($aLotacoesIntituicao);

    for ($iLotacaoInstituicao = 0; $iLotacaoInstituicao < $iTotalLotacaoInstituicao; $iLotacaoInstituicao++) {

      $oLotacaoInstituicao = $aLotacoesIntituicao[$iLotacaoInstituicao];
      for ($iLotacoesUsuario = 0; $iLotacoesUsuario < count($aLotacoesUsuario); $iLotacoesUsuario++) {

        $oLotacaoUsuario = $aLotacoesUsuario[$iLotacoesUsuario];

        if ($oLotacaoInstituicao->getCodigoLotacao() == $oLotacaoUsuario->getCodigoLotacao()) {
          unset($aLotacoesIntituicao[$iLotacaoInstituicao]);
        }
      }
    }
    sort($aLotacoesIntituicao);

    return $aLotacoesIntituicao;
  }

  /**
   * Impossibilita instancia
   */
  private function __construct() {}
  private function __clone() {}
}