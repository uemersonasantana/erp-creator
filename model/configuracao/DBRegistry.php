<?php
/**
 * Classe Para controle de cache de informações
 *
 * @author iuri@dbseller.com.br
 * @package configuracao
 */

namespace model;

final class DBRegistry {

  /**
   * itens guardados no cache
   * @var array
   */
  private $aitens = array();

  /**
   * Instancia unica do Registry
   * @var DBRegistry
   */
  private static $sInstance = null;

  /**
   * Metodo construtor marcado como privado,
   * para evitar mais de uma instancia
   */
  private function __construct() {

  }

  private function __clone() {}

  /**
   * Metodo que define a criação da instancia da classe
   * @return DBRegistry
   */
  private static function getInstance() {

    if (self::$sInstance == null) {
       self::$sInstance = new DBRegistry();
    }
    return self::$sInstance;
  }
  /**
   * Adiciona um item ao cache
   * @param mixed $sIdentifier chave que identifica o valor do cache
   * @param mixed $mContent conteudo a ser cacheado
   */
  public static function add($sIdentifier, $mContent) {

    self::getInstance()->aitens[$sIdentifier] = $mContent;
  }

  /**
   * Remove o item correspondente a chave
   * @param mixed $sIdentifier chave a ser removida
   */
  public static function remove($sIdentifier) {
    unset(self::getInstance()->aitens[$sIdentifier]);
  }

  /**
   * Retorna um item do repositorio
   * Caso nao exista o item no repositorio, retorna NULL
   * @param mixed $sIdentifier chave a ser pesquisada
   * @return mixed
   */
  public static function get($sIdentifier) {

    if (isset(self::getInstance()->aitens[$sIdentifier])) {
      return self::getInstance()->aitens[$sIdentifier];
    }
    return null;
  }

  /**
   * Verifica se a chave existe no registry
   * @param $sIdentifier
   * @return bool
   */
  public static function has($sIdentifier) {
    return isset(self::getInstance()->aitens[$sIdentifier]);
  }

  /**
   * Retorna o total de itens salvos no repository
   * @return number total de itens no repositorio
   */
  public static  function getTotalItens() {
    return count(self::getInstance()->aitens);
  }
}