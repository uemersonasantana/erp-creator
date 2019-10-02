<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$db_conecta         =   new libs\db_conecta(1); 
$db_stdlib          =   new libs\db_stdlib;
$Encriptacao      	=	  new model\Encriptacao;
$oUsuarioSistema  	= 	new model\UsuarioSistema;
$Encriptacao        =   new model\Encriptacao;
$Services_Funcoes   =   new libs\Services_Funcoes;

//	Converte a string em variáveis
$sAuth 	= $Services_Funcoes->convert_post_string($_POST);
if (isset($sAuth)) {
	parse_str($sAuth);
}

//	Transforma senha em hash
$DB_senha 	=	md5($DB_senha);

//	Busca o json com mensagens que serão usadas no arquivo.
define( 'MENSAGEM', 'configuracao.login.' );

$oParametrosMsg         = new stdClass();
$oParametrosMsg->sCampo = $DB_login;

if ( !strlen($DB_login) ) {

  $sMsg     = $db_stdlib->_M( MENSAGEM . "login_invalido" );
  $sMsgLogs = $db_stdlib->_M( MENSAGEM . "logs_login_invalido", $oParametrosMsg );
  echo $sMsg;
  exit;
}

//  Atualizando configuração do 'search_path' para seletcs sem mencionar o nome do schema.
$sp = $db_stdlib->db_query("SELECT nspname as schema FROM pg_catalog.pg_namespace");
$schema_bloqueio = array('pg_toast','pg_temp_1','pg_temp_1','pg_toast_temp_1','pg_catalog','information_schema');

foreach ($sp as $linha) {
  if ( !in_array($linha->schema, $schema_bloqueio) ) {
    $schemas .= ','.$linha->schema;
  }
}
// Retira a vírgula do início da variável.
$schemas = substr($schemas, 1);
$db_stdlib->db_query('ALTER DATABASE uas SET search_path = "$user", '.$schemas.'');

$db_stdlib->db_logsmanual_demais( $db_stdlib->_M( MENSAGEM . "abrindo_sistema", $oParametrosMsg ) );

/**
 * Valida Tentativas de login do usuário
 *
 * Buscamos o parametro de configuração do número de
 * tentativas de acesso ao portal
 */
$oPreferenciaCliente     = new \model\PreferenciaCliente();
$iTentativasLoginCliente = $oPreferenciaCliente->getTentativasLogin();

$oTentativasAcesso       = new stdClass();

$sLogin                  = $DB_login;
$sSenha                  = $DB_senha;


/**
 * Verificamos se existe outra sessao ja registrada e caso exista
 * efetua o unset e o destroy da mesma
 */
if( !empty($_SESSION['DB_id_usuario'] ) ){

  session_unset();
  session_destroy();
  session_start();

  $DB_login = $sLogin;
  $DB_senha = $sSenha;
}

/**
 * Verificamos se existe a variavel de tentativ de acesso na sessao
 */
if ( !empty($_SESSION['DB_tentativasAcesso'] ) ) {
  $oTentativasAcesso = $db_stdlib->db_getsession('DB_tentativasAcesso');
}

$iTotalTentativas = array_sum((array) $oTentativasAcesso);


if ( !empty($oTentativasAcesso->$DB_login) ) {

  $oTentativasAcesso->$DB_login = $oTentativasAcesso->$DB_login + 1;

  /**
   * Validamos se o numero de tentativas excedeu o numero
   * limite configurado
   */
  if( $oTentativasAcesso->$DB_login > $iTentativasLoginCliente ){

    /**
     * Bloqueamos o usuário
     */
    $sSqlBloqueiaUsuario  = "update db_usuarios                  ";
    $sSqlBloqueiaUsuario .= "   set usuarioativo  = 2            ";
    $sSqlBloqueiaUsuario .= " where login         = '{$DB_login}'";
    $sSqlBloqueiaUsuario .= "   and usuarioativo  = 1            ";
    $sSqlBloqueiaUsuario .= "   and administrador = 0            ";
    $rsBloqueiaUsuario    = $db_stdlib->db_query($sSqlBloqueiaUsuario );
    $sHtmlRetorno         = '';

    if( $rsBloqueiaUsuario ){

      $sMsg         = $db_stdlib->_M( MENSAGEM . "excedeu_tentativas_acesso" );
    }

    echo $sMsg;
    exit;
  }
}else{

  $_SESSION["DB_tentativasAcesso"] = '';
  $oTentativasAcesso->$DB_login = 1;
}
$db_stdlib->db_putsession( "DB_tentativasAcesso", $oTentativasAcesso );


