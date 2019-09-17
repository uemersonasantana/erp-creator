<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$db_stdlib          =   new libs\db_stdlib;
$Services_Funcoes   =   new libs\Services_Funcoes;

$sql = $db_stdlib->db_query("delete from db_usuariosonline 
               where uol_id = ".$db_stdlib->db_getsession("DB_id_usuario")."
			   and uol_ip 	= '".$_SERVER['REMOTE_ADDR']."'
			   and uol_hora = ".$db_stdlib->db_getsession("DB_uol_hora") );
$db_stdlib->db_logsmanual_demais("Sistema Encerrado - Login: ".$db_stdlib->db_getsession("DB_login"),$db_stdlib->db_getsession("DB_id_usuario"));

session_destroy();

$Services_Funcoes->redireciona($Services_Funcoes->url_acesso() . "login");