<?php
/**
 * Log Usuários Online.
 *
 * @package    libs
 * @author     Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

/**
 * db_usuariosonline
 */
class db_usuariosonline extends db_stdlib
{

function __construct() {
	if ( isset($_REQUEST['pagina']) ) {
		$pagina = 	$_REQUEST['pagina'].'.php';
	} else {
		$pagina	=	basename($_SERVER['PHP_SELF']);
	}

	$result = parent::db_query("select descricao from db_itensmenu where funcao = '".$pagina."'");
	if($result->rowCount() > 0)
	  $str = $result->fetch()->descricao;
	else
	  $str = $pagina;

	$result = parent::db_query("select uol_id from db_usuariosonline 
	  where uol_id = ".parent::db_getsession("DB_id_usuario")."
	  and uol_ip = '".$_SERVER['REMOTE_ADDR']."' 
	  and uol_hora = ".parent::db_getsession("DB_uol_hora"));
	if($result->rowCount() == 0) {
	  $hora = time();
	  parent::db_query($conn,"insert into db_usuariosonline 
	    values(".parent::db_getsession("DB_id_usuario").",
	      ".$hora.",
	      '".$_SERVER['REMOTE_ADDR']."',            
	      '".parent::db_getsession("DB_login")."',
	      '".$str."',
	      '".parent::db_getsession("DB_nome_modulo")."',
	      ".time().")");
	  parent::db_putsession("DB_uol_hora",$hora);
	} else {
	  parent::db_query("update db_usuariosonline set  
	    uol_arquivo = '".$str."',
	    uol_inativo = ".time()."
	    where uol_id = ".parent::db_getsession("DB_id_usuario")."
	    and uol_ip = '".$_SERVER['REMOTE_ADDR']."' 
	    and uol_hora = ".parent::db_getsession("DB_uol_hora")."
	    ");
	}
}
}