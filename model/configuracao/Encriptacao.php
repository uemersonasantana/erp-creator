<?php
/**
 * Classe padrão de criptografia da DBSeller
 * 
 * @package     model
 * @subpackage  configuracao
 * @author      Tales Baz   <tales.baz@dbseller.com.br>
 * @author      Vitor Rocha <vitor@dbseller.com.br>
 */

namespace model;

class Encriptacao {

  /**
   * Criptografia sha1()
   */
  const SHA1 = 1;

  /**
   * Criptografia md5()
   */
  const MD5  = 2;

  /**
   * Criptografa uma string na criptografia informada.
   * @param  string  $sString Texto para ser criptografado
   * @param  integer $iTipo   Tipo de criptografia
   *                          1 = SHA1
   *                          2 = MD5
   * @return string           Texto criptografado
   */
  public static function hash( $sString, $iTipo = 1 ) {

    switch ($iTipo) {

      case self::SHA1:
        return sha1( $sString );
      break;

      case self::MD5:
        return md5( $sString );
      break;
    }

  }

  /**
   * Criptografa o texto informado no padrão da DBSeller sha1(md5(string))
   * @param  string $sString Texto para ser criptografado
   * @return string          Texto criptografado
   */
  public static function encriptaSenha ( $sString = '' ){

    return self::hash( self::hash( $sString, self::MD5 ), self::SHA1 );
  }
}