<?php
/**
 * PreferenciaEcidade
 * 
 * @package     model
 * @subpackage  configuracao
 */

namespace model;

class PreferenciaEcidade extends Preferencia {

  protected $oPreferenciaTelaLogin;

  public function __construct() {

    $this->oPreferenciaTelaLogin = new \stdClass();
    parent::__construct("db");
  }

  public function setPreferenciaTelaLogin($iDataAlteracao, $sClassAtiva) {

    $this->oPreferenciaTelaLogin->iDataAlteracao = $iDataAlteracao;
    $this->oPreferenciaTelaLogin->sClassAtiva    = $sClassAtiva;
  }

  public function getPreferenciaTelaLogin() {

    return $this->oPreferenciaTelaLogin;
  }

  public function salvarPreferencias() {

    $this->oPreferencia->oPreferenciaTelaLogin = $this->oPreferenciaTelaLogin;
    $lSalvo = parent::salvarPreferencias();
    return $lSalvo;
  }

}