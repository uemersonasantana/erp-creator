<script>
  function js_Relatorio(cod){
    get = 'xmodulo='+cod,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0';
    
    jan = window.open('<?php echo $Services_Funcoes->url_acesso(); ?>files/pages/sys3_modulos002.php?'+get);
    jan.moveTo(0,0);
  }
</script>

<?php
if( !isset($def) ) {
  $result = $db_stdlib->db_query("SELECT codmod,nomemod,descricao,to_char(dataincl,'DD-MM-YYYY') as dataincl 
	FROM db_sysmodulo 
	ORDER BY nomemod");
if(!$result) {
        print "<BR>Nao foi possivel pesquisar no banco de dados.\n";
        exit;
}
?>
<div class="btn-group mr-1 mb-1" role="group" aria-label="Basic example">
	<button type="button" class="btn btn-dark">Módulos</button>
</div>
<table class="table table-responsive estrutura" width="100%" border="1" cellspacing="0" cellpadding="0">
<tr bgcolor="#68C6FD">
  <th><u>Nome</u></th>
  <th><u></u></th>
  <th><u>Descricao</u></th>
  <th><u>Data de Inclusão</u></th>
</tr>
<?php
$cor1 = "#A4CCF9";
$cor2 = "#A4BDF9";
$cor = "";
$numrows = $result->rowCount();
for($i = 0;$i < $numrows;$i++) {
  $db_stdlib->db_fieldsmemory($result,$i);
  echo "<tr bgcolor=\"".($cor = $cor==$cor1?$cor2:$cor1)."\" >\n";
  echo "<td style=\"cursor:pointer;\" onClick=\"location.href='".$Services_Funcoes->url_acesso_in()."sys3_tabelas001/".base64_encode("codmod=$codmod")."'\"   >".$nomemod."&nbsp;</td>\n";
  echo "<td><input name=\"relatorio\" type=\"button\" id=\"exibir_relatorio\" value=\"P\" onClick=\"js_Relatorio('$codmod')\">&nbsp;</td>\n";
  echo "<td style=\"cursor:pointer;\" onClick=\"location.href='".$Services_Funcoes->url_acesso_in()."sys3_tabelas001/".base64_encode("codmod=$codmod")."'\"   >".$descricao."&nbsp;</td>\n";		
  echo "<td>".$dataincl."&nbsp;</td>\n";			  
  echo "</tr>\n";
}
?>
</table>
<?php
} 
?>