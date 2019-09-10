<?php
/**
 * Preferencia
 * 
 * @package     model
 * @subpackage  configuracao
 */

namespace model;


abstract class Preferencia {
  
  /**
   * Atributo que receber� os valores: db ou cliente
   * @var string
   */
  private $sTipo       = "db";
  
  /**
   * Skin default
   * @var String
   */
  private $sSkinDefault = "default";
  
  /**
   * Path do arquivo de configura��o
   */
  const CONFIGURACAO    = "config/preferencias.json";
    
  /**
   * Objeto que ir� receber os valores de configura��o por tipo 
   * @var Object
   */
  protected $oPreferencia;

  /**
   * Objeto de configura��o para salvar
   * @var Object
   */
  private $oConfiguracao;
  
  /**
   * Contrutor que receber� o tipo da configura��o
   * @param String $sTipo
   */
  public function __construct($sTipo = null) {
    
    if($sTipo != null) {

      $this->sTipo = $sTipo;
    }   
    
    $this->oConfiguracao = new \stdClass();
    $this->oPreferencia  = new \stdClass();    
    if(file_exists(self::CONFIGURACAO)) {
  
      $this->oConfiguracao = json_decode(file_get_contents(self::CONFIGURACAO)); 
      
      if(property_exists($this->oConfiguracao, $sTipo)) {
         
         foreach($this->oConfiguracao->{$sTipo} as $sAtributo => $sValor ) {
             
             if(property_exists($this, $sAtributo)) {
              
              $this->{$sAtributo} = $sValor;
            }
          } 
      }
    } 
  }
  
  /**
   * Salva as preferencias no arquivo de configura��o
   * @return void
   */
  public function salvarPreferencias() {
    
    $lAcertaPermissao = false;
    if(!file_exists(self::CONFIGURACAO)) {
      
      $lAcertaPermissao = true;
    }
    $this->oPreferencia->sSkinDefault    = $this->sSkinDefault;
    $this->oConfiguracao->{$this->sTipo} = $this->oPreferencia;  
    $lSalvo = file_put_contents(self::CONFIGURACAO, json_encode($this->oConfiguracao));
    if($lAcertaPermissao) {
      
      chmod(self::CONFIGURACAO, 0775);
    }
    return $lSalvo;
  }
  
  /**
   * Atribui novo valor da skin default
   * @param [ String ] $sSkinDefault
   */
  public function setSkinDefault($sSkinDefault) {
     
    $this->sSkinDefault = $sSkinDefault; 
  }
  
  /**
   * Retorna o nome da skin default 
   * @return String
   */
  public function getSkinDefault() {
  
    return $this->sSkinDefault;
  }

}

?>