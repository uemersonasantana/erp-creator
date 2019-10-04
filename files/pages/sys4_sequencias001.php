<?php

$db_funcoes         = new dbforms\db_funcoes;
$db_db_syssequencia = new classes\db_db_syssequencia;
$db_db_sysarqcamp   = new classes\db_db_sysarqcamp;

$erro     = false;
$db_opcao = 1;

if( isset($_REQUEST["Atualizar"]) ) {
  if( !isset($campos) ) {
    $db_opcao = 2;
    $db_funcoes->db_inicio_transacao();
    $db_db_sysarqcamp->alterar();
    $db_syssequencia->excluir();
    $db_funcoes->db_fim_transacao($erro);

    //pg_exec("update db_sysarqcamp set codsequencia = 0 where codsequencia = $codsequencia") or die("Erro(15) atualizando db_sysarqcamp");
    //pg_exec("delete from db_syssequencia where codsequencia = $codsequencia") or die("Erro(16) excluindo em db_syssequencia");
  } else {
    $aux        = explode("#",$campos);
    $codcampo   = $aux[0];
    $nomecampo  = $aux[2];
    if($nomesequencia=="") {
      $nomesequencia = $db_tabela."_".$nomecampo."_seq";
    
      $db_funcoes->db_inicio_transacao();
      if( $codsequencia == "0" ) {
        //sequencia não existe, criar uma
        $db_syssequencia->incluir();   
      } else if($codsequencia != "0") {
        //sequencia existe, dá update
        $db_syssequencia->alterar();
      } else {
        db_stdlib::db_erro("Erro na variável codsequencia");
      }
    }
    $db_db_sysarqcamp->alterar();
    //pg_exec("update db_sysarqcamp set codsequencia = $codsequencia where codarq = $dbh_tabela and codcam = $codcampo") or die("Erro(38) alterando db_sysarqcamp");

    $db_funcoes->db_fim_transacao($erro);
  }
} 

//  Exibi mensagem de resposta do formulário submetido. 
if( isset($_REQUEST["Atualizar"])
  ) { 
  if ( !$db_db_syssequencia->erro_status ) {
    $erro    = true;
    $db_stdlib->db_msgbox($db_db_syssequencia->erro_msg);
  } else {
    //  Redireciona para processar a função no banco de dados.
    if ( !isset($_REQUEST["excluir"]) ) {
      $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().'sys1_funcoes002/'.base64_encode("gerar=$nomefuncao"));
    }
    //  Redireciona de volta caso a ação tenha sido para excluir a função.
    $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().$pagina,$db_db_syssequencia->erro_msg);
  }
}

$cl_modulo = new std\rotulo("db_syssequencia");
$cl_modulo->label(); 
?>
<div class="card-content collapse show">

<?php if ( $erro == true ) { ?>
<div class="alert alert-danger mb-2" role="alert"><strong>Atenção!</strong> <?php echo $db_db_syssequencia->erro_msg; ?></div>
<?php } 

include 'forms/frm_db_syssequencias001.php';

?>
</div>