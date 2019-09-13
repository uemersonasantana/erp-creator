<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$db_conecta             =   new libs\db_conecta; 
$db_stdlib              =   new libs\db_stdlib;
$Services_Skins                  =   new libs\Services_Skins;
$Services_Funcoes       =   new libs\Services_Funcoes;
$db_valida_requisitos   =   new libs\db_valida_requisitos;



$_DB_VALIDA_REQUISITOS   =   DB_VALIDA_REQUISITOS;

// Diretório das extensoes do servidor necessárias
$diretorio          = $db_valida_requisitos->getPathFile();
$sDirDocumentRoot   = $_SERVER['DOCUMENT_ROOT'] . "/";

$lErro                = false;
$lBrowser             = false;
$lErroMod             = false;
$lErroParam           = false;
$lErroDirTmp          = false;
$lErroSetings         = false;
$lDirTmpRaizExist     = true;
$lDirTmpDbPortalExist = true;

if (file_exists($diretorio)) {

// Abre o arquivo XML e transforma em um objeto
   $oXmlEst   = simplexml_load_file($diretorio);
   $lErroConf = false;
} else {

// Se não existir o arquivo config/require_extensions.xml retorna mensagem
   $sMsgConf   = "Erro: 404 Diretório de Configuração Inexistente! \n";
   $sMsgConf  .= "Contate Administrador do Sistema.";
   $lErroConf  = true;
}

