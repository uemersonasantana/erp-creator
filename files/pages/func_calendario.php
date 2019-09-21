<?php

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$db_stdlib            =   new libs\db_stdlib;
$Services_Calendario  =   new libs\Services_Calendario;

//  Pega um vetor e cria variáveis globais pelo índice do vetor.
$db_stdlib->db_postmemory($_REQUEST);

if ( !isset($mes_solicitado) ) {
  $mes_solicitado = date("n",$db_stdlib->db_getsession("DB_datausu"));
}
if ( !isset($ano_solicitado) ) {
  $ano_solicitado = date("Y",$db_stdlib->db_getsession("DB_datausu"));
}
if( isset($shutdown_function) ) {
  $Services_Calendario->shutdown_function = $shutdown_function;
}
$Services_Calendario->nome_objeto_data = $nome_objeto_data;

$Services_Calendario->cria(date("d",$db_stdlib->db_getsession("DB_datausu")),date("$mes_solicitado"),date("$ano_solicitado"),1);
?>
<script type="text/javascript">
function janela(d,m,a) {
  <?php
  echo "parent.document.getElementById('".$nome_objeto_data."_dia').value = (d<10?'0'+d:d);\n";
  echo "parent.document.getElementById('".$nome_objeto_data."_mes').value = (m<10?'0'+m:m);\n";
  echo "parent.document.getElementById('".$nome_objeto_data."_ano').value = a;\n";
  echo "parent.js_comparaDatas".$nome_objeto_data."((d<10?'0'+d:d),(m<10?'0'+m:m),a);\n";
  echo "parent.document.getElementById('div_calendario').style.display='none';\n";

  if ( isset($shutdown_function) and ($shutdown_function!='none' ) ) {
      echo $shutdown_function."\n";
  }

  ?>
}
function janela_zera(){
  <?php
  echo "parent.document.getElementById('".$nome_objeto_data."').value     = '';\n";
  echo "parent.document.getElementById('".$nome_objeto_data."_dia').value = '';\n";
  echo "parent.document.getElementById('".$nome_objeto_data."_mes').value = '';\n";
  echo "parent.document.getElementById('".$nome_objeto_data."_ano').value = '';\n";
  echo "parent.document.getElementById('div_calendario').style.display='none';\n";

  if ( isset($shutdown_function) and ($shutdown_function!='none') ) {
      echo $shutdown_function."\n";
  }

  ?>
}

</script>