<section id="basic-form-layouts">
<form class="form" method="post" name="form1" id="form1" action="<?php echo $Services_Funcoes->url_acesso_in().$pagina; ?>">
  <input type="hidden" name="codfuncao" value="<?=@$codfuncao?>">
  <div class="row match-height">
    <div class="col-md-5">
      <div class="card">
        <div class="form-body">
            <h4 class="form-section"><i class="ft-flag"></i> Cadastro de Funções</h4>
            
            <div class="form-group">
              <div class="form-check">
                  <input class="form-check-input" style="cursor:pointer;" type="radio" name="triggerfuncao" id="triggerfuncao1" value="1" <?php echo @$triggerfuncao=="1"?"checked":"" ?> >
                  <label class="form-check-label" style="cursor:pointer;" for="triggerfuncao1"><strong>Trigger</strong></label>
              </div>
              <div class="form-check">
                  <input class="form-check-input" style="cursor:pointer;" type="radio" name="triggerfuncao" id="triggerfuncao0" value="1" <?php echo @$triggerfuncao=="0"?"checked":"" ?> >
                  <label class="form-check-label" style="cursor:pointer;" for="triggerfuncao0"><strong>Função</strong></label>
              </div>
              <div class="form-check">
                  <input class="form-check-input" style="cursor:pointer;" type="radio" name="triggerfuncao" id="triggerfuncao2" value="2" <?php echo @$triggerfuncao=="2"?"checked":"" ?> >
                  <label class="form-check-label" style="cursor:pointer;" for="triggerfuncao2"><strong>View</strong></label>
              </div>
            </div>

            <div class="form-group">
              <label for="nomefuncao" title="<?php echo $Tnomefuncao; ?>"><strong><?php echo $Lnomefuncao; ?></strong></label>
              <?php $db_funcoes->db_input('nomefuncao', 100, '', true, 'text', $db_opcao); ?>
            </div>
            <div class="form-group">
              <label for="nomearquivo" title="<?php echo $Tnomearquivo; ?>"><?php echo $Lnomearquivo; ?></label>
              <?php $db_funcoes->db_input('nomearquivo', 100, '', true, 'text', $db_opcao); ?>
            </div>
            <div class="form-group" title="<?php echo $Tobsfuncao; ?>">
              <label for="obsfuncao"><?php echo $Lobsfuncao; ?></label>
              <?php $db_funcoes->db_textarea('obsfuncao', 3, 50, '', true, 'text', $db_opcao); ?>
            </div>
            <div class="form-group" title="<?php echo $Tcorpofuncao; ?>">
              <label for="corpofuncao"><?php echo $Lcorpofuncao; ?></label>
              <?php $db_funcoes->db_textarea('corpofuncao', 10, 50, '', true, 'text', $db_opcao); ?>
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
<script type="text/javascript">
document.form1.nomemod.focus();

function js_pesquisa() {
  js_OpenJanelaIframe_Novo('#modal1_conteudo','<?php echo $Services_Funcoes->url_acesso(); ?>files/pages/func_db_sysfuncoes001.php?funcao_js=parent.js_retornopesquisa|0', '100%', '580px');
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