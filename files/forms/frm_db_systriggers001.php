<section id="basic-form-layouts">
<form class="form" method="post" name="form1" id="form1" action="<?php echo $Services_Funcoes->url_acesso_in().$pagina; ?>">
  <input type="hidden" name="codtrigger" value="<?=@$codtrigger?>">
  <input type="hidden" name="trigger_erro" value="NULL">
  <div class="row match-height">
    <div class="col-md-5">
      <div class="card">
        <div class="form-body">
            <h4 class="form-section"><i class="ft-flag"></i> Cadastro de Triggers</h4>
            
            <div class="form-group">
              <label for="nometrigger" title="<?php echo $Tnometrigger; ?>"><strong><?php echo $Lnometrigger; ?></strong></label>
              <?php $db_funcoes->db_input('nometrigger', 100, '', true, 'text', $db_opcao); ?>
            </div>

            <div class="row">
              <div class="col-md-6">

                <div class="form-group">
                  <label for="quandotrigger"><strong>Quando disparar evento:</strong></label>
                  <select name="quandotrigger" id="quandotrigger"  class="form-control">
                    <option value="BEFORE" <? echo @$quandotrigger=="BEFORE"?"selected":"" ?>>Antes</option>
                    <option value="AFTER" <? echo @$quandotrigger=="AFTER"?"selected":"" ?>>Depois</option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">

                <div class="form-group">
                  <label for="quandotrigger"><strong>Evento:</strong></label>
                  <select name="eventotrigger" id="eventotrigger" class="form-control">
                    <option value="INSERT" <? echo @$eventotrigger=="INSERT"?"selected":"" ?>>Inserir</option>
                    <option value="UPDATE" <? echo @$eventotrigger=="UPDATE"?"selected":"" ?>>Atualizar</option>
                    <option value="INSERT OR UPDATE" <? echo @$eventotrigger=="INSERT OR UPDATE"?"selected":"" ?>>Inserir or Atualizar</option>
                    <option value="DELETE" <? echo @$eventotrigger=="DELETE"?"selected":"" ?>>Excluir</option>
                  </select> 
                </div>

              </div>

            </div>

              
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group" title="<?php echo $Tcodarq; ?>">
                  <?php
                    $db_funcoes->db_ancora(@$Lcodarq,"js_pesquisacodarq(true);",1);
                  
                    $db_funcoes->db_input('codarq',5,$Icodarq,true,'text',1," onchange='js_pesquisacodarq(false);'");
                  ?>
                </div>
              </div>

              <div class="col-md-8">
                <a>.</a>
                <div class="form-group" title="<?php echo $Tcodarq; ?>">
                  <?php $db_funcoes->db_input('nomearq',40,$Inomearq,true,'text',3,""); ?>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group" title="<?php echo $Tcodarq; ?>">
                  <?php
                    $db_funcoes->db_ancora(@$Lcodfuncao,"js_pesquisacodfuncao(true);",1);
                    $db_funcoes->db_input('codfuncao',5,$Icodfuncao,true,'text',1," onchange='js_pesquisacodfuncao(false);'");
                  ?>
                </div>
              </div>

              <div class="col-md-8">
                <a>.</a>
                <div class="form-group" title="<?php echo $Tnomefuncao; ?>">
                  <?php $db_funcoes->db_input('nomefuncao',40,$Inomefuncao,true,'text',3,""); ?>
                </div>
              </div>
            </div>
        </div>
        <div class="form-actions">
            <input class="btn btn-dark" onClick="Botao = 'incluir'" accesskey="i" type="submit" name="incluir" id="incluir2" value="Incluir" <?php echo isset($retorno)?"disabled":"" ?>>

            <input class="btn btn-dark" name="alterar" accesskey="a" type="submit" id="alterar2" name="alterar" value="Alterar" <?php echo !isset($retorno)?"disabled":"" ?>>

            <input class="btn btn-dark" name="excluir" accesskey="e" type="submit" id="excluir2" name="excluir" value="Excluir" onClick="return confirm('Quer realmente excluir este registro?')" <?php echo !isset($retorno)?"disabled":"" ?> >
            
            <input class="btn btn-dark" type="button" data-toggle="modal" data-target="#xlarge" onclick="js_pesquisa();" value="Procurar">
        </div>
      </div>
    </div>
  </div>
</form>
</section>
<script language="JavaScript" type="text/javascript" src="<?php echo $Services_Funcoes->url_acesso(); ?>scripts/Extensions_Funcoes.js?n=<?php echo rand(0,1000); ?>"></script>
<script>
function js_pesquisacodarq(chave){
  if(chave==true){
    js_OpenJanelaIframe_Novo('#modal1_conteudo','<?php echo $Services_Funcoes->url_acesso(); ?>files/pages/func_db_sysarquivo001.php?funcao_js=parent.js_preenchepesquisa|0|1', '100%', '580px');
  }else{
    js_OpenJanelaIframe_Novo('#modal1_conteudo','<?php echo $Services_Funcoes->url_acesso(); ?>files/pages/func_db_sysarquivo001.php?pesquisa_chave='+document.form1.codarq.value+'&funcao_js=parent.js_preenchepesquisa1', '100%', '580px');
  }
}
function js_preenchepesquisa(chave,chave1){
  js_fecharModal('#xlarge');
  document.form1.codarq.value = chave;
  document.form1.nomearq.value = chave1;
}
function js_preenchepesquisa1(chave,chave1){
  if(chave==true){
    document.form1.codarq.value = "";
    document.form1.nomearq.value = chave1;
    document.form1.codarq.focus();
  }else{
    document.form1.nomearq.value = chave;
  }
}
function js_pesquisacodfuncao(chave){
  if(chave==true){
    js_OpenJanelaIframe_Novo('#modal1_conteudo','<?php echo $Services_Funcoes->url_acesso(); ?>files/pages/func_db_sysfuncoes001.php?funcao_js=parent.js_preenchepesquisafun|0|1', '100%', '580px');
  }else{
    js_OpenJanelaIframe_Novo('#modal1_conteudo','<?php echo $Services_Funcoes->url_acesso(); ?>files/pages/func_db_sysfuncoes001.php?pesquisa_chave='+document.form1.codfuncao.value+'&funcao_js=parent.js_preenchepesquisafun1', '100%', '580px');
  }
}
function js_preenchepesquisafun(chave,chave1){
  js_fecharModal('#xlarge');
  document.form1.codfuncao.value  = chave;
  document.form1.nomefuncao.value = chave1;
}
function js_preenchepesquisafun1(chave,chave1){
  if(chave==true){
    document.form1.codfuncao.value = "";
    document.form1.nomefuncao.value = chave;
    document.form1.codfuncao.focus();
  }else{
    document.form1.nomefuncao.value = chave;
  }
}

function js_pesquisa() {
  js_OpenJanelaIframe_Novo('#modal1_conteudo','<?php echo $Services_Funcoes->url_acesso(); ?>files/pages/func_db_systriggers001.php?funcao_js=parent.js_retornopesquisa|0', '100%', '580px');
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