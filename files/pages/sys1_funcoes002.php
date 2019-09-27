<?php

$db_funcoes       = new dbforms\db_funcoes;
$db_db_sysfuncoes = new classes\db_db_sysfuncoes;

$erro     = false;
$db_opcao = 1;

if ( !isset($gerar) ) {
  $result = $db_stdlib->db_query("SELECT corpofuncao FROM db_sysfuncoes WHERE nomefuncao = '$funcao'");
  if( $result->rowCount() == 0 ) {
    $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().'sys1_funcoes001');
  }

  $db_stdlib->db_fieldsmemory($result,0);

  // Remove a função caso ela já exista no sistema.
  if ( $db_stdlib->db_query("SELECT '$funcao'::regproc;")->rowCount() > 0 ) {
    $db_stdlib->db_exec("DROP FUNCTION ".$funcao);
  }

  //  Durante o cadastro, coloquei "Aspas duplas" no lugar de 'aspas simples', porque estava acontecendo 
  $corpofuncao  = str_replace('"',"'", $corpofuncao);

  $result = $db_stdlib->db_query("$corpofuncao");
  if ( $result == false ) {
    $erro     = true;
    $erro_msg = "Erro ao processar à funcao: $funcao";
  }

  if ( !$erro ) {
    $db_stdlib->db_redireciona($Services_Funcoes->url_acesso_in().'sys1_funcoes001','Função processada com sucesso!');  
  }
} 
?>
<div class="card-content collapse show">

<?php if ( $erro == true ) { ?>
<div class="alert alert-danger mb-2" role="alert"><strong>Atenção!</strong> <?php echo $erro_msg; ?></div>
<?php } 

include 'forms/frm_db_sysfuncoes002.php';

?>
</div>