if ( isset($_DB_VALIDA_REQUISITOS) && $_DB_VALIDA_REQUISITOS == true ) { 
    
    $aBrowserVersao   = $Services_Funcoes->getBrowser();

    switch ($aBrowserVersao["browser"]) {
        case "MSIE" :
            $sBrowser = "Internet Explorer ".$aBrowserVersao["version"];
            break;
        case "OPERA" :
            $sBrowser = "Opera Browser ".$aBrowserVersao["version"];
            break;
        case "FIREFOX" :
            $sBrowser = "Mozilla Firefox ".$aBrowserVersao["version"];
            break;
        case "MOZILLA" :
            $sBrowser = "Mozilla Firefox ".$aBrowserVersao["version"];
            break;
        case "CHROME" :
            $sBrowser = "Google Chrome ".$aBrowserVersao["version"];
            break;
        case "NETSCAPE" :
            $sBrowser = "Netscape Browser ".$aBrowserVersao["version"];
            break;
        case "SAFARI" :
            $sBrowser = "Safari Browser ".$aBrowserVersao["version"];
            break;
        case "LYNX" :
            $sBrowser = "Lynx Browser ".$aBrowserVersao["version"];
            break;
        case "KONQUEROR" :
            $sBrowser = "Konqueror Browser ".$aBrowserVersao["version"];
            break;
        default :
           $sBrowser = "Browser Desconhecido";
    }

    // retorna versao do browser sem a sua subversao
    $aVersao = explode(".", $aBrowserVersao["version"]);
    // verifica modulos pendentes
    $aModulos = get_loaded_extensions();
    $sVersao = $aVersao[0];
    // versao do browser com subversao
    $oBrowserVersaoSub    = strtolower($aBrowserVersao["browser"].$aBrowserVersao["version"]);
    // versao do browser sem subversao
    $oBrowserVersaoSemSub = strtolower($aBrowserVersao["browser"].$sVersao);


    foreach ($oXmlEst->browsers->browser as $oValCampo ) {

      $sStrpos = strpos($oValCampo['versao'],'*');

      if ($sStrpos === false) {

         $aListaBrowser = $oValCampo['name'].$oValCampo['versao'];
         $iTam          = strlen($oValCampo['versao']);
         if ($aListaBrowser == $oBrowserVersaoSub) {
         $lBrowser = true;
           break;
         }
      } else {


        $sVersao          = $oValCampo['versao'];
        $aVersaoVerificar = explode(".", $sVersao);
        $iTotalVersoesUsuario   = count($aVersao);
        $iTotalVersoesVerificar = count($aVersaoVerificar);
        /**
         * deixamos as versoes do Browser com o mesmo Tamanho.
         */
        if ($iTotalVersoesUsuario != $iTotalVersoesVerificar) {
           if ($iTotalVersoesUsuario > $iTotalVersoesVerificar) {
             array_pad($aVersao, $iTotalVersoesVerificar, 0);
           } else if ($iTotalVersoesUsuario < $iTotalVersoesVerificar) {
             array_pad($aVersaoVerificar, $iTotalVersoesUsuario, 0);
           }
        }

        /**
         * Caso devemos ignorar algum pedaço da versao, deixamos a versao do browser do usuario como *
         */
        foreach ($aVersaoVerificar as $iParte => $sVersaoVerificar) {

          if ($sVersaoVerificar == "*") {
            $aVersao[$iParte] = "*";
          }
        }
        $sVersao = implode("", $aVersaoVerificar);
        $oBrowserVersaoSemSub =  strtolower($aBrowserVersao["browser"]).implode("", $aVersao);
        $aListaBrowser = $oValCampo['name'].$sVersao;
        $sValCampo     = str_replace("*", "", $oValCampo['versao']);
        $sValCampo     = str_replace(".", "", $sValCampo);
        $iTam          = strlen($sValCampo);
        if ($iTam >= 2) {

          if ($aListaBrowser == $oBrowserVersaoSemSub) {

            $lBrowser = true;
            break;
          }
        } else {

          $sMsgConf   = "Erro: Parametro(s) de Configuração do Browser não Configurados Corretamente! \n";
          $lErroConf  = true;
          $lErro      = true;
        }
      }
    }

    $i = 0;
    foreach( $oXmlEst->modulos->modulo as $aValCampo ) {

      $aListaModVlrPadrao[$i] = $aValCampo['valorpadrao'];
      if (!in_array($aListaModVlrPadrao[$i], $aModulos)) {

      $aListaModulos[$i] = $aValCampo['label'];
      $lErroMod = true;
    }
      $i++;
    }

    $aListaParam = array();
    foreach ($oXmlEst->parametros->parametro as $aParametro) {

      if ( !$Services_Funcoes->db_compara_conf_php(ini_get($aParametro['name']), $aParametro['valorpadrao'], $aParametro['bool'],
                                                              $aParametro['operacao']) ) {
            $aListaParam[] = $aParametro['valorpadrao'];
        }
    }

    if (count(@$aListaParam) > 0) {
         $lErroParam = true;
    }

    $i = 0;
    foreach ($oXmlEst->database->parametro as $aParametro) {

      $sqlSettings  = " SELECT current_setting('{$aParametro['name']}') ";
      $rsSettings   = $db_stdlib->db_query($sqlSettings);
      $iSettings    = $rsSettings->rowCount();
      if ($iSettings > 0) {

        $oSettings  = $rsSettings->fetch();
        if (isset($oSettings->current_setting) != "") {

          if ($aParametro['bool'] == 'true') {

            $sqlSettingsServer  = " SELECT current_setting('server_version_num') ";
            $rsSettingsServer   = $db_stdlib->db_query($sqlSettingsServer);
            $iSettingsServer    = $rsSettingsServer->rowCount();
            if ($iSettingsServer > 0) {

              $oSettingsServer = $rsSettingsServer->fetch();
              if ($aParametro['valor_min'] > $oSettingsServer->current_setting ||
                  $aParametro['valor_max'] < $oSettingsServer->current_setting) {

                $lErroSetings            = true;
                $aListaParamPostgre[$i] = $aParametro['name'];
                $aItemPostgre[$i]       = $oSettings->current_setting;
              } else {

                $sStrpos = strpos($aParametro['valorpadrao'],'*');
                if ($sStrpos === false) {

                  if ($oSettings->current_setting != $aParametro['valorpadrao']) {

                      $lErroSetings            = true;
                    $aListaParamPostgre[$i] = $aParametro['name'];
                    $aItemPostgre[$i]       = $oSettings->current_setting;
                  }
                } else {

                    $sVersaoConfigurada = substr($aParametro['valorpadrao'], 0,$sStrpos-1);
                    $sVersaoPostgre     = substr($oSettings->current_setting, 0,$sStrpos-1);
                    
                    if ($sVersaoPostgre != $sVersaoConfigurada) {

                        $lErroSetings            = true;
                        $aListaParamPostgre[$i] = $aParametro['name'];
                        $aItemPostgre[$i]       = $sVersaoPostgre;
                    }
                }
              }
            } else {

                $lErroSetings            = true;
                $aListaParamPostgre[$i]  = $aParametro['name'];
                $aItemPostgre[$i]        = "Nenhum registro encontrado!";
            }
          } else {

            if ($oSettings->current_setting != $aParametro['valorpadrao']) {

              $lErroSetings            = true;
              $aListaParamPostgre[$i] = $aParametro['name'];
              $aItemPostgre[$i]       = $oSettings->current_setting;
            }
          }
        } else {

          $lErroSetings            = true;
          $aListaParamPostgre[$i]  = $aParametro['name'];
          $aItemPostgre[$i]        = "Nenhum registro encontrado!";
        }
      } else {

        $lErroSetings            = true;
        $aListaParamPostgre[$i] = $aParametro['name'];
        $aItemPostgre[$i]       = "Nenhum registro encontrado!";
      }
      $i++;
    }
    $sDiretorioDbportal = $oXmlEst->diretorio;

    if ( isset($sDiretorioDbportal['name']) ) {
    $sNomePadrao = $sDiretorioDbportal['name'];
    }

    if (file_exists($sDirDocumentRoot."tmp/")) {

      $dirTmpDbPortal = fopen($sDirDocumentRoot."tmp/dir.txt", "w");
      if ($dirTmpDbPortal == false) {

        $lDirTmpDbPortalExist = false;
        $dirMsgDbportal       = $sDirDocumentRoot."tmp/";
      }
    } else {
      $lDirTmpDbPortalExist   = false;
      $dirMsgDbportal         = $sDirDocumentRoot."tmp/";
    }

    if ($lDirTmpRaizExist == false || $lDirTmpDbPortalExist == false) {
      $lErroDirTmp = true;
    }

    if ($lBrowser != true || $lErroMod == true || $lErroParam == true || $lErroDirTmp == true || $lErroSetings == true) {
         $lErro = true;
    }

    if (!isset($sMsgCabecalho)) {

        if (!isset($sNomePadrao) && empty($sNomePadrao)) {
          $sNomePadrao = 'sistema';
        }

      $sMsgCabecalho  = "Antes de prosseguir com o login, no {$sNomePadrao}, voc&ecirc; ";
      $sMsgCabecalho .= "precisa instalar as extens&otilde;es do servidor que est&atilde;o pendentes.";
    }
}

