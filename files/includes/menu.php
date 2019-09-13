<?php

function exibir_menu($pagina) {
	switch ($pagina) {
	    case 'instituicoes':
	        return false;
	    case 'areas':
	        return false;
	    case 'modulos':
	        return false;
	    default:
	    	return true;
	}
}

if ( exibir_menu($pagina) ) {
	$db_stdlib->db_menu($db_stdlib->db_getsession("DB_id_usuario"),$db_stdlib->db_getsession("DB_modulo"),$db_stdlib->db_getsession("DB_anousu"),$db_stdlib->db_getsession("DB_instit") );
}