<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require ''.$_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';


$db_conecta    = new libs\db_conecta; 

$db_conecta->conecta();
$db_conecta->val_sessao();

$db_stdlib    = new libs\db_stdlib; 



/*
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
//include("libs/db_sessoes.php");
//include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
*/

/*
  //#99#//DB_acessado     Utilizado para Log
  //#99#//DB_login        Login do usuário
  //#99#//DB_id_usuario   Númedo do id do usuário na taela |db_usuarios|
  //#99#//DB_ip           Número do IP que esta acessando
  //#99#//DB_uol_hora     Hora de acesso do usuário
  //#99#//DB_SELLER       Variavel de controle
  //#99#//DB_NBASE        Nome da base de dados que esta sendo acessada
  //#99#//DB_modulo       Número do módulo que esta acessado
  //#99#//DB_nome_modulo  Nome do módulo que esta acessado
  //#99#//DB_anousu       Exercício que esta sendo acessado
  //#99#//DB_datausu      Data do servidor
  //#99#//DB_coddepto     Código do departamento do usuário
  //#99#//DB_instit       Código da instituição
*/

 $db_stdlib->db_menu($db_stdlib->db_getsession("DB_id_usuario"),$db_stdlib->db_getsession("DB_modulo"),$db_stdlib->db_getsession("DB_anousu"),$db_stdlib->db_getsession("DB_instit") );