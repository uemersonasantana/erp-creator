<?php

$db_funcoes         = new dbforms\db_funcoes;
$cl_syssequencia = new classes\cl_syssequencia;
$cl_sysarqcamp   = new classes\cl_sysarqcamp;

$erro     = false;
$db_opcao = 1;

if( isset($_REQUEST["atualizar"]) ) { 
  if( !isset($campos) ) {
    $db_opcao = 2;

    $db_funcoes->db_inicio_transacao();
    $cl_syssequencia->excluir($codsequencia);
    
    //  Coloca valor na variável global para passar no filtro de insert da classe 'cl_sysarqcamp'.
    $GLOBALS['codsequencia']  = 0;

    $cl_sysarqcamp->alterar($retorno);
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
        $cl_syssequencia->incluir();   
      } else if($codsequencia != "0") {
        //sequencia existe, dá update
        $cl_syssequencia->alterar();
      } else {
        db_stdlib::db_erro("Erro na variável codsequencia");
      }
    }

    //  Coloca valor na variável global para passar no filtro de insert da classe 'cl_sysarqcamp'.
    if ( $codsequencia == 0 and $cl_syssequencia->codsequencia > 0 ) {
      $GLOBALS['codsequencia']  = $cl_syssequencia->codsequencia;
    }

    $cl_sysarqcamp->alterar($retorno,$codcampo);
    $erro_msg = $cl_sysarqcamp->erro_msg;

    //pg_exec("update db_sysarqcamp set codsequencia = $codsequencia where codarq = $dbh_tabela and codcam = $codcampo") or die("Erro(38) alterando db_sysarqcamp");

    $db_funcoes->db_fim_transacao($erro);
  }
} 

//  Exibi mensagem de resposta do formulário submetido. 
if( isset($_REQUEST["atualizar"])
  ) { 
  if ( !$cl_syssequencia->erro_status and ( $nomesequencia=="" or !isset($campos) ) ) {
    
    $erro    = true;
    $db_stdlib->db_msgbox($cl_syssequencia->erro_msg);
  
  } else if ( !$cl_sysarqcamp->erro_status ) {
    
    $erro    = true;
    $db_stdlib->db_msgbox($cl_sysarqcamp->erro_msg);
  } else {
    //  Redireciona de volta caso a ação tenha sido para excluir a função.
    $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().$pagina,$cl_sysarqcamp->erro_msg);
  }
}

$cl_modulo = new std\rotulo("db_syssequencia");
$cl_modulo->label(); 
?>
<div class="card-content collapse show">

<?php if ( $erro == true ) { ?>
<div class="alert alert-danger mb-2" role="alert"><strong>Atenção!</strong> <?php echo $cl_syssequencia->erro_msg; ?></div>
<?php } 

include 'forms/frm_db_syssequencias001.php';

?>
</div>