//  BEGIN: HTML
include $Services_Skins->getPathFile('dashboard','html_start.php');
    //  BEGIN: Head
    include $Services_Skins->getPathFile('dashboard','head.php');
    //  END: Head

    //  BEGIN: Body
    include $Services_Skins->getPathFile('login','body_start.php');
        //  BEGIN: Content
        include $Services_Skins->getPathFile('login','body_content_start.php');

            //  ----Páginas que será carregada-----

            ?>
                <section class="container">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="col-lg-10 col-md-6 col-10 box-shadow-2 p-0">
                            <div class="card border-grey border-lighten-3 px-1 py-1 m-0">
                                <div class="card-header border-0">
                                    <div class="text-center mb-1">
                                        <img src="<?php echo $Services_Skins->getSkinLink(); ?>app-assets/images/logo/logo.png" alt="branding logo">
                                    </div>
                                    <div class="font-large-1 text-center">
                                        Verificação de Configurações 
                                    </div>
                                    <div class="font-medium-1 text-center">
                                        Instala&ccedil;&atilde;o de extens&otilde;es pendentes do servidor
                                    </div>
                                </div>
                                <div class="card-content">

                                    <?php 

                                    if ( isset($_DB_VALIDA_REQUISITOS) && $_DB_VALIDA_REQUISITOS == true ) {
                                        if (isset($lErro) && $lErro == true) { 

                                            ?>
                                        
                                        <table border="0" width="100%">
                                            <tr align="center">
                                                <td>
                                                <b>Verificação de Configurações</b>
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td width="0" height="0" nowrap="nowrap"><?php echo $sMsgCabecalho; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Se preferir entre em contato com o administrador de seu sistema.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Segue a lista abaixo:</b></td>
                                                    </tr>
                                                </table>

                                        <?php
                                        if (count(@$aListaModulos) > 0) {

                                          echo "<table border=0 cellpadding=0 cellspacing=2 height=100%>";
                                          echo "<tr rowspan=0>";
                                          echo "  <td><b>Módulos Pendentes:</b></td>";
                                          echo "</tr>";

                                          foreach ($aListaModulos as $aValCampo) {

                                              echo "<tr id=lista_pendente_tr rowspan=0>";
                                              echo "  <td id=lista_pendente_td>".$aValCampo."</td>";
                                              echo "</tr>";

                                          }
                                          echo "</table>";

                                        }

                                        if (count(@$aListaParam) > 0) {

                                          echo "<table border=0 cellpadding=0 cellspacing=2 height=100%>";
                                          echo "<tr rowspan=0>";
                                          echo "  <td><b>Parâmetro PHP.INI:</b></td>";
                                          echo "  <td><b>Nome no PHP.INI:</b></td>";
                                          echo "  <td><b>Valor Requirido:</b></td>";
                                          echo "  <td><b>Valor Encontrado:</b></td>";
                                          echo "</tr>";

                                          foreach ($oXmlEst->parametros->parametro as $aParametro) {

                                            if ( !$Services_Funcoes->db_compara_conf_php(ini_get($aParametro['name']), $aParametro['valorpadrao'], $aParametro['bool'],
                                                                              $aParametro['operacao']) ) {

                                              echo "<tr id=lista_pendente_tr rowspan=0>";
                                              echo "  <td id=lista_pendente_td>".$aParametro['label']."</td>";
                                                    echo "  <td id=lista_pendente_td>".$aParametro['name']."</td>";
                                              echo "  <td id=lista_pendente_td>".$aParametro['label_valorpadrao']." </td>";

                                              if ($aParametro['bool'] == 'true'){

                                                if (ini_get($aParametro['name']) == 1) {
                                                  echo "  <td id=lista_pendente_td>On</td>";
                                                } else if (ini_get($aParametro['name']) == 0) {
                                                  echo "  <td id=lista_pendente_td>Off</td>";
                                                }else{
                                                  echo "  <td id=lista_pendente_td>".ini_get($aParametro['name'])."</td>";
                                                }

                                              } else {

                                                if (ini_get($aParametro['name']) == 1) {
                                                  echo "  <td id=lista_pendente_td>On</td>";
                                                } else if (ini_get($aParametro['name']) == 0) {
                                                  echo "  <td id=lista_pendente_td>Off</td>";
                                                }else{
                                                  echo "  <td id=lista_pendente_td>".ini_get($aParametro['name'])."</td>";
                                                }

                                              }
                                              echo "</tr>";
                                            }
                                          }
                                          echo "</table>";
                                        }

                                        if (count(@$aListaParamPostgre) > 0) {

                                          echo "<table border=0 cellpadding=0 cellspacing=2 height=100%>";
                                          echo "<tr rowspan=0>";
                                          echo "  <td><b>Parâmetro POSTGRESQL:</b></td>";
                                          echo "  <td><b>Nome no POSTGRESQL:</b></td>";
                                          echo "  <td><b>Valor Requirido:</b></td>";
                                          echo "  <td><b>Valor Encontrado:</b></td>";
                                          echo "</tr>";

                                          $i = 0;
                                          foreach ($oXmlEst->database->parametro as $aParametro) {

                                            if ($aParametro['name'] == $aListaParamPostgre[$i]) {
                                               echo "<tr id=lista_pendente_tr rowspan=0>";
                                               echo "  <td id=lista_pendente_td>".$aParametro['label']."</td>";
                                               echo "  <td id=lista_pendente_td>".$aParametro['name']."</td>";

                                                 if ($aParametro['bool'] == 'true') {
                                                    $sStrpos = strpos($aParametro['valorpadrao'],'*');

                                                    if ($sStrpos === false) {
                                                       echo "  <td id=lista_pendente_td>".$aParametro['valorpadrao']."</td>";
                                                    } else {
                                                       $sVersaoConfigurada = substr($aParametro['valorpadrao'], 0,$sStrpos-1);
                                                       echo "  <td id=lista_pendente_td>".$sVersaoConfigurada."</td>";
                                                    }

                                                 } else {
                                                    echo "  <td id=lista_pendente_td>".$aParametro['valorpadrao']."</td>";
                                                 }

                                               echo "  <td id=lista_pendente_td>".$aItemPostgre[$i]." </td>";
                                               echo "</tr>";
                                            }
                                            $i++;
                                          }
                                          echo "</table>";
                                        }

                                        if ($lDirTmpRaizExist == false || $lDirTmpDbPortalExist == false) {

                                          echo "<table border=0 height=100%>";
                                          echo "<tr>";
                                          echo "  <td><b>Diretório(s) não encontrado(s) ou sem permissão de escrita:</b></td>";
                                          echo "</tr>";
                                          echo "<tr id=lista_pendente_tr rowspan=0>";

                                            if ($lDirTmpRaizExist == false) {

                                               echo "<td id=lista_pendente_td>".$dirMsgRaiz."</td>";

                                             }
                                          echo "</tr>";
                                          echo "<tr id=lista_pendente_tr rowspan=0>";

                                            if ($lDirTmpDbPortalExist == false) {

                                               echo "<td id=lista_pendente_td>".$dirMsgDbportal."</td>";

                                             }
                                        }

                                        if ($lBrowser == false) {

                                          echo "<table border=0 height=100%>";
                                          echo "<tr rowspan=0>";
                                          echo "  <td><b>Browser Incompativel:</b></td>";
                                          echo "</tr>";
                                          echo "<tr id=lista_pendente_tr rowspan=0>";
                                          echo "  <td id=lista_pendente_td>".$sBrowser."</td>";
                                          echo "</tr>";
                                          echo "</table>";

                                        }

                                        if (isset($sMsgErro) && $sMsgErro != "") {

                                          echo "<table border=0 height=100%>";
                                          echo "<tr rowspan=0>";
                                          echo "  <td><b>Parametros de configuração do PostgreSQL:</b></td>";
                                          echo "</tr>";
                                          echo "<tr id=lista_pendente_tr rowspan=0>";
                                          echo "  <td id=lista_pendente_td>".$sMsgErro."</td>";
                                          echo "</tr>";
                                          echo "</table>";

                                        }

                                        if ($lErroConf == true) {

                                          echo "<table border=0 height=100%>";
                                          echo "<tr rowspan=0>";
                                          echo "  <td><b>Arquivo Inexistente ou Parametro(s) de Configuração não Configurado(s) Corretamente:</b></td>";
                                          echo "</tr>";
                                          echo "<tr id=lista_pendente_tr rowspan=0>";
                                          echo "  <td id=lista_pendente_td>".$sMsgConf."</td>";
                                          echo "</tr>";
                                          echo "</table>";

                                        }

                                          echo "</tr>";
                                          echo "</table>";
                                          echo "<p></p>";
                                        ?>
                                          </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <?php
                                            } else {

                                                if ( isset($lVerificaRequisitos) && $lVerificaRequisitos == true ) {
                                        ?>
                                        <table border="0" width="100%">
                                            <tr align="center">
                                                <td>
                                                <div id="lista_pendente" align="left">
                                                <div id="titulo">Verificação de Configurações</div>
                                                <div id="conteudo">
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td width="0" height="0" nowrap="nowrap"></td>
                                                    </tr>
                                                </table>
                                                </div>
                                            <?php
                                                    if ($lErroConf == true) {

                                                      echo "<table border=0 height=100%>";
                                                      echo "<tr rowspan=0>";
                                                      echo "  <td><b>Arquivo Inexistente ou Parametro(s) de Configuração não Configurado(s) Corretamente:</b></td>";
                                                      echo "</tr>";
                                                      echo "<tr id=lista_pendente_tr rowspan=0>";
                                                      echo "  <td id=lista_pendente_td>".$sMsgConf."</td>";
                                                      echo "</tr>";
                                                      echo "</table>";

                                                    } else {
                                            ?>
                                            <table width="100%" border="0" cellpadding="4" cellspacing="4">
                                              <tr>
                                                <td width="0" height="0" nowrap="nowrap"> <?php echo $sMsgCabecalho; ?></td>
                                              </tr>
                                              <tr>
                                                <td>Se preferir entre em contato com o administrador de seu sistema.</td>
                                              </tr>
                                              <tr>
                                                <td>Segue a lista abaixo:</td>
                                              </tr>
                                            </table>

                                            <table border=0 height=100%>
                                              <tr rowspan=0>
                                                <td><b>Verifica Config. Homologadas do Sistema:</b></td>
                                              </tr>
                                              <tr id=lista_pendente_tr rowspan=0>
                                                <td id=lista_pendente_td>Configurações OK.</td>
                                              </tr>
                                            </table>

                                                <?} ?>
                                              </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <?php
                                                } else {

                                                    if (isset($lErro) && $lErro == false) {
                                                        $_SESSION['DB_configuracao_ok'] = '';
                                                        $Services_Funcoes->redireciona($Services_Funcoes->url_acesso() . "files/login.php");
                                                    }
                                                }
                                            }
                                        } else {

                                            if ($lErroConf == true) {
                                        ?>
                                        <table border='0' width='100%'>
                                            <tr align='center'>
                                                <td>
                                                <table width='100%' border='0' cellpadding='0' cellspacing='0'>
                                                    <tr>
                                                        <td width='0' height='0' nowrap='nowrap'></td>
                                                    </tr>
                                                </table>
                                                </div>
                                                <table border='0' width='100%' height='100%'>
                                                    <tr rowspan='0'>
                                                        <td><b>Arquivo Inexistente ou Parametro(s) de Configuração não Configurado(s) Corretamente:</b></td>
                                                    </tr>
                                                    <tr id='lista_pendente_tr' rowspan='0'>
                                                        <td id='lista_pendente_td'><?php echo $sMsgConf; ?></td>
                                                    </tr>
                                                </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <?php
                                            } else {

                                                if ( isset($lVerificaRequisitos) && $lVerificaRequisitos == true ) {
                                        ?>
                                        <table border="0" width="100%">
                                          <tr align="center">
                                            <td>
                                            <?php
                                              if ($lErroConf == true) {

                                                echo "<table border=0 height=100%>";
                                                echo "<tr rowspan=0>";
                                                echo "  <td><b>Arquivo Inexistente ou Parametro(s) de Configuração não Configurado(s) Corretamente:</b></td>";
                                                echo "</tr>";
                                                echo "<tr id=lista_pendente_tr rowspan=0>";
                                                echo "  <td id=lista_pendente_td>".$sMsgConf."</td>";
                                                echo "</tr>";
                                                echo "</table>";

                                              } else {
                                            ?>
                                            <table border=0 height=100%>
                                              <tr rowspan=0>
                                                <td><b>Verifica Config. Homologadas do Sistema:</b></td>
                                              </tr>
                                              <tr id=lista_pendente_tr rowspan=0>
                                                <td id=lista_pendente_td>Configurações OK.</td>
                                              </tr>
                                            </table>
                                            <?php } ?>
                                            </td>
                                          </tr>
                                        </table>
                                        <?php
                                                } else {
                                                    $_SESSION['DB_configuracao_ok'] = '';
                                                    $Services_Funcoes->redireciona($Services_Funcoes->url_acesso() . "files/login.php");
                                                }
                                            }
                                        }
                                        ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
    <?php
        include $Services_Skins->getPathFile('login','body_content_end.php');

        //  BEGIN: Header
        include $Services_Skins->getPathFile('login','body_footer.php');
        //  END: Header

    //  END: Body
    include $Services_Skins->getPathFile('login','body_end.php'); 

//  END: HTML
include $Services_Skins->getPathFile('dashboard','html_end.php'); 