<?php

$db_funcoes         = new dbforms\db_funcoes;
$db_db_syssequencia = new classes\db_db_syssequencia;
$db_db_sysarqcamp   = new classes\db_db_sysarqcamp;

$erro     = false;
$db_opcao = 1;

if( isset($_REQUEST["atualizar"]) ) { 
  if( !isset($campos) ) {
    $db_opcao = 2;

    $db_funcoes->db_inicio_transacao();
    $db_db_syssequencia->excluir($codsequencia);
    
    //  Coloca valor na variável global para passar no filtro de insert da classe 'db_db_sysarqcamp'.
    $GLOBALS['codsequencia']  = 0;

    $db_db_sysarqcamp->alterar($retorno);
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
        $db_db_syssequencia->incluir();   
      } else if($codsequencia != "0") {
        //sequencia existe, dá update
        $db_db_syssequencia->alterar();
      } else {
        db_stdlib::db_erro("Erro na variável codsequencia");
      }
    }

    //  Coloca valor na variável global para passar no filtro de insert da classe 'db_db_sysarqcamp'.
    if ( $codsequencia == 0 and $db_db_syssequencia->codsequencia > 0 ) {
      $GLOBALS['codsequencia']  = $db_db_syssequencia->codsequencia;
    }

    $db_db_sysarqcamp->alterar($retorno,$codcampo);
    $erro_msg = $db_db_sysarqcamp->erro_msg;

    //pg_exec("update db_sysarqcamp set codsequencia = $codsequencia where codarq = $dbh_tabela and codcam = $codcampo") or die("Erro(38) alterando db_sysarqcamp");

    $db_funcoes->db_fim_transacao($erro);
  }
} 

//  Exibi mensagem de resposta do formulário submetido. 
if( isset($_REQUEST["atualizar"])
  ) { 
  if ( !$db_db_syssequencia->erro_status and ( $nomesequencia=="" or !isset($campos) ) ) {
    
    $erro    = true;
    $db_stdlib->db_msgbox($db_db_syssequencia->erro_msg);
  
  } else if ( !$db_db_sysarqcamp->erro_status ) {
    
    $erro    = true;
    $db_stdlib->db_msgbox($db_db_sysarqcamp->erro_msg);
  } else {
    //  Redireciona de volta caso a ação tenha sido para excluir a função.
    $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().$pagina,$db_db_sysarqcamp->erro_msg);
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