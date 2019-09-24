<script>
  function js_Relatorio(cod){
    get = 'xarquivo='+cod,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0';
    
    jan = window.open('<?php echo $Services_Funcoes->url_acesso(); ?>files/pages/sys3_modulos002.php?'+get);
    jan.moveTo(0,0);
  }
</script>

<div class="btn-group mr-1 mb-1" role="group" aria-label="Basic example">
	<button type="button" class="btn btn-light" onClick="history.back()">Voltar</button>
	<?php
	$nomemod 	=	$db_stdlib->db_query("select nomemod from db_sysmodulo where codmod = $codmod")->fetch()->nomemod;
	?>
	<button type="button" class="btn btn-dark">Módulo: <?=$nomemod?></button>
</div>

<?php
$sql_mod = "select db_sysmodulo.nomemod,
		   db_sysarquivo.codarq,
		   db_sysarquivo.nomearq,
		   db_sysarquivo.descricao,
		   db_sysarquivo.sigla,
		   to_char(db_sysarquivo.dataincl,'DD-MM-YYYY') as dataincl,
		   db_sysarquivo.tipotabela,
		   db_sysarquivo2.nomearq as arqpai,
		   db_sysarquivo.rotulo
	       from db_sysarquivo
                   	inner join db_sysarqmod
                   			on db_sysarqmod.codarq = db_sysarquivo.codarq
                   inner join db_sysmodulo
                   		on db_sysmodulo.codmod = db_sysarqmod.codmod
	       left outer join db_sysarqarq on db_sysarquivo.codarq = db_sysarqarq.codarq 
	       left outer join db_sysarquivo db_sysarquivo2 on db_sysarqarq.codarqpai = db_sysarquivo2.codarq
                   where db_sysarqmod.codmod = $codmod
                   order by nomearq";

$result 	=	$db_stdlib->db_query($sql_mod);
?>
<table class="table table-responsive estrutura" border="1" cellspacing="0" cellpadding="0">
	<tr bgcolor="#8AF96A">
	  <th><u>Nome</u></th>
	      <th><u></u></th>
	  <th><u>Label</u></th>
	  <th><u>Descricao</u></th>
	  <th><u>Sigla</u></th>
	  <th><u>Tipo</u></th>
	  <th><u>Tabela Principal</u></th>
	  <th nowrap><u>Data de Inclusão</u></th>
	</tr>
	<?php
	$cor1 = "#CAF59A";
	$cor2 = "#B0FDD2";
	$cor = "";
	$numrows = $result->rowCount();
	for($i = 0;$i < $numrows;$i++) {
	  $db_stdlib->db_fieldsmemory($result, $i);

	  echo "<tr bgcolor=\"".($cor = $cor==$cor1?$cor2:$cor1)."\" style=\"cursor:pointer;\" onClick=\"location.href='".$Services_Funcoes->url_acesso_in()."sys3_campos001/".base64_encode("tabela=$codarq")."'\">\n";
	  echo "<td style=\"cursor:pointer;\" onClick=\"location.href='".$Services_Funcoes->url_acesso_in()."sys3_tabelas001/".base64_encode("codmod=$codarq")."'\" title='".$nomearq."'>".substr($nomearq,0,20)."&nbsp;</td>\n";
	  echo "<td><input name=\"relatorio\" type=\"button\" id=\"exibir_relatorio\" value=\"P\" onClick=\"js_Relatorio('$codarq')\">&nbsp;</td>\n";
	  echo "<td style=\"cursor:pointer;\" title='".$rotulo."'>".substr($rotulo,0,20)."&nbsp;</td>\n";
	  echo "<td style=\"cursor:pointer;\" onClick=\"location.href='sys3_tabelas001.php?".base64_encode("codmod=$codarq")."'\" title='".$descricao."'>".substr($descricao,0,60)."&nbsp;</td>\n";
	  echo "<td>".$sigla."&nbsp;</td>\n";
	  echo "<td>".($tipotabela=='0'?'Manutenção':($tipotabela=='1'?'Parâmetro':'Dependente'))."&nbsp;</td>\n";
	  echo "<td>".$arqpai."&nbsp;</td>\n";
	  echo "<td>".$dataincl."&nbsp;</td>\n";
	  echo "</tr>\n";
	}
	?>
</table>