<?php
/**
 * Conexão com o banco de dados.
 *
 * @package    libs
 * @author     Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

use \PDO;

/*
  * Parametros de conexão e cofiguração.
*/ 

define("DB_COR_FUNDO" , "#00CCFF");
define("DB_FILES"     , "/dbportal2/imagens/files");
define("DB_DIRPCB"    , "/home/sistema");
define("DB_EXEC"      , "/usr/bin/dbs");
define("DB_NETSTAT"   , "netstat");

define("DB_VALIDA_REQUISITOS" , false);
define("lUtilizaCaptcha"      , false);

define("db_fonte_codversao"   , "4");
define("db_fonte_codrelease"  , "850");

// Usuário do PostgreSQL
define("DB_USUARIO"   , "postgres");
// Senha do usuário do PostgreSQL
define("DB_SENHA"     , "postgres");
// Ip do servidor para a conexão com a base de dados
define("DB_SERVIDOR"  , "localhost");
// Porta para conexao com o banco de dados (porta do Pool de Conexoes quando utilizado)
define("DB_PORTA"     , "5432");
// Porta para conexao direta com PostgreSQL quando tivermos um pool de conexao          
define("DB_PORTA_ALT" , "5432");
// Nome da base de dados
define("DB_BASE"      , "uas");
define("DB_SELLER"    , "");

/**
 * db_conecta
 */
class db_conecta extends db_stdlib
{

  private static $instance;

