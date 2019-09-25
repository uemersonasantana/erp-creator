<form class="form" method="post" name="form1" id="form1" action="<?php echo $Services_Funcoes->url_acesso_in().$pagina; ?>">
  <input type="hidden" name="codarq" value="<?=@$codarq?>">
  <div class="form-body">
      <h4 class="form-section">
        <i class="ft-flag"></i> Dados da Tabela</h4>
      <div class="form-group">
        <label for="nomearq"><?php echo $Lnomearq; ?></label>
        <?php $db_funcoes->db_input('nomearq', 70, '', true, 'text', $db_opcao); ?>
      </div>
      <div class="form-group">
        <label for="rotulo"><?php echo $Lrotulo; ?></label>
        <?php $db_funcoes->db_input('rotulo', 50, '', true, 'text', $db_opcao); ?>
      </div>
      <div class="form-group">
        <label for="descricao"><?php echo $Ldescricao; ?></label>
        <?php $db_funcoes->db_textarea('descricao', 3, 40, '', true, 'text', $db_opcao); ?>
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
        <label for="sigla"><?php echo $Lsigla; ?></label>
        <?php $db_funcoes->db_input('sigla', 50, '', true, 'text', $db_opcao); ?>
      </div>
      <div class="form-group">
        <label for="modulo"><strong>Módulo:</strong></label>
        <select name="modulo" id="modulo" class="form-control" size="1">
        <?php

            $result = $db_stdlib->db_query("SELECT codmod, nomemod FROM db_sysmodulo WHERE ativo is true ORDER BY nomemod");

            foreach ( $result as $linha) {
              echo "<option value=\"".$linha->codmod."\" ".( $linha->codmod == $GLOBALS["codmod"] ? "selected" : "" ).">".$linha->nomemod."</option>\n";
            }
        ?>
        </select>

        <label for="tipotabela" title="<?php echo $Ttipotabela; ?>"><?php echo $Ltipotabela; ?></label>
        <select name="tipotabela" id="tipotabela" class="form-control" size="1" >
          <option value="0" <?=(@$tipotabela=="0"?"selected":"")?>>Manutenção</option>
          <option value="1" <?=(@$tipotabela=="1"?"selected":"")?>>Parâmetro</option>
          <option value="2" <?=(@$tipotabela=="2"?"selected":"")?>>Dependência</option>
        </select>


        <label for="select"><strong>Tabela Pai:</strong></label>
        <select name="tabelapai" size="1" id="select" class="form-control">
          <option value="0">Nenhuma...</option>
            <?php
              $result = $db_stdlib->db_query("select codarq,nomearq 
                                 from db_sysarquivo  
                       order by nomearq");

              foreach ( $result as $linha) {
                echo "<option value=\"".$linha->codarq."\" ".( $linha->codarq == $GLOBALS["codarqpai"] ? "selected" : "" ).">".$linha->nomearq."</option>\n";
              }
            ?>
        </select>
      </div>
      <div class="form-group">
        <h5>Gerador Programa:</h5>
        <label for="naolibclass"><?php echo $Lnaolibclass; ?>
          <input name="naolibclass" type="checkbox" class="form-control" value="" <?=(@$naolibclass?'checked':'')?>>
        </label>

        <label for="naolibfunc"><?php echo $Lnaolibfunc; ?>
          <input name="naolibfunc" type="checkbox" class="form-control" value="" <?=(@$naolibfunc?'checked':'')?>>
        </label>

        <label for="naolibform"><?php echo $Lnaolibform; ?>
          <input name="naolibform" type="checkbox" class="form-control" value="" <?=(@$naolibform?'checked':'')?>>
        </label>

        <label for="naolibprog"><?php echo $Lnaolibprog; ?>
          <input name="naolibprog" type="checkbox" class="form-control" value="" <?=(@$naolibprog?'checked':'')?>>
        </label>
      </div>
  </div>

  <div class="form-actions">
    <input class="btn btn-dark" onClick="Botao = 'incluir'" accesskey="i" type="submit" name="incluir" id="incluir2" value="Incluir" <?php echo isset($retorno)?"disabled":"" ?>>

    <input class="btn btn-dark" name="alterar" accesskey="a" type="submit" id="alterar2" name="alterar" value="Alterar" <?php echo !isset($retorno)?"disabled":"" ?>>

    <input class="btn btn-dark" name="excluir" accesskey="e" type="submit" id="excluir2" name="excluir" value="Excluir" onClick="return confirm('Quer realmente excluir este registro?')" <?php echo !isset($retorno)?"disabled":"" ?>> 
    <input class="btn btn-dark" type="button" data-toggle="modal" data-target="#xlarge" onclick="js_pesquisa();" value="Procurar">
  </div>
</form>
<script language="JavaScript" type="text/javascript" src="<?php echo $Services_Funcoes->url_acesso(); ?>scripts/Extensions_Funcoes.js?n=<?php echo rand(0,1000); ?>"></script>
<script type="text/javascript">
document.form1.nomearq.focus();

js_trocacordeselect();

function js_pesquisa() {
  js_OpenJanelaIframe_Novo('#modal1_conteudo','<?php echo $Services_Funcoes->url_acesso(); ?>files/pages/func_db_sysarquivo.php?funcao_js=parent.js_retornopesquisa|0', '100%', '580px');
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