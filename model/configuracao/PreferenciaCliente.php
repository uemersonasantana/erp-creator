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
   * N�mero de tentativas para fazer login
   * @var integer
   */
  protected $iTentativasLogin = 99;
  protected $iDiasExpiraToken = 30;

  public function __construct() {

    parent::__construct("cliente");
  }

  /**
   * M�todo para retornar o n�mero das tentivas de login configurada
   * @return integer
   */
  public function getTentativasLogin() {

    return $this->iTentativasLogin;
  }

  /**
   * Configura o n�mero de tentativas de login
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
   * Salva as preferencias no arquivo de configura��o
   * @return void
   */
  public function salvarPreferencias() {

    $this->oPreferencia->iTentativasLogin = $this->iTentativasLogin;
    $this->oPreferencia->iDiasExpiraToken = $this->iDiasExpiraToken;
    $lSalvo = parent::salvarPreferencias();
    return $lSalvo;
  }
}