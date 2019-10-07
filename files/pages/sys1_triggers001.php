<?php

$db_funcoes         =  new dbforms\db_funcoes;
$cl_systriggers  =  new classes\cl_systriggers;
$cl_sysarquivo   =  new classes\cl_sysarquivo;
$cl_sysfuncoes   =  new classes\cl_sysfuncoes;

$erro     = false;
$db_opcao = 1;

if ( isset($retorno) ) {
  $sql = "SELECT 
            t.codtrigger
            ,t.nometrigger
            ,t.quandotrigger
            ,t.eventotrigger
            ,f.nomefuncao
            ,f.codfuncao
            ,a.codarq
            ,a.nomearq 
          
          FROM db_systriggers t
            INNER JOIN db_sysfuncoes f ON f.codfuncao = t.codfuncao
            INNER JOIN db_sysarquivo a ON a.codarq = t.codarq
          WHERE codtrigger = $retorno";
  $result = $db_stdlib->db_query($sql);

  $db_stdlib->db_fieldsmemory($result,0);
}

//////////INCLUIR/////////////
if(isset($_REQUEST["incluir"])) {
  $db_opcao = 1;

  $db_funcoes->db_inicio_transacao();
  $cl_systriggers->incluir();
  $db_funcoes->db_fim_transacao($erro);

////////////////ALTERAR////////////////  
} else if(isset($_REQUEST["alterar"])) {
  $db_opcao = 2;

  $db_funcoes->db_inicio_transacao();
  $cl_systriggers->alterar();
  $db_funcoes->db_fim_transacao($erro);

////////////////EXCLUIR//////////////
} else if(isset($_REQUEST["excluir"])) {
  $db_opcao = 3;

  $db_funcoes->db_inicio_transacao();
  $cl_systriggers->excluir($codtrigger);
  $db_funcoes->db_fim_transacao($erro);
}

//  Exibi mensagem de resposta do formulário submetido. 
if( isset($_REQUEST["incluir"])
      or isset($_REQUEST["alterar"])
      or isset($_REQUEST["excluir"])
  ) { 
  if ( !$cl_systriggers->erro_status ) {
    $erro    = true;
    $db_stdlib->db_msgbox($cl_systriggers->erro_msg);
  } else {
    $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().$pagina,$cl_systriggers->erro_msg);
  }
}

$cl_rotulo = new \std\rotulo("db_systriggers");
$cl_rotulo->label();

$cl_sysarquivo->rotulo->label();
$cl_sysfuncoes->rotulo->label();
?>
<div class="card-content collapse show">

<?php if ( $erro == true ) { ?>
<div class="alert alert-danger mb-2" role="alert"><strong>Atenção!</strong> <?php echo $cl_systriggers->erro_msg; ?></div>
<?php } 

include 'forms/frm_db_systriggers001.php';

?>
</div>