<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$db_stdlib        = new libs\db_stdlib; 
$db_funcoes       = new dbforms\db_funcoes;

$cl_systriggers  = new classes\cl_systriggers;
$cl_systriggers->rotulo->label("codtrigger");
$cl_systriggers->rotulo->label("nometrigger");

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
                <label class="col-md-3 label-control" for="projectinput5" title="<?=$Tcodtrigger?>"><?=$Lcodtrigger?></label>
                <div class="col-md-9">
                  <?php $db_funcoes->db_input("codtrigger",4,$Icodtrigger,true,"text",4,"","chave_codtrigger"); ?>
                </div>
            </div>
        </div>
        <div class="form-body">
            <div class="form-group row" style="margin-bottom:0;">
                <label class="col-md-3 label-control" for="projectinput5" title="<?=$Tnometrigger?>"><?=$Lnometrigger?></label>
                <div class="col-md-9">
                  <?php $db_funcoes->db_input("nometrigger",40,$Inometrigger,true,"text",4,"","chave_nometrigger"); ?>
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
              if( file_exists($_SERVER['DOCUMENT_ROOT']."/files/dbinputs/db_func_db_systriggers.php" ) == true ) {
                include($_SERVER['DOCUMENT_ROOT']."/files/dbinputs/db_func_db_systriggers.php");
              } else {
                $campos = "db_systriggers.*";
              }
          }
          if( isset($chave_codtrigger) and (trim($chave_codtrigger) != "" ) ){
             $sql = $cl_systriggers->sql_query($chave_codtrigger,$campos,"codtrigger");
          } else if ( isset($chave_nometrigger) and (trim($chave_nometrigger) != "" ) ){
             $sql = $cl_systriggers->sql_query("",$campos,"nometrigger"," nometrigger like '$chave_nometrigger%' ");
          } else {
             $sql = $cl_systriggers->sql_query("",$campos,"codtrigger","");
          }
          $repassa = array();
          if(isset($chave_nometrigger)){
            $repassa = array("chave_codtrigger"=>$chave_codtrigger,"chave_nometrigger"=>$chave_nometrigger);
          }
          
          $db_stdlib->db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
        } else {
          if ( $pesquisa_chave != null and $pesquisa_chave != "" ) {
            $result = $cl_systriggers->sql_record($cl_systriggers->sql_query($pesquisa_chave));
            if ( $cl_systriggers->numrows !=0 ) {
              $db_stdlib->db_fieldsmemory($result,0);
              echo "<script>".$funcao_js."('$nometrigger',false);</script>";
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