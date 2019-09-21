<?php

$db_funcoes       = new dbforms\db_funcoes;
$db_db_sysmodulo  = new classes\db_db_sysmodulo;

if( isset($retorno) ) {
  $sql = "SELECT *
                  ,to_char(dataincl,'DD') as dataincl_dia
                  ,to_char(dataincl,'MM') as dataincl_mes
                  ,to_char(dataincl,'YYYY') as dataincl_ano
              FROM 
                db_sysmodulo
	            WHERE codmod = $retorno";

  $result = $db_stdlib->db_query($sql);

  $db_stdlib->db_fieldsmemory($result,0);
}

//////////INCLUIR/////////////
if( isset($_REQUEST["incluir"]) ) {
  $db_funcoes->db_inicio_transacao();

  $db_db_sysmodulo->nomemod       = $nomemod;
  $db_db_sysmodulo->descricao     = $descricao;
  $db_db_sysmodulo->data          = $dataincl;
  $db_db_sysmodulo->dataincl_dia  = $dataincl_dia;
  $db_db_sysmodulo->dataincl_mes  = $dataincl_mes;
  $db_db_sysmodulo->dataincl_ano  = $dataincl_ano;
  $db_db_sysmodulo->ativo         = $ativo;

  $db_db_sysmodulo->incluir();
  
  if ( $db_db_sysmodulo->erro_status != 0 ) {
    $db_stdlib->db_erro($db_db_sysmodulo->erro_status);
  }   

  $db_funcoes->db_fim_transacao();
////////////////ALTERAR////////////////  
} else if( isset($_REQUEST["alterar"]) ) {

  if(!checkdate($dataincl_mes,$dataincl_dia,$dataincl_ano))
    $db_stdlib->db_erro("Data inválida(update)");
  else
    $data = $dataincl_ano."-".$dataincl_mes."-".$dataincl_dia;  
  
  $db_stdlib->db_query("UPDATE 
                            db_sysmodulo 
                          SET 
                            nomemod   = '$nomemod'
                            ,descricao = '$descricao'
                            ,dataincl  = '$data'
                            ,ativo = '$ativo'
	      	                WHERE 
                            codmod  =  $codmod");
  $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().$pagina);
////////////////EXCLUIR//////////////
} else if( isset($_REQUEST["excluir"]) ) {

  $db_stdlib->db_query("DELETE FROM 
                                db_sysmodulo 
                              WHERE 
                                codmod = ".$_REQUEST["codmod"]);
 $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().$pagina);
}

$cl_modulo = new std\rotulo("db_sysmodulo");
$cl_modulo->label(); 

//echo $GLOBALS['Lnomemod'];


$Lnomemod   = (isset($Lnomemod)     ? $Lnomemod   : null);
$Ldescricao = (isset($Ldescricao)   ? $Ldescricao : null);

$nomemod    = (isset($nomemod)     ? $nomemod    : null);
$descricao  = (isset($descricao)   ? $descricao  : null);

$Tnomemod   = (isset($Tnomemod)    ? $Tnomemod   : null);
$Tdescricao = (isset($Tnomemod)    ? $Tdescricao : null);

$Ldataincl  = (isset($Ldataincl)    ? $Ldataincl : null);


if( isset($_REQUEST["procurar"]) || isset($_REQUEST["priNoMe"]) || isset($_REQUEST["antNoMe"]) || isset($_REQUEST["proxNoMe"]) || isset($_REQUEST["ultNoMe"]) ) {

  $sql = "SELECT 
                codmod    as db_codmod
                ,codmod
                ,nomemod 
                ,descricao 
              FROM 
                db_sysmodulo
              
              WHERE nomemod like '".$_REQUEST["nomemod"]."%'
              
              ORDER BY nomemod";

  //  		db_lov($sql,15,"sys1_modulos001.php");
  
  $GLOBALS['db_corcabec']   = '';
  $GLOBALS['cor1']          = '';
  $GLOBALS['cor2']          = '';
  
  //$db_stdlib->db_lovrot($sql,15,"()","","js_Voltar|codmod","","NoMe");
  //echo '<input class="btn btn-dark" type="button" name="voltar" value="Voltar" onClick="js_Voltar();">';
} else {
?>
<div class="card-content collapse show">
<form class="form" method="post" name="form1" id="form1" action="<?php echo $Services_Funcoes->url_acesso_in().$pagina; ?>" onSubmit="return js_submeter(this)">
  <div class="form-body">
      <h4 class="form-section">
        <i class="ft-flag"></i> Dados do Módulo</h4>
      <div class="form-group">
        <label for="nomemod"><?php echo $Lnomemod; ?></label>
        <input class="form-control" id="nomemod" name="nomemod" title="<?php echo $Tnomemod; ?>" type="text" id="nomemod" value="<?php echo $nomemod; ?>">
      </div>
      <div class="form-group">
        <label for="descricao"><?php echo $Ldescricao; ?></label>
        <textarea class="form-control" name="descricao" cols="50" title="<?php echo $Tdescricao; ?>" rows="7" id="descricao"><?php echo $descricao; ?></textarea>
      </div>
      <div class="form-group">
        <label for="dataincl"><?php echo $Ldataincl; ?></label>
        <?php
        $db_funcoes  = new dbforms\db_funcoes;
        $dataincl_dia = date("d");
        $dataincl_mes = date("m");
        $dataincl_ano = date("Y");
        $db_funcoes->db_inputdata("dataincl",@$dataincl_dia,@$dataincl_mes,@$dataincl_ano,1,'text',2);
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

      <input class="btn btn-dark" type="button"  onClick="location.href='sys3_tabelas001.php?<?php echo base64_encode("codmod=$retorno&manutabela=true") ?>'" value="Ver Tabelas" <?php echo !isset($retorno)?"disabled":"" ?>>
      <input type="hidden" name="codmod" value="<?=@$codmod?>">

  </div>
</form>
</div>
<?php
}
?>
<script language="JavaScript" type="text/javascript" src="<?php echo $Services_Funcoes->url_acesso(); ?>scripts/Extensions_Funcoes.js?n=<?php echo rand(0,1000); ?>"></script>
<script type="text/javascript">
Botao = 'incluir';
function js_submeter(obj) {
  if(Botao != 'procurar') {  
    if(obj.nomemod.value == "") {
      alert("Campo nome do módulo é obrigatório");
    obj.nomemod.focus();
    return false;
    }
  if(obj.descricao.value == "") {
      alert("Campo descrição é obrigatório");
    obj.descricao.focus();
    return false;
    }
  if(obj.dataincl_dia.value == "" || obj.dataincl_mes.value == "" || obj.dataincl_ano.value == "") {
    alert("Campo data vazio ou inválido!");
    obj.dataincl_dia.focus();
    return false;
  }
  }
  return true;
}

function js_iniciar() {
if(document.form1)
  document.form1.nomemod.focus();
}

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

js_iniciar();
</script>