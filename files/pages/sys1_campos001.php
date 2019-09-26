<?php

$db_funcoes         =  new dbforms\db_funcoes;
$db_db_syscampo     =  new classes\db_db_syscampo;
$db_db_sycampodep   =  new classes\db_db_syscampodep;
$db_db_syscampodef  =  new classes\db_db_syscampodef;

$erro     = false;
$db_opcao = 1;

if( isset($campodefault) ){
  $retorno = $campodefault;
}

if ( isset($retorno) ) {
  $sql = "select m.codmod,a.codarq as tabela,c.codcam,c.nomecam,c.conteudo,c.descricao,c.rotulo,c.valorinicial,c.tamanho,c.nulo,c.maiusculo,c.autocompl,c.aceitatipo,c.tipoobj,c.rotulorel
          from db_syscampo c
      left outer join db_sysarqcamp ac
      on ac.codcam = c.codcam
      left outer join db_sysarquivo a
      on a.codarq = ac.codarq
      left outer join db_sysarqmod m
      on m.codarq = a.codarq
      where c.codcam = $retorno";
      
  $result = $db_stdlib->db_query($sql);

  $db_stdlib->db_fieldsmemory($result,0);

  if( isset($campodefault) ) {
    $campdes    = $nomecam;
    $codcampai  = $retorno;
    
    unset($retorno,$nomecam);
  }
}

//////////INCLUIR/////////////
if(isset($_REQUEST["incluir"])) {
  $db_opcao = 1;

  $db_funcoes->db_inicio_transacao();
  $db_db_syscampo->incluir();
  if ( $codcampai > 0 ) {
    $db_db_sycampodep->incluir($db_db_syscampo->codcam,$codcampai);
  }
  if ( isset($itensdef) ) {
    $numArray = sizeof($itensdef);
    for( $i = 0;$i < $numArray;$i++ ) {
      $aux = explode("#&",$itensdef[$i]);

      $db_db_syscampodef->incluir($codcam, $aux[0], ( !empty($aux[1]) ? $aux[1] : ' ' ) );
    }
  }
  $db_funcoes->db_fim_transacao($erro);

////////////////ALTERAR////////////////  
} else if(isset($_REQUEST["alterar"])) {
  $db_opcao = 2;

  $db_funcoes->db_inicio_transacao();
  $db_db_syscampo->alterar();

  $db_db_sycampodep->excluir($codcam);
  if ( $codcampai > 0 ) {
    $db_db_sycampodep->incluir($codcam,$codcampai);
  }
  $db_db_syscampodef->excluir($codcam);

  if ( isset($itensdef) ) {
    $numArray = sizeof($itensdef);
    for( $i = 0;$i < $numArray;$i++ ) {
      $aux = explode("#&",$itensdef[$i]);

      $db_db_syscampodef->incluir($codcam, $aux[0], ( !empty($aux[1]) ? $aux[1] : ' ' ) );
    }
  } 
  $db_funcoes->db_fim_transacao($erro);

////////////////EXCLUIR//////////////
} else if(isset($_REQUEST["excluir"])) {
  $db_opcao = 3;

  $db_funcoes->db_inicio_transacao();
  $db_db_sycampodep->excluir($codcam);
  $db_db_syscampodef->excluir($codcam);
  $db_db_syscampo->excluir($codcam);
  $db_funcoes->db_fim_transacao($erro);
}

//  Exibi mensagem de resposta do formulário submetido. 
if( isset($_REQUEST["incluir"])
      or isset($_REQUEST["alterar"])
      or isset($_REQUEST["excluir"])
  ) { 
  if ( !$db_db_syscampo->erro_status ) {
    $erro    = true;
    $db_stdlib->db_msgbox($db_db_syscampo->erro_msg);
  } else {
    $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().$pagina,$db_db_syscampo->erro_msg);
  }
}

$cl_rotulo = new \std\rotulo("db_syscampo");
$cl_rotulo->label(); 
?>
<div class="card-content collapse show">

<?php if ( $erro == true ) { ?>
<div class="alert alert-danger mb-2" role="alert"><strong>Atenção!</strong> <?php echo $db_db_syscampo->erro_msg; ?></div>
<?php } 

include 'forms/frm_db_syscampo.php';

?>

</div>