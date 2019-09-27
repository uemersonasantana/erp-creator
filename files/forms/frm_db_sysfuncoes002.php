<section id="basic-form-layouts">
<form class="form" method="post" name="form1" id="form1" action="<?php echo $Services_Funcoes->url_acesso_in().$pagina; ?>">
  <input type="hidden" name="codfuncao" value="<?=@$codfuncao?>">
  <input type="hidden" name="gerar2" value="<?=@$gerar?>">
  <div class="row match-height">
    <div class="col-md-5">
      <div class="card">
        <div class="form-body">
            <h4 class="form-section"><i class="ft-flag"></i> Processar Função no Banco</h4>
            
            <div class="form-group">
              <label for="funcao" style="font-size:25px;">Função: <strong><i><?=( isset($gerar) ? $gerar : $gerar2 )?></i></strong></label>
              <input name="funcao" type="hidden" id="funcao" value="<?=$gerar?>">
            </div>
        </div>
        <div class="form-actions">
            <input type="submit" name="processar" id="processar" class="btn btn-dark" value="Processar" >

            <input type="button" name="cancelar" id="cancelar" class="btn btn-dark" onclick="document.location.href='<?php echo $Services_Funcoes->url_acesso_in(); ?>sys1_funcoes001'" value="Cancelar">
        </div>
      </div>
    </div>
  </div>
</form>
</section>