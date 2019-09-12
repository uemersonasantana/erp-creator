<?php
/**
 * Classe para validar configurações do PHP e outros se está de acordo com a necessidade do sistema.
 *
 * @package   libs
 * @author    Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

class db_valida_requisitos extends db_stdlib {

	const EXTENSIONS_BASE = "/libs/require_extensions.xml";

	public function __construct($DB_VALIDA_REQUISITOS) {

		if ( isset($DB_VALIDA_REQUISITOS) ) {
			self::valida($DB_VALIDA_REQUISITOS);
		}
	}

	public function valida($DB_VALIDA_REQUISITOS) {

		if ( file_exists($_SERVER['DOCUMENT_ROOT'] . self::EXTENSIONS_BASE) ) {

		  // Abre o arquivo XML e transforma em um objeto
		  $oXmlEst      = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . self::EXTENSIONS_BASE);
		} else {

		  // Se não existir o arquivo retorna para a index.php
		  session_start();
		  session_destroy();
		  header("Location: valida_requisitos.php");
		}



		if ( isset($DB_VALIDA_REQUISITOS) && $DB_VALIDA_REQUISITOS == true ) {

		  if(!session_id()){
		    session_start();
		  }

		  if ( !parent::db_getsession("DB_configuracao_ok") ) {

		    session_destroy();
		    header("Location: valida_requisitos.php");
		  }
		}
	}

	/**
	 * Retorna o path do arquivo.
	 *
	 * @return string
	*/
	public function getPathFile() {
		return $_SERVER['DOCUMENT_ROOT'] . self::EXTENSIONS_BASE;
	}
}