if ( $DB_login == 'dbseller' and !$db_stdlib->db_query("SELECT * from db_usuarios WHERE id_usuario = 1")->rowCount() ) {

  $sqlprocura     = "SELECT * FROM db_usuarios WHERE id_usuario = 1";
  $resultprocura  = $db_stdlib->db_query($sqlprocura);

  if ( !$resultprocura->rowCount() ) {

    $sql = "INSERT INTO db_usuarios (id_usuario , nome , login , senha , usuarioativo , email , usuext)
             values     (1,'UAS - ERP CREATOR','dbseller','" . $Encriptacao->encriptaSenha('') . "','1','nao@informado.com',0)";
     //                    values     (nextval('db_usuarios_id_usuario_seq'),'DBSeller Informática Ltda','dbseller','','t','dbseller#dbseller.com.br',0)";

    $res = $db_stdlib->db_query($sql);
  }

  // cria cgm
  $sqlprocura     = "SELECT * FROM cgm WHERE z01_numcgm = 1";
  $resultprocura  = $db_stdlib->db_query($sqlprocura);

  if ( !$resultprocura->rowCount() ) {
    $sql = "INSERT INTO cgm (z01_numcgm , z01_nome ) VALUES (1,'INSTITUIÇÃO UAS - ERP CREATOR')";
    $res = $db_stdlib->db_query($sql);
  }

  // criar instituicao
  $sqlprocura = "SELECT * FROM db_config WHERE codigo = 1";
  $resultprocura = $db_stdlib->db_query($sqlprocura);

  if ( !$resultprocura->rowCount() ) {
    $sql = "INSERT INTO db_config (codigo , nomeinst,  prefeitura , numcgm ) VALUES (1, 'INSTITUIÇÃO UAS - ERP CREATOR', true, 1)";
    $res = $db_stdlib->db_query($sql);
  }

  // criar ligacao instituicao com usuario (db_userinst)
  $sqlprocura     = "SELECT * FROM db_userinst WHERE id_instit = 1 and id_usuario = 1";
  $resultprocura  = $db_stdlib->db_query($sqlprocura);

  if ( !$resultprocura->rowCount() ) {
    $sql = "INSERT INTO db_userinst (id_instit, id_usuario) VALUES (1, 1)";
    $res = $db_stdlib->db_query($sql);
  }

  // criar ligacao usuario com cgm (db_usuacgm)
  $sqlprocura     = "SELECT * FROM db_usuacgm WHERE id_usuario = 1";
  $resultprocura  = $db_stdlib->db_query($sqlprocura);

  if ( !$resultprocura->rowCount() ) {
    $sql = "INSERT INTO db_usuacgm (id_usuario, cgmlogin) VALUES (1, 1)";
    $res = $db_stdlib->db_query($sql);
  }

  // criar departamento
  $sqlprocura     = "SELECT * FROM db_depart WHERE coddepto = 1";
  $resultprocura  = $db_stdlib->db_query($sqlprocura);

  if ( !$resultprocura->rowCount() ) {
    $sql = "INSERT INTO db_depart (coddepto , descrdepto ,instit) VALUES (1,'CPD',1)";
    $res = $db_stdlib->db_query($sql);
  }

  // criar db_deptousu
  $sql = "INSERT INTO db_depusu(id_usuario , coddepto) VALUES (1,1)";
  $res = $db_stdlib->db_query($sql);


  // liberar acesso ao sistema
  $sql = "INSERT INTO db_sysregrasacesso VALUES (0,'2000-01-01','00:00','2999-01-01','24:00',1,current_date,'implantação')";
  $res = $db_stdlib->db_query($sql);

  $sql = "INSERT INTO db_sysregrasacessoip VALUES (0,'*')";
  $res = $db_stdlib->db_query($sql);
}

/**
 * Habilita acesso apenas para usuarios do e-cidade usuext = 0 negando para:
 * 1 - Usuário Externo
 * 2 - Perfil
 */
$sSql  = "SELECT * FROM db_usuarios WHERE usuarioativo <> '0' and usuext not in (1,2) and login = '{$DB_login}' \n";
$result = $db_stdlib->db_query($sSql);

if ($DB_login != 'dbseller' and $result->rowCount() > 0 and $result->fetch()->administrador != 1 ) {

  $result1 = $db_stdlib->db_query("SELECT db21_ativo FROM db_config WHERE prefeitura = true");
  $ativo   = $result->fetch()->db21_ativo;

  if ($ativo == 3) {

    $sMsg = $db_stdlib->_M( MENSAGEM . "sistema_desativado" );
    $db_stdlib->db_logsmanual_demais( $sMsg );
    echo $sMsg;
    exit;
  }else if ($ativo == 2) {

    $sMsg     = $db_stdlib->_M( MENSAGEM . "acesso_negado" );
    $sMsgLogs = $db_stdlib->_M( MENSAGEM . "logs_acesso_negado", $oParametrosMsg );
    $db_stdlib->db_logsmanual_demais( $sMsgLogs );
    echo $sMsg;
    exit;
  }
}

$sSql    = "SELECT * FROM db_depusu";
$result1 = $db_stdlib->db_query( $sSql );

