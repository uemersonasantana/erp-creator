<?php

$db_funcoes       = new dbforms\db_funcoes;
$db_db_sysmodulo  = new classes\db_db_sysmodulo;

$erro     = false;
$db_opcao = 1;

if ( isset($retorno) ) {
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
  $db_opcao = 1;
  $db_funcoes->db_inicio_transacao();
  $db_db_sysmodulo->incluir();
  $db_funcoes->db_fim_transacao($erro);

////////////////ALTERAR////////////////  
} else if( isset($_REQUEST["alterar"]) ) {
  $db_opcao = 2;
  $db_funcoes->db_inicio_transacao();
  $db_db_sysmodulo->alterar();
  $db_funcoes->db_fim_transacao($erro);

////////////////EXCLUIR//////////////
} else if( isset($_REQUEST["excluir"]) ) {
  $db_opcao = 3;
  $db_funcoes->db_inicio_transacao();
  $db_db_sysmodulo->excluir();
  $db_funcoes->db_fim_transacao($erro);
}

//  Exibi mensagem de resposta do formulário submetido. 
if( isset($_REQUEST["incluir"])
      or isset($_REQUEST["alterar"])
      or isset($_REQUEST["excluir"])
  ) { 
  if ( !$db_db_sysmodulo->erro_status ) {
    $erro    = true;
    $db_stdlib->db_msgbox($db_db_sysmodulo->erro_msg);
  } else {
    $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().$pagina,$db_db_sysmodulo->erro_msg);
  }
}

$cl_modulo = new std\rotulo("db_sysmodulo");
$cl_modulo->label(); 
?>
<div class="card-content collapse show">

<?php if ( $erro == true ) { ?>
<div class="alert alert-danger mb-2" role="alert"><strong>Atenção!</strong> <?php echo $db_db_sysmodulo->erro_msg; ?></div>
<?php } 

include 'forms/frm_db_sysmodulo.php';

?>
</div>