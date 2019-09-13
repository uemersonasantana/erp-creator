<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$db_conecta         =   new libs\db_conecta; 
$db_stdlib          =   new libs\db_stdlib;
$Services_Funcoes   =   new libs\Services_Funcoes;
$Services_Skins     =   new libs\Services_Skins;

//  Converte a string em variáveis
$sAuth  = $Services_Funcoes->convert_post_string($_GET);
if (isset($sAuth)) {
    parse_str($sAuth);
}

$hora = time();

$db_stdlib->db_query("INSERT INTO 
                                db_usuariosonline 
                            
                            VALUES 
                                ( ".$db_stdlib->db_getsession("DB_id_usuario")."
                                    ,".$hora."
                                    ,'".$_SERVER['REMOTE_ADDR']."'
                                    ,'".$db_stdlib->db_getsession("DB_login")."'
                                    ,'Entrou no sistema'          
                                    ,''
                                    ,".time()."
                                    ,' ')");

$db_stdlib->db_putsession("DB_uol_hora", $hora);

$result = $db_stdlib->db_query("SELECT nome, login, administrador FROM db_usuarios WHERE id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario"));

$oDadosUsuario = $result->fetch();


$DB_SELLER  =   DB_SELLER;

/**
 * Salva o skin no cookie
 */
$Services_Skins->setCookie();

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

        include $Services_Skins->getPathFile('dashboard','body_content_end.php');

        //  BEGIN: Header
        include $Services_Skins->getPathFile('dashboard','body_footer.php');
        //  END: Header

    //  END: Body
    include $Services_Skins->getPathFile('dashboard','body_end.php'); 

//  END: HTML
include $Services_Skins->getPathFile('dashboard','html_end.php'); 