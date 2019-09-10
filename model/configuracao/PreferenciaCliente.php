<?php
/**
 * PreferenciaCliente
 * 
 * @package     model
 * @subpackage  configuracao
 */

namespace model;

class PreferenciaCliente extends Preferencia {

  /**
   * Número de tentativas para fazer login
   * @var integer
   */
  protected $iTentativasLogin = 99;
  protected $iDiasExpiraToken = 30;

  public function __construct() {

    parent::__construct("cliente");
  }

  /**
   * Método para retornar o número das tentivas de login configurada
   * @return integer
   */
  public function getTentativasLogin() {

    return $this->iTentativasLogin;
  }

  /**
   * Configura o número de tentativas de login
   * @param void
   */
  public function setTentativasLogin($iTentativasLogin) {

    $this->iTentativasLogin = $iTentativasLogin;
  }

  public function getDiasExpiraToken() {

    return $this->iDiasExpiraToken;
  }

  public function setDiasExpiraToken($iDiasExpiraToken) {

    $this->iDiasExpiraToken = $iDiasExpiraToken;
  }
  /**
   * Salva as preferencias no arquivo de configuração
   * @return void
   */
  public function salvarPreferencias() {

    $this->oPreferencia->iTentativasLogin = $this->iTentativasLogin;
    $this->oPreferencia->iDiasExpiraToken = $this->iDiasExpiraToken;
    $lSalvo = parent::salvarPreferencias();
    return $lSalvo;
  }
}