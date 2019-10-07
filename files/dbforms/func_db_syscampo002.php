<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$db_stdlib        = new libs\db_stdlib; 
$db_funcoes       = new dbforms\db_funcoes;

$cl_syscampo  = new classes\cl_syscampo;
$cl_syscampo->rotulocl->label("codcam");
$cl_syscampo->rotulocl->label("nomecam");

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

    include $Services_Skins->getPathFile('dashboard','body_start.php');
?>
  <div class="card-body">
      <form class="form form-horizontal" name="form2">
        <input type="hidden" name="funcao_js" value="<?php echo $funcao_js; ?>" />
        <div class="form-body center">
          <h5>Selecione o Campo:</h5>
        </div>
        <div class="form-body">
            <div class="form-group row" style="margin-bottom:0;">
                <div class="col-md-9">
                <?php
                $result   = $db_stdlib->db_query("SELECT codcam, nomecam FROM db_syscampo WHERE nomecam LIKE '$campo%'");
                $numrows  = $result->rowCount();
                if ( $numrows > 0 ) {
                  echo "<select name=\"voltacampo\" id=\"voltacampo\" class=\"form-control\" size=\"15\">\n";
                  foreach ( $result as $linha ) {
                    echo "<option value=\"".$linha->codcam."\">".$linha->nomecam."</option>\n";
                  }
                  echo "</select>\n";
                } else {
                  echo "Campo não encontrado\n";
                }
                ?>
                </div>
            </div>
        </div>

        <div class="form-actions center" style="margin-top:0;padding:0;padding-top:10px;">
          <input type="button" name="inserir" class="btn btn-sm btn-info" onClick="js_inserir()" value="Inserir">
          <input type="button" name="fechar" class="btn btn-sm btn-info" onClick="parent.js_fecharModal('#xlarge');return false" value="Fechar">
        </div>
      </form>
  </div>
  <script type="text/javascript">
    function js_inserir() {
      var F = document.form2;
      var SI = F.voltacampo.selectedIndex;
      if(SI != -1) {
        parent.js_insSelect(F.voltacampo.options[SI].text,F.voltacampo.options[SI].value);
        F.voltacampo.options[SI] = null;
        if(SI <= (F.voltacampo.length - 1)) 
            F.voltacampo.options[SI].selected = true;  
        parent.js_trocacordeselect();
        //parent.js_fecharModal('#xlarge');
      }
    }
  </script>
<?php
    //  END: Body
    include $Services_Skins->getPathFile('dashboard','body_end.php'); 

//  END: HTML
include $Services_Skins->getPathFile('dashboard','html_end.php'); 