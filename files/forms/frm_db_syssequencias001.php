<form name="form1" method="post">
<section id="basic-form-layouts">
  <div class="row match-height">
    <div class="col-md-5">
      <div class="card">
        <div class="card-content collapse show">
          <div class="card-body">
            <div class="form-body">
              <h4 class="form-section"><i class="ft-flag"></i> Organizar Campos</h4>
          
              <div class="form-group">
              <?php
              $db_funcoes->db_label("db_sysarquivo","tabela");
              if ( isset($GLOBALS["retorno"]) ) {
              $result = $db_stdlib->db_query("SELECT codarq, nomearq FROM db_sysarquivo WHERE codarq = $retorno");
              $db_stdlib->db_fieldsmemory($result,0);         
              }
              echo $db_funcoes->db_text("tabela",40,40,@trim($nomearq),@$codarq,3)
              ?>
              </div>

              <div class="form-group">
                <label for="campos"><strong>Campos:</strong></label>

                <select name="campos" id="campos" class="form-control" onChange="document.form1.atualizar.value = 'Atualizar'" size="17" style="width:250px">
                  <?php
                  if ( isset($GLOBALS["retorno"]) ) {
                    $sql_result = $db_stdlib->db_query("SELECT c.codcam,c.nomecam,ac.codsequencia FROM db_syscampo c INNER JOIN db_sysarqcamp ac ON ac.codcam = c.codcam WHERE ac.codarq = $retorno");

                    $result   = $sql_result;

                    $numrows = $result->rowCount();
                    
                    $codsequencia = 0;

                    if($numrows > 0) {
                      foreach ($result as $linha) {
                        if( $linha->codsequencia != "0" and $codsequencia == 0 ) {
                          $codsequencia = $linha->codsequencia;
                        }

                        echo "<option value=\"".$linha->codcam."#".$linha->codsequencia."#".trim($linha->nomecam)."\" ".(($linha->codsequencia)!=0?"selected":"").">".$linha->nomecam."</option>\n";
                      }
                    }
                  }
                  if($codsequencia == "0") {
                    $incrseq     = "1";
                    $minvalueseq = "1";
                    $maxvalueseq = "9223372036854775807";
                    $startseq    = "1";
                    $cacheseq    = "1";  
                    $nomesequencia = "";
                  } else {
                    $result = $db_stdlib->db_query("SELECT incrseq,minvalueseq,maxvalueseq,startseq,cacheseq,nomesequencia FROM db_syssequencia WHERE codsequencia = $codsequencia");
                    $db_stdlib->db_fieldsmemory($result,0);
                  }
                 ?>
                </select> 
                <input type="hidden" name="codsequencia" value="<?=@$codsequencia?>"> 
              </div>

              

              <div class="form-actions">
                <input name="atualizar" class="btn btn-dark btn-min-width mr-1 mb-1" onClick="return confirm('Atualizar sequencia?')" accesskey="a" type="submit" value="Atualizar" disabled> 
                <input type="button" name="Button" class="btn btn-dark btn-min-width mr-1 mb-1" onClick="js_retsel()" value="Retirar Sele&ccedil;&atilde;o de Campos">
              </div>
          </div>
        </div>
      </div>
    </div>
    </div>

    <div class="col-md-5">
      <div class="card">
        <div class="card-content collapse show">
          <div class="card-body">
            <h4 class="form-section"><i class="ft-flag"></i></h4>

            <div class="form-group">
              <label for="nomesequencia" title="<?php echo $Tnomesequencia; ?>"><?php echo $Lnomesequencia; ?></label>
              <?php $db_funcoes->db_input('nomesequencia', 40, '', true, 'text', $db_opcao); ?>
            </div>

            <div class="form-group">
              <label for="incrseq" title="<?php echo $Tincrseq; ?>"><?php echo $Lincrseq; ?></label>
              <?php $db_funcoes->db_input('incrseq', 20, '', true, 'text', $db_opcao); ?>
            </div>

            <div class="form-group">
              <label for="minvalueseq" title="<?php echo $Tminvalueseq; ?>"><?php echo $Lminvalueseq; ?></label>
              <?php $db_funcoes->db_input('minvalueseq', 20, '', true, 'text', $db_opcao); ?>
            </div>
            <div class="form-group">
              <label for="maxvalueseq" title="<?php echo $Tmaxvalueseq; ?>"><?php echo $Lmaxvalueseq; ?></label>
              <?php $db_funcoes->db_input('maxvalueseq', 20, '', true, 'text', $db_opcao); ?>
            </div>

            <div class="form-group">
              <label for="startseq" title="<?php echo $Tstartseq; ?>"><?php echo $Lstartseq; ?></label>
              <?php $db_funcoes->db_input('startseq', 20, '', true, 'text', $db_opcao); ?>
            </div>

            <div class="form-group">
              <label for="cacheseq" title="<?php echo $Tcacheseq; ?>"><?php echo $Lcacheseq; ?></label>
              <?php $db_funcoes->db_input('cacheseq', 20, '', true, 'text', $db_opcao); ?>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</form>
<script language="JavaScript" type="text/javascript" src="<?php echo $Services_Funcoes->url_acesso(); ?>scripts/Extensions_Funcoes.js?n=<?php echo rand(0,1000); ?>"></script>
<script>

js_iniciar();
js_trocacordeselect();

function js_iniciar() {
  var F = document.form1;
  
  if(F.campos.selectedIndex == -1)
    F.atualizar.value = "Excluir Sequência";
  F.atualizar.disabled = false;
}
function js_retsel() {
  var F = document.form1.campos;
  for(i = 0;i < F.length;i++)
    F.options[i] = new Option(F.options[i].text,F.options[i].value);
  js_trocacordeselect();
  document.form1.atualizar.value = "Excluir Sequência";
}

function js_retornopesquisa(chave) {
  url       = '<?php echo $Services_Funcoes->url_acesso_in(); ?>';
  pagina    = '<?php echo $pagina; ?>';
  get       = btoa('retorno='+chave);

  link      = url+pagina+'/'+get;

  location.href = ''+link+'';
}

function js_fecharModal(modal) {
  $(modal).modal('hide');
  $('body').removeClass('modal-open');
  $('.modal-backdrop').remove();
}

</script>