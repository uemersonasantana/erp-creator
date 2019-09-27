<section id="basic-form-layouts">
  <form class="form" method="post" name="form1" id="form1" action="<?php echo $Services_Funcoes->url_acesso_in().$pagina; ?>" onSubmit="return js_submeter(this)">
    <input type="hidden" name="codcam" value="<?=@$codcam?>">
    <div class="row match-height">
      <div class="col-md-6">
        <div class="card">
          <div class="card-content collapse show">
            <div class="card-body">
              <div class="form-body">
                <h4 class="form-section"><i class="ft-flag"></i> Dados Campo</h4>
                <div class="form-group">
                  <select name="codcampai" class="form-control" onchange="js_buscadefault(this.value)">
                    <option value='0'>Campo Principal...</option>
                    <?php
                    if ( isset($campodefault) ) {
                        echo "<option selected value='$codcampai'>$campdes</option>";
                    }
                    if ( isset($codcam) ) {
                      $sql = "Select nomecam as nom,codcampai as pai from db_syscampodep inner join db_syscampo on db_syscampo.codcam=codcampai  where db_syscampodep.codcam = $codcam";
                      $result = $db_stdlib->db_query($sql);
                      if ( $result->rowCount() > 0 ) {
                        $db_stdlib->db_fieldsmemory($result,0);
                        echo "<option selected value='$pai'>$nom</option>";
                      }   
                    }  
                    ?>
                  </select>
                </div>

                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                          <label for="nomecam" title="<?php echo $Tnomecam; ?>"><?php echo $Lnomecam; ?></label>
                          <?php $db_funcoes->db_input('nomecam', 70, '', true, 'text', $db_opcao); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                          <label for="conteudo" title="<?php echo $Tconteudo; ?>"><?php echo $Lconteudo; ?></label>
                          <?php
                            if( isset($conteudo) ) {
                              $v_tipo = explode('(',$conteudo);
                              $conteudo = $v_tipo[0];
                              if( isset($v_tipo[1]) ) {
                                $v = explode(")",$v_tipo[1]);
                              } else {
                                $v = "";
                              }
                            }
                            //$tamanho = $v[0];
                          ?>
                          <select name="conteudo" id="conteudo" class="form-control" OnChange="js_valida(this.value)">
                            <option value="0" ></option>
                            <option value="varchar" <?php echo @$conteudo=="varchar"?"selected":"" ?>>Varchar</option>
                            <option value="text" <?php echo @$conteudo=="text"?"selected":"" ?>>Text</option>
                            <option value="oid" <?php echo @$conteudo=="oid"?"selected":"" ?>>Oid</option>
                            <
                            <option value="int4" <?php /* Se o valor da variável conteúdo for string e inteiro, ex: int4, float8; o php não consegue encontrar*/ echo ( substr(@$conteudo, 0,3) =="int" or @$conteudo=="integer")?"selected":"" ?>>Int4</option>
                            <option value="int8" <?php echo @$conteudo=="int8"?"selected":"" ?>>Int8</option>
                            <option value="float4" <?php /* Se o valor da variável conteúdo for string e inteiro, ex: int4, float8; o php não consegue encontrar*/ echo ( substr(@$conteudo, 0,5) == "float" and substr(@$conteudo, 5,1) == 4 ) ?"selected":"" ?>>Float4</option>
                            <option value="float8" <?php /* Se o valor da variável conteúdo for string e inteiro, ex: int4, float8; o php não consegue encontrar*/ echo (substr(@$conteudo, 0,5) == "float" and substr(@$conteudo, 5,1) == 8 ) ?"selected":"" ?>>Float8</option>
                            <option value="bool" <?php echo @$conteudo=="bool"?"selected":"" ?>>Lógico</option>
                            <option value="char" <?php echo @$conteudo=="char"?"selected":"" ?>>Char</option>
                            <option value="date" <?php echo @$conteudo=="date"?"selected":"" ?>>Data</option>
                          </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                          <label for="tamanho" title="<?php echo $Ttamanho; ?>"><?php echo $Ltamanho; ?></label>
                          <?php $db_funcoes->db_input('tamanho', 3, 1, true, 'text', $db_opcao); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                  <label for="rotulo" title="<?php echo $Trotulo; ?>"><?php echo $Lrotulo; ?></label>
                  <?php $db_funcoes->db_input('rotulo', 50, '', true, 'text', $db_opcao); ?>
                </div>
                <div class="form-group">
                  <label for="rotulorel" title="<?php echo $Trotulorel; ?>"><?php echo $Lrotulorel; ?></label>
                  <?php $db_funcoes->db_input('rotulorel', 50, '', true, 'text', $db_opcao); ?>
                </div>
                <div class="form-group">
                  <label for="valorinicial" title="<?php echo $Tvalorinicial; ?>"><?php echo $Lvalorinicial; ?></label>
                  <?php $db_funcoes->db_input('valorinicial', 50, '', true, 'text', $db_opcao); ?>
                </div>

                <div class="form-group">
                  <label for="descricao" title="<?php echo $Tdescricaol; ?>"><?php echo $Ldescricao; ?></label>
                  <?php $db_funcoes->db_textarea('descricao', 3, 50, '', true, 'text', $db_opcao); ?>
                </div>
                <div class="form-group">
                  <div class="form-check">
                    <input name="nulo" id="nulo" class="form-check-input" type="checkbox" value="" <?=(@$nulo?'checked':'')?>>
                    <label class="form-check-label" for="nulo"><strong>Aceita Nulo</strong></label>
                  </div>
                  <div class="form-check">
                    <input name="maiusculo" id="maiusculo" class="form-check-input" type="checkbox" value="" <?=(@$maiusculo?'checked':'')?>>
                    <label class="form-check-label" for="maiusculo"><strong>Maísculo</strong></label>
                  </div>
                  <div class="form-check">
                    <input name="autocompl" id="autocompl" class="form-check-input" type="checkbox" value="" <?=(@$autocompl?'checked':'')?>>
                    <label class="form-check-label" for="autocompl"><strong>Auto Completar</strong></label>
                  </div>
                </div>

                <div class="form-group">
                  <label for="aceitatipo" title="<?php echo $Taceitatipo; ?>"><strong><?php echo $Laceitatipo; ?></strong></label>
                  <select name="aceitatipo" id="aceitatipo" class="form-control" OnChange="js_valida(this.selectedIndex)">
                    <option value='0'>Não Valida Campo</option>
                    <option value="1" <?php echo @$aceitatipo=="1"?"selected":"" ?>>Somente 
                    Números</option>
                    <option value="2" <?php echo @$aceitatipo=="2"?"selected":"" ?>>Somente 
                    Letras</option>
                    <option value="3" <?php echo @$aceitatipo=="3"?"selected":"" ?>>Números 
                    e Letras</option>
                    <option value="4" <?php echo @$aceitatipo=="4"?"selected":"" ?>>Números 
                    Casa Dec.</option>
                    <option value="5" <?php echo @$aceitatipo=="5"?"selected":"" ?>> 
                    Vardadeiro/Falso</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="aceitatipo" title="<?php echo $Ttipoobj; ?>"><strong><?php echo $Ltipoobj; ?></strong></label>
                  <select name="tipoobj" id="tipoobj" class="form-control">
                    <option <?=(@$tipoobj=='text'?"selected":"")?> value='text'>Input 
                    Text</option>
                    <option <?=(@$tipoobj=='checkbox'?"selected":"")?> value='checkbox'>Input 
                    Checkbox</option>
                    <option <?=(@$tipoobj=='radiobutton'?"selected":"")?> value='radiobutton'>Input 
                    Radio Button</option>
                    <option <?=(@$tipoobj=='image'?"selected":"")?> value='image'>Input 
                    Imagem</option>
                    <option <?=(@$tipoobj=='textarea'?"selected":"")?> value='textarea'>TextArea</option>
                    <option <?=(@$tipoobj=='select'?"selected":"")?> value='select'>Select</option>
                    <option <?=(@$tipoobj=='multiple'?"selected":"")?> value='multiple'>Select 
                    Multiplo</option>
                  </select>
                </div>
              </div>

              
            </div><div class="form-actions">
                <input class="btn btn-dark" onClick="Botao = 'incluir'" accesskey="i" type="submit" name="incluir" id="incluir2" value="Incluir" <?php echo isset($retorno)?"disabled":"" ?>>

                <input class="btn btn-dark" name="alterar" accesskey="a" type="submit" id="alterar2" name="alterar" value="Alterar" <?php echo !isset($retorno)?"disabled":"" ?>>

                <input class="btn btn-dark" name="excluir" accesskey="e" type="submit" id="excluir2" name="excluir" value="Excluir" onClick="return confirm('Quer realmente excluir este registro?')" <?php echo !isset($retorno)?"disabled":"" ?>> 
                <input class="btn btn-dark" type="button" data-toggle="modal" data-target="#xlarge" onclick="js_pesquisa();" value="Procurar Campo">
              </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card">   
          <div class="card-content collapse show">
            <div class="card-body">
              <button type="button" class="btn btn-info btn-min-width mr-1 mb-1" onclick="js_documentacao_iframe()"  data-toggle="modal" data-target="#xlarge" >Campo Principal</button>
                <div class="form-body">

                  <label for="itensdef"><strong>Valores default:</strong></label>
                  
                  <div class="row">
                    <div class="col-md-5">
                      <div class="form-group">
                        <select multiple name="itensdef[]" class="form-control" onChange="js_mostradef(this)" size="13" id="itensdef" style="width:100%;">
                          <?php
                          if(isset($retorno)){
                            $result = $db_stdlib->db_query("SELECT * FROM db_syscampodef WHERE codcam = ".$retorno);
                            foreach ($result as $linha) {
                              echo "<option value=\"".$linha->defcampo."#&".$linha->defdescr."\">".$linha->defcampo."</option>\n";
                            }
                          }
                          ?>
                        </select> 
                      </div>
                    </div>

                    <div class="col-md-7">
                      <div class="form-group">
                        <label for="textodef"><strong>Nome Valor Default:</strong></label>
                        <?php $db_funcoes->db_input('textodef', 100, '', true, 'text', $db_opcao); ?>
                      </div>

                      <div class="form-group">
                        <label for="descitensdef"><strong>Descrição Valor Default:</strong></label>
                        <?php $db_funcoes->db_textarea('descitensdef', 2, 1, '', true, 'text', $db_opcao); ?>
                      </div>

                      <div class="form-group">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                          <button type="button" name="adicionar" class="btn btn-success" style="font-size:20px;" onClick="js_adddef(this.form)">+</button>
                          <button type="button" name="retirar" class="btn btn-danger" style="font-size:20px;" onClick="js_remdef(this.form)">-</button>
                          <button type="button" name="alterardef" class="btn btn-info" onClick="js_alterardef(this.form)">A</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</section>
