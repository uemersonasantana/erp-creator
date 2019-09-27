<?php

$db_funcoes         =  new dbforms\db_funcoes;
$db_db_sysarquivo   =  new classes\db_db_sysarquivo;
$db_db_sysarqmod    =  new classes\db_db_sysarqmod;
$db_db_sysarqarq    =  new classes\db_db_sysarqarq;

$erro     = false;
$db_opcao = 1;

if ( isset($retorno) ) {
  $sql = "select m.codmod,a.codarq,a.nomearq,a.descricao,a.sigla,a.rotulo,aq.codarqpai,a.naolibclass,a.naolibfunc,a.naolibprog,a.naolibform,
          to_char(a.dataincl,'DD') as dataincl_dia,to_char(a.dataincl,'MM') as dataincl_mes,to_char(a.dataincl,'YYYY') as dataincl_ano, a.tipotabela
          from db_sysarquivo a
           left outer join db_sysarqarq aq on aq.codarq = a.codarq
      inner join db_sysarqmod m
      on m.codarq = a.codarq
      where a.codarq = $retorno";
  $result = $db_stdlib->db_query($sql);

  $db_stdlib->db_fieldsmemory($result,0);
}

//////////INCLUIR/////////////
if(isset($_REQUEST["incluir"])) {
  $db_opcao = 1;

  $db_funcoes->db_inicio_transacao();
  $db_db_sysarquivo->incluir();
  $db_db_sysarqmod->incluir($modulo,$db_db_sysarquivo->codarq);
  if ( (INT)$tabelapai > 0 ) {
    $db_db_sysarqarq->incluir($tabelapai,$db_db_sysarquivo->codarq);
  }
  $db_funcoes->db_fim_transacao($erro);

////////////////ALTERAR////////////////  
} else if(isset($_REQUEST["alterar"])) {
  $db_opcao = 2;

  $db_funcoes->db_inicio_transacao();
  $db_db_sysarquivo->alterar();
  $db_db_sysarqmod->alterar($modulo,$db_db_sysarquivo->codarq);
  $db_db_sysarqarq->alterar($tabelapai,$db_db_sysarquivo->codarq);
  $db_funcoes->db_fim_transacao($erro);

////////////////EXCLUIR//////////////
} else if(isset($_REQUEST["excluir"])) {
  $db_opcao = 3;
  $db_funcoes->db_inicio_transacao();
  $db_db_sysarqarq->excluir(null,$codarq);
  $db_db_sysarqmod->excluir(null,$codarq);
  $db_db_sysarquivo->excluir();
  $db_funcoes->db_fim_transacao($erro);
}

//  Exibi mensagem de resposta do formulário submetido. 
if( isset($_REQUEST["incluir"])
      or isset($_REQUEST["alterar"])
      or isset($_REQUEST["excluir"])
  ) { 
  if ( !$db_db_sysarquivo->erro_status ) {
    $erro    = true;
    $db_stdlib->db_msgbox($db_db_sysarquivo->erro_msg);
  } else {
    $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().$pagina,$db_db_sysarquivo->erro_msg);
  }
}

$cl_rotulo = new \std\rotulo("db_sysarquivo");
$cl_rotulo->label(); 
?>
<div class="card-content collapse show">

<?php if ( $erro == true ) { ?>
<div class="alert alert-danger mb-2" role="alert"><strong>Atenção!</strong> <?php echo $db_db_sysarquivo->erro_msg; ?></div>
<?php } 

include 'forms/frm_db_sysarquivo001.php';

?>

</div>