<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$db_conecta         =   new libs\db_conecta; 
$db_stdlib          =   new libs\db_stdlib;
$db_usuariosonline  =   new libs\db_usuariosonline;
$Services_Funcoes   =   new libs\Services_Funcoes;
$Services_Skins     =   new libs\Services_Skins;

//  Variáveis que serão usadas no sistema.
$DB_SELLER  =   DB_SELLER;
//$hora       =   time();

/**
 * Salva o skin no cookie
 */
$Services_Skins->setCookie();

//  Pega um vetor e cria variáveis globais pelo índice do vetor.
$db_stdlib->db_postmemory($_REQUEST);

/**
 * Carrega informações do usuário.
 */
$result = $db_stdlib->db_query("SELECT nome, login, administrador FROM db_usuarios WHERE id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario"));

$oDadosUsuario = $result->fetch();

/**
 *  Converte a string $get em variáveis.
 *  Focado nas páginas: Instituições, Áreas, Módulos e Módulo.
 */ 
if ( isset($get) ) {
    parse_str(base64_decode($get));
}

if( !empty($DB_SELLER) ){ 
    if( !empty($_SESSION["DB_SELLER"]) ) {
        $db_stdlib->db_putsession("DB_SELLER","on");
    }
    if( !empty($_SESSION["DB_NBASE"]) ) {
        $db_stdlib->db_putsession("DB_NBASE",DB_BASE);
    }
} else if( isset($_SESSION["DB_NBASE"]) ) {
    unset($_SESSION["DB_NBASE"]);
}

/**
 * Funções de cabeçalho para as páginas: Instituições, Áreas, Módulos e Módulo.
 */
if ( $pagina != 'instituicoes' ) {
    if ( !isset($instit) and isset($_SESSION['DB_instit']) ) {
        $instit = $_SESSION['DB_instit'];
    }

    if ( !isset($area_de_acesso) and isset($_SESSION['DB_Area']) ) {
        $area_de_acesso = $_SESSION['DB_Area'];
    }
    //print_r(base64_decode($get)); exit();

    $Services_Funcoes->cabecalho_pagina($pagina, (isset($instit) ? $instit : null), (isset($area_de_acesso) ? $area_de_acesso : null) );
}

if(isset($modulo) and is_numeric($modulo)){

  $sSqlAreaModulo   =   " SELECT at26_codarea FROM atendcadareamod WHERE at26_id_item = $modulo ";
  $rsSqlAreaModulo  =   $db_stdlib->db_query($sSqlAreaModulo);

  $iNumAreaModulo   =   $rsSqlAreaModulo->rowCount();

  if($iNumAreaModulo > 0){
    $rsSqlAreaModulo    =   $rsSqlAreaModulo->fetch();
    $db_stdlib->db_putsession("DB_Area",$rsSqlAreaModulo->at26_codarea);
  }
}

$db_stdlib->db_putsession( "DB_datausu",time() );

if( !isset($formAnousu) and isset($modulo) ) {
    $db_stdlib->db_putsession("DB_modulo"           ,   $modulo);
    $db_stdlib->db_putsession("DB_nome_modulo"      ,   $nomemod);
    $db_stdlib->db_putsession("DB_anousu"           ,   $anousu);
} else if(  isset($formAnousu) and $formAnousu != "" ) {
    $db_stdlib->db_putsession("DB_anousu"           ,   $formAnousu);
}

//  Se o exercício não for selecionado no módulo, está acessando o módulo.
if( !isset($formAnousu) and isset($modulo) ) {
  //  Se o ano da data do exercício for diferente do anousu registrado, o sistema utiliza como padrão o anousu da data.
  if( $db_stdlib->db_getsession("DB_anousu") != date("Y",$db_stdlib->db_getsession("DB_datausu")) ){
    $db_stdlib->db_putsession("DB_anousu" , date("Y",$db_stdlib->db_getsession("DB_datausu")) );
  }
}

//  BEGIN: HTML
include $Services_Skins->getPathFile('dashboard','html_start.php');
    //  BEGIN: Head
    include $Services_Skins->getPathFile('dashboard','head.php');
    //  END: Head

    //  BEGIN: Body
    include $Services_Skins->getPathFile('dashboard','body_start.php');
        //  BEGIN: Header
        include $Services_Skins->getPathFile('dashboard','body_header.php');
        //  END: Header
        
        //  ----Menu-----
        include 'includes/menu.php';

        //  BEGIN: Content
        include $Services_Skins->getPathFile('dashboard','body_content_start.php');
            //  BEGIN: Content Header
            include $Services_Skins->getPathFile('dashboard','body_content_header.php');
            //  END: Content Header

            //  BEGIN: Content Body
            include $Services_Skins->getPathFile('dashboard','body_content_body_start.php');

                //  ----Páginas que será carregada-----
                include 'pages/' . $pagina . '.php';

            //  END: Content Body
            include $Services_Skins->getPathFile('dashboard','body_content_body_end.php');

        //  END: Content
        include $Services_Skins->getPathFile('dashboard','body_content_end.php');

        //  BEGIN: Footer
        include $Services_Skins->getPathFile('dashboard','body_footer.php');
        //  END: Footer

    //  END: Body
    include $Services_Skins->getPathFile('dashboard','body_end.php'); 

//  END: HTML
include $Services_Skins->getPathFile('dashboard','html_end.php'); 