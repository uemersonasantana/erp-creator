<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$db_conecta     =   new libs\db_conecta; 
$db_conecta->conecta();
$db_conecta->val_sessao();
$db_stdlib      =   new libs\db_stdlib;
$db_funcoes     =   new dbforms\db_funcoes; 
$oSkin          =   new libs\Services_Skins;

//  BEGIN: HTML
include $oSkin->getPathFile('html_start.php');
    //  BEGIN: Head
    include $oSkin->getPathFile('head.php');
    //  END: Head

    //  BEGIN: Body
    include $oSkin->getPathFile('body_start.php');
        //  BEGIN: Header
        include $oSkin->getPathFile('body_header.php');
        //  END: Header
    
        $db_stdlib->db_menu($db_stdlib->db_getsession("DB_id_usuario"),$db_stdlib->db_getsession("DB_modulo"),$db_stdlib->db_getsession("DB_anousu"),$db_stdlib->db_getsession("DB_instit") ); 

        //  BEGIN: Content
        include $oSkin->getPathFile('body_content_start.php');
            //  BEGIN: Content Header
            include $oSkin->getPathFile('body_content_header.php');
            //  END: Content Header

            //  BEGIN: Content Body
            include $oSkin->getPathFile('body_content_body_start.php');

                //  ----Páginas que será carregada-----

            //  END: Content Body
            include $oSkin->getPathFile('body_content_body_end.php');

        include $oSkin->getPathFile('body_content_end.php');

        //  BEGIN: Header
        include $oSkin->getPathFile('body_footer.php');
        //  END: Header

    //  END: Body
    include $oSkin->getPathFile('body_end.php'); 

//  END: HTML
include $oSkin->getPathFile('body_end.php'); 