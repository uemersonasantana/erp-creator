<?php
/**
 * cl_abre_arquivo
 *
 * @package     libs
 * @subpackage  class
 * @author      Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

class cl_abre_arquivo {
  //|00|//cl_abre_arquivo
  //|10|//Abre um determinado arquivo no diretório DOCUMENT_ROOT do servidor onde estiver rodando o PHP
  //|15|//$clabre_arquivo = new cl_abre_arquivo($nomearq="");
  //|20|//Nome do Arquivo : Nome do arquivo para abrir e colocar na propriedade arquivo
  var $nomearq = null;
  //|30|//nomearq : Nome do arquivo com o caminho completo
  var $arquivo = null;
  //|30|//arquivo : FD do arquivo - retorno da função fopen()
  function cl_abre_arquivo($nomearq = "") {
    //#00#//cl_abre_arquivo
    //#10#//Método para abrir um arquivo
    //#15#//cl_abre_arquivo($nomearq="");
    //#20#//Nome do Arquivo : Nome do arquivo a ser gerado, quando em branco, o sistema gera um arquivo aleatório
    //#20#//                  com a função tempnam()
    //#40#//true se o arquivo foi gerado ou false se nao foi gerado
    global $HTTP_SERVER_VARS;
    $Dirroot = "";
    if ($nomearq == "") {
      $Dirroot = substr($HTTP_SERVER_VARS['DOCUMENT_ROOT'], 0, strrpos($HTTP_SERVER_VARS['DOCUMENT_ROOT'], "/"))."/";
      $nomearq = tempnam("tmp", "");
    }
    $this->arquivo = fopen($Dirroot.$nomearq, "w");
    $this->nomearq = $Dirroot.$nomearq;
    if ($this->arquivo == false) {
      return false;
    } else {
      return true;
    }
  }
  //|XX|//
}