<script language="JavaScript" type="text/javascript" src="<?php echo $Services_Funcoes->url_acesso(); ?>scripts/Extensions_Funcoes.js?n=<?php echo rand(0,1000); ?>"></script>
<script type="text/javascript">
function js_submeter(obj) {
  obj.elements["itensdef[]"].multiple = true;   
  for(var i = 0;i < obj.itensdef.length;i++)
    obj.itensdef.options[i].selected = true;
  return true;
}

document.form1.nomecam.focus();

function js_buscadefault(valor){
  if ( document.form1.incluir.disabled == false && valor !=0 ) {
    get = btoa('campodefault='+valor);
    location.href="<?php echo $Services_Funcoes->url_acesso_in(); ?>sys1_campos001/"+get;
  }
}

function js_pesquisa() {
  js_OpenJanelaIframe_Novo('#modal1_conteudo','<?php echo $Services_Funcoes->url_acesso(); ?>files/pages/func_db_syscampo001.php?funcao_js=parent.js_retornopesquisa|0', '100%', '580px');
}

function js_retornopesquisa(chave) {
  url       = '<?php echo $Services_Funcoes->url_acesso_in(); ?>';
  pagina    = '<?php echo $pagina; ?>';
  get       = btoa('retorno='+chave);

  link      = url+pagina+'/'+get;

  location.href = ''+link+'';
}

