<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$db_stdlib        = new libs\db_stdlib; 
$db_funcoes       = new dbforms\db_funcoes;

$db_db_sysmodulo  = new classes\db_db_sysmodulo;
$db_db_sysmodulo->rotulo->label("codmod");
$db_db_sysmodulo->rotulo->label("nomemod");

$Services_Skins   = new libs\Services_Skins;

//  Pega um vetor e cria variáveis globais pelo índice do vetor.
$db_stdlib->db_postmemory($_GET);

$GLOBALS['db_corcabec']   = '';
$GLOBALS['cor1']          = '';
$GLOBALS['cor2']          = '';


//  BEGIN: HTML
include $Services_Skins->getPathFile('dashboard','html_start.php');
    //  BEGIN: Head
    include $Services_Skins->getPathFile('dashboard','head.php');
    //  END: Head

    //  BEGIN: Body
    include $Services_Skins->getPathFile('dashboard','body_start.php');
?>
  <style type="text/css">
    .table th, .table td {
      border:0;
    }
    .card-body {
      padding:0;
      padding-top:21px;
    }
  </style>
  <div class="card-body">
      <form class="form form-horizontal" name="form2">
        <input type="hidden" name="funcao_js" value="<?php echo $funcao_js; ?>" />
        <div class="form-body center">
          <h5></h5>
        </div>
        <div class="form-body">
            <div class="form-group row" style="margin-bottom:0;">
                <label class="col-md-3 label-control" for="projectinput5" title="<?=$Tcodmod?>"><?=$Lcodmod?></label>
                <div class="col-md-9">
                  <?php $db_funcoes->db_input("codmod",4,$Icodmod,true,"text",4,"","chave_codmod"); ?>
                </div>
            </div>
        </div>
        <div class="form-body">
            <div class="form-group row" style="margin-bottom:0;">
                <label class="col-md-3 label-control" for="projectinput5" title="<?=$Tnomemod?>"><?=$Lnomemod?></label>
                <div class="col-md-9">
                  <?php $db_funcoes->db_input("nomemod",40,$Inomemod,true,"text",4,"","chave_nomemod"); ?>
                </div>
            </div>
        </div>

        <div class="form-actions center" style="margin-top:0;padding:0;padding-top:10px;">
          <input class="btn btn-sm btn-info" name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
          <input class="btn btn-sm btn-info" name="limpar" type="reset" id="limpar" value="Limpar" />
          <input type="button" name="fechar" id="fechar" class="btn btn-sm btn-info" onclick="parent.js_fecharModal('#xlarge');return false" value="Fechar" />
        </div>
    </form>
  </div>
  <table class="table table-responsive-lg">
    <tr> 
      <td align="center" valign="top"> 
        <?php 
        if( !isset($pesquisa_chave) ) {
          if( isset($campos)==false ) {
              if( file_exists($_SERVER['DOCUMENT_ROOT']."/functions/db_func_db_sysmodulo.php" ) == true ) {
                include($_SERVER['DOCUMENT_ROOT']."/functions/db_func_db_sysmodulo.php");
              } else {
                $campos = "db_sysmodulo.*";
              }
          }
          if( isset($chave_codmod) and (trim($chave_codmod) != "" ) ){
             $sql = $db_db_sysmodulo->sql_query($chave_codmod,$campos,"codmod");
          } else if ( isset($chave_nomemod) and (trim($chave_nomemod) != "" ) ){
             $sql = $db_db_sysmodulo->sql_query("",$campos,"nomemod"," nomemod like '$chave_nomemod%' ");
          } else {
             $sql = $db_db_sysmodulo->sql_query("",$campos,"codmod","");
          }
          $repassa = array();
          if(isset($chave_nomemod)){
            $repassa = array("chave_codmod"=>$chave_codmod,"chave_nomemod"=>$chave_nomemod);
          }
          
          $db_stdlib->db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
        } else {
          if ( $pesquisa_chave != null and $pesquisa_chave != "" ) {
            $result = $db_db_sysmodulo->sql_record($db_db_sysmodulo->sql_query($pesquisa_chave));
            if ( $db_db_sysmodulo->numrows !=0 ) {
              $db_stdlib->db_fieldsmemory($result,0);
              echo "<script>".$funcao_js."('$nomemod',false);</script>";
            }else{
             echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
            }
          }else{
           echo "<script>".$funcao_js."('',false);</script>";
          }
        }
        ?>
       </td>
     </tr>
  </table>
<?php
    //  END: Body
    include $Services_Skins->getPathFile('dashboard','body_end.php'); 

//  END: HTML
include $Services_Skins->getPathFile('dashboard','html_end.php'); 