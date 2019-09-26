<form name="form1" method="post" onSubmit="return js_selecionar()">
<section id="basic-form-layouts">
	<div class="row match-height">
	  <div class="col-md-5">
	    <div class="card">
	      <div class="card-content collapse show">
	        <div class="card-body">
	          	<div class="form-body">
	            	<h4 class="form-section"><i class="ft-flag"></i> Organizar Campos</h4>
	        	
	            	<div class="form-group">
	            		<label for="dbh_modulo"><strong>Módulo:</strong></label>
	            		<select name="dbh_modulo" class="form-control" onChange="this.form.submit();">
                      		<?php
							echo '<option value="0">Nenhum...</option>';
							$result = $db_stdlib->db_query("SELECT codmod,nomemod FROM db_sysmodulo ORDER BY nomemod");
							foreach ( $result as $linha ) {
								echo '<option value="'.$linha->codmod.'" '.( $dbh_modulo == $linha->codmod ?"selected":"").'>'.$linha->nomemod.'</option>';
							}
							?>
                    	</select>
	            	</div>

	            	<div class="form-group">
	            		<label for="dbh_modulo"><strong>Tabela:</strong></label>


	            		<select name="dbh_tabela" class="form-control" onChange="this.form.submit();">
							<?php
							echo '<option value="0">Nenhum...</option>';
							$sql = "SELECT m.codarq,nomearq FROM db_sysarquivo a
							       		INNER JOIN db_sysarqmod m ON a.codarq = m.codarq ";
							
							if( $dbh_modulo > 0 ) {
							   $sql .= " where m.codmod = ".$dbh_modulo;
							}
							$sql .= " order by nomearq";
							$result = $db_stdlib->db_query($sql);

							foreach ( $result as $linha ) {
							  echo '<option value="'.$linha->codarq.'" '.($dbh_tabela == $linha->codarq?"selected":"").'>'.$linha->nomearq.'</option>';
							}
							?>
                    	</select>
	            	</div>

	            	<div class="form-group">
	            		<label for="dbh_modulo"><strong>Campos sem tabela:</strong></label>

	            		<select name="naoorganizados" class="form-control" size="17" ondblclick="js_naoorganizados()">
		            		<?php
					        $result = $db_stdlib->db_query("SELECT db_syscampo.codcam,db_syscampo.nomecam
						                   FROM db_syscampo
								        		LEFT JOIN db_sysarqcamp on db_sysarqcamp.codcam = db_syscampo.codcam
								   			WHERE substr(db_syscampo.nomecam,1,2) != 'DB' and db_sysarqcamp.codcam is null
			                             	ORDER BY db_syscampo.codcam DESC");
			                
			                foreach ( $result as $linha ) {
								echo "<option value=\"".$linha->codcam."\">".$linha->nomecam."</option>\n";
							}
					        ?>
					    </select>
	            	</div>

	            	<div class="form-actions">
	            		<input name="atualizar" type="submit" class="btn btn-dark btn-min-width mr-1 mb-1" value="Atualizar">
	            	</div>
	        	</div>
	    	</div>
		  </div>
		</div>
	  </div>

	  <div class="col-md-5">
	    <div class="card">
	      <div class="card-content collapse show">
	        <div class="card-body">
	        	<h4 class="form-section"><i class="ft-flag"></i></h4>

	        	<div class="form-group">
	        		<label for="dbh_modulo"><strong>Campos já relacionados:</strong></label>
	        		<input type="text" name="procuracampo" class="form-control" id="procuracampo5">
              		<input type="button" name="procurarcampo" class="btn btn-info btn-min-width mr-1 mb-1" onClick="return js_procurar()" id="procurarcampo6" value="Procurar">
	        	</div>

	        	<div class="row">
                    <div class="col-md-5">
			        	<div class="form-group">
			        		<label for="campos"><strong>Campos:</strong></label>

							<select name="campos[]" id="campos" class="form-control" size="17" multiple>
							<?php
							if ( isset($dbh_tabela) ) {
							    $result = $db_stdlib->db_query("SELECT c.codcam,c.nomecam FROM db_syscampo c INNER JOIN db_sysarqcamp ac ON ac.codcam = c.codcam WHERE ac.codarq = $dbh_tabela ORDER BY ac.seqarq");
								foreach ( $result as $linha ) {
							        echo "<option value=\"".$linha->codcam."\">".$linha->nomecam."</option>\n";
							    }
							}
							?>
							</select>
						</div>
					</div>
					<div class="col-md-5">
			        	<div class="form-group" style="margin-top:100px;">
							<img style="cursor:pointer;" onClick="js_sobe();return false;" src="<?php echo $Services_Skins->getSkinLink(); ?>img/Controles/seta_up.png" title='Mover para cima' />
							<br/><br/>
							<img style="cursor:pointer;" onClick="js_desce()" src="<?php echo $Services_Skins->getSkinLink(); ?>img/Controles/seta_down.png" title='Mover para baixo' />
							<br/><br/>
							<img style="cursor:pointer;" onClick="js_excluir()" src="<?php echo $Services_Skins->getSkinLink(); ?>img/Controles/bt_excluir.png" title='Remover da seleção' />
			        	</div>
			        </div>
	        	</div>
	        </div>
	    	</div>
		</div>
	  </div>
	</div>
</section>
</form>
<script>
function js_naoorganizados() {
  var F = document.form1;
  var SI = F.naoorganizados.selectedIndex;

  if(SI != -1) {
    F.elements['campos[]'].options[F.elements['campos[]'].options.length] = new Option(F.naoorganizados.options[SI].text,F.naoorganizados.options[SI].value)
    F.naoorganizados.options[SI] = null;
  //    if(SI <= (F.naoorganizados.length - 1))
  //        F..options[SI].selected = true;
      js_trocacordeselect();
  }
}
function js_sobe() {
  var F = document.getElementById("campos");
  if(F.selectedIndex != -1 && F.selectedIndex > 0) {
    var SI = F.selectedIndex - 1;
    var auxText = F.options[SI].text;
	var auxValue = F.options[SI].value;
	F.options[SI] = new Option(F.options[SI + 1].text,F.options[SI + 1].value);
	F.options[SI + 1] = new Option(auxText,auxValue);
	js_trocacordeselect();
	F.options[SI].selected = true;
  }
}
function js_desce() {
  var F = document.getElementById("campos");
  if(F.selectedIndex != -1 && F.selectedIndex < (F.length - 1)) {
    var SI = F.selectedIndex + 1;
    var auxText = F.options[SI].text;
	var auxValue = F.options[SI].value;
	F.options[SI] = new Option(F.options[SI - 1].text,F.options[SI - 1].value);
	F.options[SI - 1] = new Option(auxText,auxValue);
	js_trocacordeselect();
	F.options[SI].selected = true;
  }
}
function js_excluir() {
  var F = document.getElementById("campos");
  var SI = F.selectedIndex;
  if(F.selectedIndex != -1 && F.length > 0) {
    document.form1.naoorganizados.options[document.form1.naoorganizados.length] = new Option(F.options[SI].text,F.options[SI].value);
    F.options[SI] = null;
	js_trocacordeselect();
    if(SI <= (F.length - 1))
      F.options[SI].selected = true;
  }
}
function js_insSelect(texto,valor) {
  var F = document.getElementById("campos");
  F.options[F.length] = new Option(texto,valor);
}
function js_procurar() {
  if(document.form1.procuracampo.value == "") {
    alert("Informe algum argumento para pesquisa");
  	document.form1.procuracampo.focus();
  	return false;
  }
  js_OpenJanelaIframe('top.corpo','db_iframe_pesquisa','sys1_campos003.php?campo=' + document.form1.procuracampo.value);

  //jan = window.open('sys1_campos003.php?campo=' + document.form1.procuracampo.value,'','width=220,height=310,location=0');
  //jan.moveTo(450,150);
  return true;
}
function js_selecionar() {
  var F = document.getElementById("campos").options;
  if(document.form1.dbh_tabela.value == "0") {
    alert("Escolha uma tabela, digitando o nome ou parte dele, e clique em tabela.");
	return false;
  }
  for(var i = 0;i < F.length;i++) {
    F[i].selected = true;
  }
  return true;
}
</script>