function js_fecharModal(modal) {
  $(modal).modal('hide');
  $('body').removeClass('modal-open');
  $('.modal-backdrop').remove();
}
</script>

<script>
function js_adddef(obj) {
  if(obj.textodef.value == "") {
    alert("Campo não pode ser vazio!");
  obj.textodef.focus();
  return false;
  }
  obj.elements["itensdef[]"].options[obj.elements["itensdef[]"].length] = new Option(obj.textodef.value,obj.textodef.value + '#&' + obj.descitensdef.value);
  obj.elements["itensdef[]"].options[obj.elements["itensdef[]"].length-1].select = true; 
  js_trocacordeselect();
  obj.textodef.value = "";
  obj.descitensdef.value = "";
  obj.textodef.focus();
}
function js_mostradef(obj) {
  var mat = new String(obj.options[obj.selectedIndex].value);
  mat = mat.split("#&");
  document.form1.textodef.value = mat[0];
  document.form1.descitensdef.value = mat[1];
  document.form1.adicionar.disabled = true;
  document.form1.retirar.disabled = false;
  document.form1.alterardef.disabled = false;
}
function js_alterardef(obj) {
  document.form1.adicionar.disabled = false;
  document.form1.retirar.disabled = true;
  document.form1.alterardef.disabled = true;
  obj.elements["itensdef[]"].options[obj.elements["itensdef[]"].selectedIndex].text = obj.textodef.value; 
  obj.elements["itensdef[]"].options[obj.elements["itensdef[]"].selectedIndex].value = obj.textodef.value + '#&' + obj.descitensdef.value;
  obj.textodef.value = "";
  obj.descitensdef.value = "";
  obj.textodef.focus();  
}
function js_remdef(obj) {
  if(!confirm("Excluir Item Default?"))
    return false;
  obj.elements["itensdef[]"].options[obj.elements["itensdef[]"].selectedIndex] = null;
  js_trocacordeselect();
  document.form1.adicionar.disabled = false;
  document.form1.retirar.disabled = true;
  document.form1.alterardef.disabled = true;
  obj.textodef.value = "";
  obj.descitensdef.value = "";
  obj.textodef.focus();  
}
function js_verifica(){
  if(document.form1.conteudo.value==0){
    alert('Selecione o tipo do campo!');
    return false;
  }
  var tam = new Number(document.form1.tamanho.value);
  if(isNaN(tam) || tam==''){
    alert('Verifique o tamanho do campo!');
    document.form1.tamanho.focus();
    return false;
  }  
  return true;
}

function js_documentacao_iframe() {
  js_OpenJanelaIframe_Novo('#modal1_conteudo','<?php echo $Services_Funcoes->url_acesso(); ?>files/pages/func_db_syscampo001.php?funcao_js=parent.js_recebecampo|codcam|nomecam', '100%', '580px');
}

function js_recebecampo(chave,nome){ 
  js_fecharModal('#xlarge');
  if(document.form1.alterar.disabled==false || document.form1.alterar.disabled==false){ 
    tam=document.form1.codcampai.options.length;  
    document.form1.codcampai.options[tam]=new Option(nome,chave,true);   
    if(tam%2==0){
      document.form1.codcampai.options[tam].style.backgroundColor= "#D7CC06";
    }else{  
      document.form1.codcampai.options[tam].style.backgroundColor= "#F8EC07";
    }
  }else{  
    js_buscadefault(chave);
  }  
}

</script>