<form class="form" method="post" name="form1" id="form1" action="<?php echo $Services_Funcoes->url_acesso_in().$pagina; ?>">
  <div class="form-body">
      <h4 class="form-section">
        <i class="ft-flag"></i> Dados do Módulo</h4>
      <div class="form-group">
        <label for="nomemod"><?php echo $Lnomemod; ?></label>
        <?php $db_funcoes->db_input('nomemod', 70, '', true, 'text', $db_opcao); ?>
      </div>
      <div class="form-group">
        <label for="descricao"><?php echo $Ldescricao; ?></label>
        <?php $db_funcoes->db_textarea('descricao', 3, 50, '', true, 'text', $db_opcao); ?>
      </div>
      <div class="form-group">
        <label for="dataincl"><?php echo $Ldataincl; ?></label>
        <?php
        $db_funcoes  = new dbforms\db_funcoes;
        $dataincl_dia = date("d");
        $dataincl_mes = date("m");
        $dataincl_ano = date("Y");
        $db_funcoes->db_inputdata("dataincl",@$dataincl_dia,@$dataincl_mes,@$dataincl_ano,1,'text',$db_opcao);
        ?>
      </div>
      <div class="form-group">
        <label for="ativo"><b>Ativo</b></label>
        <?php
        $xx = array("t"=>"SIM","f"=>"NAO");
        $db_funcoes->db_select('ativo',$xx,true,1,"");
        ?>
      </div>
  </div>

  <div class="form-actions">
      <input class="btn btn-dark" onClick="Botao = 'incluir'" accesskey="i" type="submit" name="incluir" id="incluir2" value="Incluir" <?php echo isset($retorno)?"disabled":"" ?>>

      <input class="btn btn-dark" name="alterar" accesskey="a" type="submit" id="alterar2" name="alterar" value="Alterar" <?php echo !isset($retorno)?"disabled":"" ?>>

      <input class="btn btn-dark" name="excluir" accesskey="e" type="submit" id="excluir2" name="excluir" value="Excluir" onClick="return confirm('Quer realmente excluir este registro?')" <?php echo !isset($retorno)?"disabled":"" ?>> 
      <input class="btn btn-dark" type="button" data-toggle="modal" data-target="#xlarge" onclick="js_pesquisa();" value="Procurar">

      <input class="btn btn-dark" type="button"  onClick="location.href='<?php echo $Services_Funcoes->url_acesso_in(); ?>sys3_tabelas001/<?php echo base64_encode("codmod=$retorno&manutabela=true") ?>'" value="Ver Tabelas" <?php echo !isset($retorno)?"disabled":"" ?>>
      <input type="hidden" name="codmod" value="<?=@$codmod?>">

  </div>
</form>
<script language="JavaScript" type="text/javascript" src="<?php echo $Services_Funcoes->url_acesso(); ?>scripts/Extensions_Funcoes.js?n=<?php echo rand(0,1000); ?>"></script>
<script type="text/javascript">
document.form1.nomemod.focus();

function js_pesquisa() {
  js_OpenJanelaIframe_Novo('#modal1_conteudo','<?php echo $Services_Funcoes->url_acesso(); ?>files/pages/func_db_sysmodulo.php?funcao_js=parent.js_retornopesquisa|0', '100%', '580px');
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