<section id="basic-form-layouts">
  <form class="form" method="post" name="form1" id="form1" action="<?php echo $Services_Funcoes->url_acesso_in().$pagina; ?>">
    <input type="hidden" name="codarq" value="<?=@$codarq?>">
    <div class="row match-height">
      <div class="col-md-5">
        <div class="card">
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
              <select name="modulo" id="modulo" class="form-control">
              <?php

                  $result = $db_stdlib->db_query("SELECT codmod, nomemod FROM db_sysmodulo WHERE ativo is true ORDER BY nomemod");

                  foreach ( $result as $linha) {
                    echo "<option value=\"".$linha->codmod."\" ".( $linha->codmod == $GLOBALS["codmod"] ? "selected" : "" ).">".$linha->nomemod."</option>\n";
                  }
              ?>
              </select>

              <label for="tipotabela" title="<?php echo $Ttipotabela; ?>"><?php echo $Ltipotabela; ?></label>
              <select name="tipotabela" id="tipotabela" class="form-control">
                <option value="0" <?=(@$tipotabela=="0"?"selected":"")?>>Manutenção</option>
                <option value="1" <?=(@$tipotabela=="1"?"selected":"")?>>Parâmetro</option>
                <option value="2" <?=(@$tipotabela=="2"?"selected":"")?>>Dependência</option>
              </select>
              
              <label for="select"><strong>Tabela Pai:</strong></label>
              <select name="tabelapai" id="select" class="form-control">
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
              <div class="form-check">
                <h5>Gerador Programa:</h5>
                <input name="naolibclass" id="naolibclass" type="checkbox" class="form-check-input" value="" <?=(@$naolibclass?'checked':'')?>>
                <label class="form-check-label" for="naolibclass"><?php echo $Lnaolibclass; ?></label>
              </div>
              <div class="form-check">
                <input name="naolibfunc" id="naolibfunc" type="checkbox" class="form-check-input" value="" <?=(@$naolibfunc?'checked':'')?>>
                <label class="form-check-label" for="naolibfunc"><?php echo $Lnaolibfunc; ?></label>
              </div>
              <div class="form-check">
                <input name="naolibform" id="naolibform" type="checkbox" class="form-check-input" value="" <?=(@$naolibform?'checked':'')?>>
                <label class="form-check-label" for="naolibform"><?php echo $Lnaolibform; ?></label>
              </div>
              <div class="form-check">
                <input name="naolibprog" id="naolibprog" type="checkbox" class="form-check-input" value="" <?=(@$naolibprog?'checked':'')?>>
                <label class="form-check-label" for="naolibprog"><?php echo $Lnaolibprog; ?></label>
              </div>
            </div>
        </div>

        <div class="form-actions">
          <input class="btn btn-dark" onClick="Botao = 'incluir'" accesskey="i" type="submit" name="incluir" id="incluir2" value="Incluir" <?php echo isset($retorno)?"disabled":"" ?>>

          <input class="btn btn-dark" name="alterar" accesskey="a" type="submit" id="alterar2" name="alterar" value="Alterar" <?php echo !isset($retorno)?"disabled":"" ?>>

          <input class="btn btn-dark" name="excluir" accesskey="e" type="submit" id="excluir2" name="excluir" value="Excluir" onClick="return confirm('Quer realmente excluir este registro?')" <?php echo !isset($retorno)?"disabled":"" ?>> 
          <input class="btn btn-dark" type="button" data-toggle="modal" data-target="#xlarge" onclick="js_pesquisa();" value="Procurar">
        </div>
        </div>
      </div>
    </div>
    
  </form>
</section>
<script language="JavaScript" type="text/javascript" src="<?php echo $Services_Funcoes->url_acesso(); ?>scripts/Extensions_Funcoes.js?n=<?php echo rand(0,1000); ?>"></script>
<script type="text/javascript">
document.form1.nomearq.focus();

js_trocacordeselect();

function js_pesquisa() {
  js_OpenJanelaIframe_Novo('#modal1_conteudo','<?php echo $Services_Funcoes->url_acesso(); ?>files/dbforms/func_db_sysarquivo001.php?funcao_js=parent.js_retornopesquisa|0', '100%', '580px');
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