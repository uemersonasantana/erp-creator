<?php
if( isset($tabelacod)) {
  $db_stdlib->db_putsession("tabelacod",$tabelacod);
  $tabela = $tabelacod;
} else if( isset($_SESSION["tabelacod"]) ) {
  $tabela = $db_stdlib->db_getsession("tabelacod");
}

?>

<div class="btn-group mr-1 mb-1" role="group" aria-label="Basic example">
	<button type="button" class="btn btn-light" onClick="<?php echo ( isset($manutabela) ? "location.href='".$Services_Funcoes->url_acesso_in()."sys1_tabelas001/".base64_encode("retorno=".$tabela)."'" : "history.back()" ); ?>">Voltar</button>
	
	<?php
	$codseq = 0;
	$result = $db_stdlib->db_query("select c.codcam,c.nomecam,c.conteudo,c.descricao,c.rotulo,c.tamanho,c.nulo,c.valorinicial,ar.codsequencia
                       from db_syscampo c
                       inner join db_sysarqcamp ar
                       on ar.codcam = c.codcam
                       where ar.codarq = $tabela
					   order by ar.seqarq");
    $nometab = $db_stdlib->db_query("select nomearq from db_sysarquivo where codarq = $tabela")->fetch()->nomearq;
    ?>
	<button type="button" class="btn btn-dark">Tabela: <?=$tabela." - ".$nometab?></button>
</div>

<table class="table table-responsive estrutura" border="1" cellspacing="0" cellpadding="0">
	<tr bgcolor="#FF6464">
          <th><u>Item</u></th>
          <th><u>Código</u></th>
          <th><u>Nome</u></th>
	  <th><u>Tipo</u></th>
	  <th><u>Label</u></th>
          <th><u>Tamanho</u></th>
          <th><u>Nulo</u></th>
          <th><u>Valor Incial</u></th>
          <th><u>Seq</u></th>
          <th><u>Descricao</u></th>
	</tr>
    <? 
	$cor1 = "#FEA27A";
	$cor2 = "#FFDBBF";
	$cor = "";
	$numrows = $result->rowCount();
	for($i = 0;$i < $numrows;$i++) {
	  $db_stdlib->db_fieldsmemory($result,$i);

      echo "<tr bgcolor=\"".($cor = $cor==$cor1?$cor2:$cor1)."\">\n";
      echo "<td>".($i+1)."&nbsp;</td>\n";
      echo "<td>".$codcam."&nbsp;</td>\n";
      echo "<td>".$nomecam."&nbsp;</td>\n";
      echo "<td>".$conteudo."&nbsp;</td>\n";
      echo "<td>".$rotulo."&nbsp;</td>\n";
      echo "<td>".$tamanho."&nbsp;</td>\n";
      echo "<td>".($nulo=='t'?'Sim':"&nbsp;")."</td>\n";				
      echo "<td>".$valorinicial."&nbsp;</td>\n";				
      echo "<td>".$codsequencia."&nbsp;</td>\n";				
      if($codsequencia!=0){
	 	$codseq = $codsequencia;
      }
      echo "<td>".$descricao."&nbsp;</td>\n";				
      echo "</tr>\n";
	}
     ?>
	</table>
	<br>
    <?php
	//Chave primaria
	$result = $db_stdlib->db_query("select c.nomecam
                       from db_syscampo c
                       inner join db_sysprikey p
                       on p.codcam = c.codcam
                       where p.codarq = $tabela
                       order by p.sequen"); 

    if( isset($tabelacod) )
	  echo "<a href=sys4_chaveprim001.php?tabela=$tabela alt=\"Adiciona chave primaria\">Chave Primária:&nbsp;</a>\n";
    else
	  echo "<strong>Chave Primária:</strong>\n";
	  
	$numrows = $result->rowCount();
	if( $numrows == 0 )
	  echo "Sem chave primaria\n";
	else
	  for($i = 0;$i < $numrows;$i++) {
        echo $result->fetch()->nomecam;
      }

	 echo "<hr align='left' style='width:750px'>";
	// Chave estrangeira

	$result = $db_stdlib->db_query("select referen 
                       from db_sysforkey 
                       where codarq = $tabela
                       group by(referen)");
	$numrows = $result->rowCount();
	echo "<table class=\"table table-responsive estrutura\" border=\"0\">\n";
	if( $numrows == 0 )
	  if( isset($tabelacod) )
	    echo "<tr><td><a href=\"sys4_chaveestrangeira001.php?".base64_encode("tabela=$tabela")."\">Chave Estrangeira: </a></td><td>Sem Chave Estrangeira</td></tr>\n";
	  else
	    echo "<tr><td><strong>Chave Estrangeira:</strong>&nbsp;</td><td>Sem Chave Estrangeira</td></tr>\n";
	else {
	  if ( isset($tabelacod) )
        echo "<tr><td><a href=\"sys4_chaveestrangeira001.php?".base64_encode("tabela=$tabela")."\">Chave Estrangeira: </a></td><td></td></tr>\n";
      else
	    echo "<tr><td><strong>Chave Estrangeira:</strong></td><td>&nbsp;</td></tr>\n";
    for( $j = 0;$j < $numrows;$j++ ) {
	  $fork = $db_stdlib->db_query("select c.nomecam,a.nomearq
                         from db_sysarquivo a,db_syscampo c,db_sysforkey f
                         where a.codarq = f.referen
                         and c.codcam = f.codcam
                         and f.codarq = $tabela
                         and f.referen = ".$result->fetch()->referen." 
                         order by f.sequen");
      $numfork 		=	$fork->rowCount();
      $result_tmp	=	$fork->fetch();

      echo "<tr><td></td><td>\n";
      
      for( $i = 0;$i < $numfork;$i++ ) {
        echo $result_tmp->nomecam." ";
      }
      echo "<font color=\"#cc7272\">Referente a:&nbsp;&nbsp;</font> ";
	  if( isset($tabelacod) ) 
        echo "<a href=\"".$Services_Funcoes->url_acesso_in()."sys4_chaveestrangeira001/".base64_encode("tabela=$tabela&ref=".$result->fetch()->referen)."\">".$fork->fetch()->nomearq."</a>\n";
      else
	    echo $result_tmp->nomearq;

      echo "</td></tr>\n";
    }

	}
	echo "</table>\n";

	// Indices
	 echo "<hr align='left' style='width:750px'>";

    $result = $db_stdlib->db_query("select codind,nomeind,campounico
                       from db_sysindices
                       where codarq = $tabela");
	echo "<table class=\"table table-responsive estrutura\">\n";
	$numrows = $result->rowCount();
	if( $numrows == 0 ) {
	  if( isset($tabelacod) )
        echo "<tr><td><a href=\"".$Services_Funcoes->url_acesso_in()."sys4_indices001/".base64_encode("tabela=$tabela")."\">Indices:</a></td><td>Sem Indice</td></tr>\n";
   	  else
	    echo "<tr><td><strong>Indices:</strong></td><td>Sem Indice</td></tr>\n";
	} else {
	  

	  if ( isset($tabelacod) )
        echo "<tr><td><a href=\"".$Services_Funcoes->url_acesso_in()."sys4_indices001/".base64_encode("tabela=$tabela")."\">Indices:</a></td><td><a href=\"".$Services_Funcoes->url_acesso_in()."sys4_indices001/".base64_encode("tabela=$tabela&ind=".$result->fetch()->codind)."\"></a></td></tr>\n";
      else
	    echo "<tr><td><strong>Indices:</strong></td><td></td></tr>\n";

	  for( $i = 0;$i < $numrows;$i++ ){
	  	$result_tmp	=	$result->fetch();
        
        $result_ind = $db_stdlib->db_query("select nomecam
                       from db_sysindices i
                            inner join db_syscadind c on c.codind = i.codind
                            inner join db_syscampo a on a.codcam = c.codcam 
                       where codarq = $tabela and i.codind = ".$result_tmp->codind." order by c.sequen");
         $numro 		= 	$result_ind->rowCount();         
         $qcamp = "( ";
         $separador = "";
         for($ii = 0;$ii < $numro;$ii++){
         	$result_tmp2	=	$result_ind->fetch();

         	$qcamp .= $separador.$result_tmp2->nomecam;
            $separador = ",";
	     }
	     $qcamp .= ")";
	    if( isset($tabelacod) )
          echo "<tr><td></td><td><a href=\"".$Services_Funcoes->url_acesso_in()."sys4_indices001/".base64_encode("tabela=$tabela&ind=".$result_tmp->codind)."\">".$result_tmp->nomeind.($result_tmp->campounico == "1" ? "(unique)" : "")."</a></td></tr>\n";
		else
		  echo "<tr><td></td><td>".$result_tmp->nomeind.($result_tmp->campounico == "1" ? "(unique)" : "")." <strong>$qcamp</strong> </td></tr>\n";
      }
	}
	echo "</table>\n";

	// sequencias
       if($codseq!=0){
         $result = $db_stdlib->db_query("select codsequencia,
                              nomesequencia,
			      incrseq,
			      minvalueseq,
			      maxvalueseq,
			      startseq,
			      cacheseq
                       from db_syssequencia
                       where codsequencia = $codseq");
	 echo "<hr align='left' style='width:750px'>";
	 echo "<table class=\"table table-responsive estrutura\">\n";
	 $numrows 		= 	$result->rowCount();
	 $result_tmp 	=	$result->fetch();

	 if ( $numrows == 0 ) {
	   echo "<tr><td><strong>Sequencia:</strong></td><td>Não Encontrada.</td></tr>\n";
	 } else {
	   echo "<tr><td><strong>Sequencia:</strong></td><td>".$result_tmp->codsequencia."</td></tr>\n";
	   echo "<tr><td><strong>Nome:</strong></td><td>".$result_tmp->nomesequencia."</td></tr>\n";
	   echo "<tr><td><strong>Incremento:</strong></td><td>".$result_tmp->incrseq."</td></tr>\n";
	   echo "<tr><td><strong>Valor Mínimo:</strong></td><td>".$result_tmp->minvalueseq."</td></tr>\n";
	   echo "<tr><td><strong>Valor Máximo:</strong></td><td>".$result_tmp->maxvalueseq."</td></tr>\n";
	   echo "<tr><td><strong>Inicio Sequencia:</strong></td><td>".$result_tmp->startseq."</td></tr>\n";
	   echo "<tr><td><strong>Cache Sequencia:</strong></td><td>".$result_tmp->cacheseq."</td></tr>\n";
	 }
 	 echo "</table>\n";
       }else{
         echo "<table class=\"table table-responsive estrutura\">
	       <tr>
	       <td> Sem Sequencia </td>
	       </tr>
	       </table>";
       }
	?>