  public static function getInstance() {
    if (!isset(self::$instance)) { 
      try { 
        self::$instance = new PDO('pgsql:host='.DB_SERVIDOR.';port='.DB_PORTA.';dbname='.DB_BASE, DB_USUARIO, DB_SENHA);
        self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
    }

    return self::$instance;
  }

  public static function prepare($sql) {
    return self::getInstance()->prepare($sql);
  }

  public static function lastInsertId() {
    return self::getInstance()->lastInsertId();
  }

  function conecta() {

    if (!isset($_SESSION)) {
      session_start();
    }

    $UsuarioSistema = new \model\UsuarioSistema;

    $UsuarioSistema->getUsuarioByLogin('dbseller');

    $_SESSION['DB_traceLog']              = false;
    $_SESSION['DB_uol_hora']              = 1568122924;

    $_SESSION['DB_ip']                    = '127.0.0.1';
    $_SESSION['DB_coddepto']              = 1;

    $_SESSION['DB_nome_modulo']           = 1;
    $_SESSION['DB_datausu']               = 1568119054;
    $_SESSION['DB_login']                 = 'dbseller';
    $_SESSION['DB_id_usuario']            = 1;
    $_SESSION['DB_modulo']                = 1;
    $_SESSION['DB_anousu']                = 2019;
    $_SESSION['DB_instit']                = 1;
    $_SESSION['DB_itemmenu_acessado']     = 665;
    $_SESSION["DB_administrador"]         = 1;
    $_SESSION['DB_preferencias_usuario']  = 'TzoxODoiUHJlZmVyZW5jaWFVc3VhcmlvIjo4OntzOjMyOiIAUHJlZmVyZW5jaWFVc3VhcmlvAHNOb21lQXJxdWl2byI7czoxMzoiZGJzZWxsZXIuanNvbiI7czozMDoiAFByZWZlcmVuY2lhVXN1YXJpbwBzT3JkZW5hY2FvIjtOO3M6MzU6IgBQcmVmZXJlbmNpYVVzdWFyaW8Ab1VzdWFyaW9TaXN0ZW1hIjtPOjE0OiJVc3VhcmlvU2lzdGVtYSI6MTM6e3M6MTM6IgAqAGlJZFVzdWFyaW8iO3M6MToiMSI7czo4OiIAKgBzTm9tZSI7czoxOToiUFJFRkVJVFVSQSBEQlNFTExFUiI7czo5OiIAKgBzTG9naW4iO3M6ODoiZGJzZWxsZXIiO3M6OToiACoAc1NlbmhhIjtzOjQwOiI2N2E3NDMwNmIwNmQwYzAxNjI0ZmUwZDAyNDlhNTcwZjRkMDkzNzQ3IjtzOjE5OiIAKgBpU2l0dWFjYW9Vc3VhcmlvIjtzOjE6IjEiO3M6OToiACoAc0VtYWlsIjtzOjI0OiJkYnNlbGxlckBkYnNlbGxlci5jb20uYnIiO3M6MTg6IgAqAGxVc3VhcmlvRXh0ZXJubyI7czoxOiIwIjtzOjE3OiIAKgBsQWRtaW5pc3RyYWRvciI7czoxOiIxIjtzOjEzOiIAKgBkRGF0YVRva2VuIjtzOjEwOiIyMDE2LTA1LTE5IjtzOjE2OiIAKgBhSW5zdGl0dWljb2VzIjthOjA6e31zOjE3OiIAKgBhRGVwYXJ0YW1lbnRvcyI7YTowOnt9czo3OiIAKgBvQ2dtIjtOO3M6MjA6IgAqAGxQcmVlbmNoZXVFc29jaWFsIjtiOjA7fXM6MzE6IgBQcmVmZXJlbmNpYVVzdWFyaW8Ac0V4aWJlQnVzY2EiO3M6MToiMCI7czoyNToiAFByZWZlcmVuY2lhVXN1YXJpbwBzU2tpbiI7czo3OiJkZWZhdWx0IjtzOjM4OiIAUHJlZmVyZW5jaWFVc3VhcmlvAGxIYWJpbGl0YUNhY2hlTWVudSI7YjoxO3M6NDI6IgBQcmVmZXJlbmNpYVVzdWFyaW8AYUZpbHRyb3NQZXJzb25hbGl6YWRvcyI7TjtzOjk6InNPcmRlbmNhbyI7czoxMDoic2VxdWVuY2lhbCI7fQ';
    
    if (!isset($_SESSION['DB_login']) || !isset($_SESSION['DB_id_usuario'])) {
      session_destroy();
      echo "Sessão Inválida!(12)<br>Feche seu navegador e faça login novamente.\n";
      exit;
    }

    if( isset($_SESSION['DB_servidor']) &&
        isset($_SESSION['DB_base'])     &&
        isset($_SESSION['DB_user'])     &&
        isset($_SESSION['DB_porta'])    &&
        isset($_SESSION['DB_senha']) ){

      $DB_SERVIDOR = db_stdlib::db_getsession("DB_servidor");
      $DB_BASE     = db_stdlib::db_getsession("DB_base");
      $DB_PORTA    = db_stdlib::db_getsession("DB_porta");
      $DB_USUARIO  = db_stdlib::db_getsession("DB_user");
      $DB_SENHA    = db_stdlib::db_getsession("DB_senha");
    }

    /**
     * Nome do programa atual
     */
    $sProgramaAtual = basename($_SERVER["SCRIPT_NAME"]);

    if (isset($_SESSION['DB_NBASE'])) {
      $DB_BASE = $_SESSION["DB_NBASE"];
    }

    if (isset($_SESSION['DB_servidor'])) {
      $DB_SERVIDOR = $_SESSION["DB_servidor"];
    }


    db_stdlib::db_logs();


    if (db_stdlib::db_getsession("DB_id_usuario") != 1 && db_stdlib::db_getsession("DB_administrador") != 1){

      $result1 = db_stdlib::db_query("select db21_ativo from db_config where prefeitura = true");

      if (!$result1) {
        print_r("Erro ao verificar se sistema está liberado! Contate suporte!! Erro: ". $result->errorInfo());
      }

      $ativo   = $result1->fetch()->db21_ativo;

      if ($ativo == 3) {

        echo "Sistema desativado pelo administrador!   <br>Sessão terminada, feche seu navegador!\n";
        session_destroy();
        exit;
      }
    }
  }

  //  Valida sessôes
  static function val_sessao() {
    $sess = 0;
    if(!$_SESSION["DB_modulo"])
      $sess = 1;
    if(!$_SESSION["DB_nome_modulo"])
      $sess = 1;
    if(!$_SESSION["DB_anousu"])
      $sess = 1;
    if(!$_SESSION["DB_instit"])
      $sess = 1;
    if(!$_SESSION["DB_uol_hora"])
      $sess = 1;
    if($sess == 1) {
      session_destroy();
      echo "Sessão Inválida!(14)<br>Feche seu navegador e faça login novamente.<Br>\n";
      exit;
    }
  }
  
}