<?php

$db_funcoes         =  new dbforms\db_funcoes;
$db_sysarqcamp      =  new classes\cl_sysarqcamp;

$erro     = false;
$db_opcao = 1;

//////////ATUALIZAR/////////////
if (isset($_REQUEST["atualizar"])) {
  $db_opcao = 1;

  $db_funcoes->db_inicio_transacao();

  $tam = sizeof($campos);
  
  $result = $db_stdlib->db_query("SELECT codcam, codsequencia FROM db_sysarqcamp where codsequencia != 0 and codarq = $dbh_tabela");
  
  if( $result->rowCount() > 0 ) {
    for ( $i=0; $i < $result->rowCount(); $i++ ) {
      $db_stdlib->db_fieldsmemory($result,$i);
      $matcam[$i] = $codcam;
      $matseq[$i] = $codsequencia;
    }
  }

  //  Coloca valor na variável global para passar no filtro de insert da classe 'db_sysarqcamp'.
  if ( !isset($codsequencia) ) {
    $GLOBALS['codsequencia']  = 0;
  }

  $db_sysarqcamp->excluir($dbh_tabela);

  for($i = 0;$i < $tam;$i++){
    $codseq = 0;
    if(isset($matcam)){
      for($x=0;$x<sizeof($matcam);$x++){
        if($matcam[$x]==$campos[$i]){
          $codseq = $matseq[$x];
        }
      }
    }

    $db_sysarqcamp->incluir($dbh_tabela, $campos[$i], ($i + 1), $codseq);
  }
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