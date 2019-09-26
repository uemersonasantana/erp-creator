<?php

$db_funcoes         =  new dbforms\db_funcoes;
$db_sysarqcamp      =  new classes\db_db_sysarqcamp;

$erro     = false;
$db_opcao = 1;

//////////ATUALIZAR/////////////
if (isset($_REQUEST["atualizar"])) {
  $db_opcao = 1;

  $db_funcoes->db_inicio_transacao();
  $db_sysarqcamp->incluir();
  $db_funcoes->db_fim_transacao($erro);
}

//  Exibi mensagem de resposta do formulário submetido. 
if( isset($_REQUEST["atualizar"]) ) { 
  if ( !$db_sysarqcamp->erro_status ) {
    $erro    = true;
    $db_stdlib->db_msgbox($db_sysarqcamp->erro_msg);
  } else {
    $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().$pagina,$db_sysarqcamp->erro_msg);
  }
}

$rotulo = new \std\rotulo("sysarqcamp");
$rotulo->label(); 
?>
<div class="card-content collapse show">

<?php if ( $erro == true ) { ?>
<div class="alert alert-danger mb-2" role="alert"><strong>Atenção!</strong> <?php echo $db_sysarqcamp->erro_msg; ?></div>
<?php } 

include 'forms/frm_db_syscampo002.php';

?>
</div>