if( $result->rowCount() == 0 or $result1->rowCount() == 0 ) {

  if( $result1->rowCount() == 0 ){

    $sMsg     = $db_stdlib->_M( MENSAGEM . "login_sem_departamento" );
    $sMsgLogs = $db_stdlib->_M( MENSAGEM . "logs_login_sem_departamento", $oParametrosMsg );
    $db_stdlib->db_logsmanual_demais( $sMsgLogs );
    echo $sMsg;
  }else{

    $sMsg = $db_stdlib->_M( MENSAGEM . "login_invalido" );
    $db_stdlib->db_logsmanual_demais( $sMsg );
    echo $sMsg;
  }

  exit;

} else {

  $oUsuario = $result->fetch();

  // valida data limite para login
  if ( !empty($oUsuario->dataexpira) and strtotime($oUsuario->dataexpira) < strtotime(date('Y-m-d')) ) {
    
    $db_stdlib->db_logsmanual_demais( $db_stdlib->_M( MENSAGEM . 'logs_data_expira', $oParametrosMsg ), $oUsuario->id_usuario );
    $sMsg = $db_stdlib->_M( MENSAGEM . 'data_expira' );
    
    echo $sMsg;

    exit;
  }

  if ($Encriptacao->hash( $DB_senha ) != $oUsuario->senha) {

    $db_stdlib->db_logsmanual_demais( $db_stdlib->_M( MENSAGEM . 'logs_senha_invalida', $oParametrosMsg ), $oUsuario->id_usuario );
    $sMsg = $db_stdlib->_M( MENSAGEM . 'senha_invalida' );
    echo $sMsg;

    exit;
  }



  if ($oUsuario->usuarioativo != 1) {

    $sMsg = $db_stdlib->_M( MENSAGEM . 'usuario_bloqueado' );
    echo $sMsg;

    exit;
  }
  
  /**
   * Desregistramos a variável que controla as tentativas de acesso
   */
  unset($_SESSION['DB_tentativasAcesso']);

  $db_stdlib->db_putsession( "DB_login"         , $DB_login );
  $db_stdlib->db_putsession( "DB_id_usuario"    , $oUsuario->id_usuario );
  $db_stdlib->db_putsession( "DB_administrador" , $oUsuario->administrador );

  /**
   * Realiza a busca das preferências do usuário.
   */
  $oUsuarioSistema = new model\UsuarioSistema( $oUsuario->id_usuario );
  $sPreferencias   = serialize($oPreferenciaUsuario = $oUsuarioSistema->getPreferenciasUsuario());
  $db_stdlib->db_putsession("DB_preferencias_usuario", base64_encode($sPreferencias));

  if (isset($_SERVER["REMOTE_ADDR"]) ){
    $db_stdlib->db_putsession("DB_ip",$_SERVER["REMOTE_ADDR"]);
  }

  $db_stdlib->db_putsession("DB_base",     DB_BASE);
  $db_stdlib->db_putsession("DB_NBASE",    DB_BASE);
  $db_stdlib->db_putsession("DB_servidor", DB_SERVIDOR);
  $db_stdlib->db_putsession("DB_porta",    DB_PORTA);
  $db_stdlib->db_putsession("DB_senha",    DB_SENHA);
  $db_stdlib->db_putsession("DB_user",     DB_USUARIO);

if( $db_stdlib->db_verifica_ip_banco() != '1' ){

  $sMsg = $db_stdlib->_M( MENSAGEM . 'ip_nao_autorizado' );
  $db_stdlib->db_logsmanual_demais( $db_stdlib->_M( MENSAGEM . 'logs_ip_nao_autorizado', $oParametrosMsg ), $oUsuario->id_usuario );
  echo $sMsg;
  exit;
}

$db_db_versao 	= new classes\db_db_versao;
$rsVersao      	= $db_db_versao->sql_record($db_db_versao->sql_query(null,"db30_codversao,db30_codrelease","db30_codver desc limit 1"));

if( $db_db_versao->numrows == 0 ){

  $db30_codversao  = "1";
  $db30_codrelease = "1";
} else {
  $rsVersao = $rsVersao->fetch();
}

$db_fonte_codversao   =	DB_FONTE_CODVERSAO;
$db_fonte_codrelease  =	DB_FONTE_CODRELEASE;

if( $rsVersao->db30_codversao != $db_fonte_codversao || $rsVersao->db30_codrelease != $db_fonte_codrelease ){

  $oParametrosMsg               = new stdClass();
  $oParametrosMsg->sVersaoFonte = $db_fonte_codversao.$db_fonte_codrelease;
  $oParametrosMsg->sVersaoBanco = $rsVersao->db30_codversao.$rsVersao->db30_codrelease;
  $sMsg                         = $db_stdlib->_M( MENSAGEM . 'versao_banco', $oParametrosMsg );
  $db_stdlib->db_logsmanual_demais( $db_stdlib->_M( MENSAGEM . 'logs_versao_banco', $oParametrosMsg ), $oUsuario->id_usuario );
  echo $sMsg;
  exit;
}
/**
 * Funções de cabeçalho para as páginas: Instituições, Áreas, Módulos e Módulo.
 */
$db_stdlib->log_db_usuariosonline('insert','Entrou no sistema');
$db_stdlib->db_putsession("DB_uol_hora", time());

$db_stdlib->db_logsmanual_demais("Acesso Liberado ao sistema - Login: " . $db_stdlib->db_getsession("DB_login"), $db_stdlib->db_getsession("DB_id_usuario"));

$Services_Funcoes->redireciona_msgretorno($Services_Funcoes->url_acesso() . "in/instituicoes", 'Login feito com sucesso!');
}