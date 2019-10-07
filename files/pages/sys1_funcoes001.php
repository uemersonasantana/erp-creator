<?php

$db_funcoes       = new dbforms\db_funcoes;
$cl_sysfuncoes = new classes\cl_sysfuncoes;

$erro     = false;
$db_opcao = 1;

if ( isset($retorno) ) {
  $sql    = "SELECT * FROM db_sysfuncoes WHERE codfuncao = $retorno";
  $result = $db_stdlib->db_query($sql);
  $db_stdlib->db_fieldsmemory($result,0);
}

//////////INCLUIR/////////////
if( isset($_REQUEST["incluir"]) ) {
  $db_opcao = 1;
  $db_funcoes->db_inicio_transacao();
  $codfuncao = $codfuncao==""?"1":$codfuncao;
  $cl_sysfuncoes->incluir();
  $db_funcoes->db_fim_transacao($erro);

////////////////ALTERAR////////////////  
} else if( isset($_REQUEST["alterar"]) ) {
  $db_opcao = 2;
  $db_funcoes->db_inicio_transacao();
  $cl_sysfuncoes->alterar();
  $db_funcoes->db_fim_transacao($erro);

////////////////EXCLUIR//////////////
} else if( isset($_REQUEST["excluir"]) ) {
  $db_opcao = 3;
  $db_funcoes->db_inicio_transacao();
  $cl_sysfuncoes->excluir($codfuncao);
  // Remove a função caso ela já exista no sistema.
  if ( $db_stdlib->db_query("SELECT '$nomefuncao'::regproc;")->rowCount() > 0 ) {
    $db_stdlib->db_exec("DROP FUNCTION ".$nomefuncao);
  }
  $db_funcoes->db_fim_transacao($erro);
}

//  Exibi mensagem de resposta do formulário submetido. 
if( isset($_REQUEST["incluir"])
      or isset($_REQUEST["alterar"])
      or isset($_REQUEST["excluir"])
  ) { 
  if ( !$cl_sysfuncoes->erro_status ) {
    $erro    = true;
    $db_stdlib->db_msgbox($cl_sysfuncoes->erro_msg);
  } else {
    //  Redireciona para processar a função no banco de dados.
    if ( !isset($_REQUEST["excluir"]) ) {
      $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().'sys1_funcoes002/'.base64_encode("gerar=$nomefuncao"));
    }
    //  Redireciona de volta caso a ação tenha sido para excluir a função.
    $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().$pagina,$cl_sysfuncoes->erro_msg);
  }
}

$cl_modulo = new std\rotulo("db_sysfuncoes");
$cl_modulo->label(); 
?>
<div class="card-content collapse show">

<?php if ( $erro == true ) { ?>
<div class="alert alert-danger mb-2" role="alert"><strong>Atenção!</strong> <?php echo $cl_sysfuncoes->erro_msg; ?></div>
<?php } 

include 'forms/frm_db_sysfuncoes001.php';

?>
</div>