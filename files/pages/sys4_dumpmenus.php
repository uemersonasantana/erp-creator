<?php

$db_funcoes       = new dbforms\db_funcoes;

$arquivo = $_SERVER['DOCUMENT_ROOT']."/tmp/atualiza_menus.txt";
$fd = fopen($arquivo,"w");

fputs($fd,"<?php \n");
fputs($fd,"//data : ".date("d/m/Y",$db_stdlib->db_getsession("DB_datausu"))."\n");

// itens dos menus 
$sql = "SELECT * FROM db_itensmenu";
$result = $db_stdlib->db_query($sql);
if ( $result->rowCount() > 0 ) {
  for ( $i=0; $i<$result->rowCount(); $i++ ) {
     $db_stdlib->db_fieldsmemory($result,$i);
     fputs($fd,'$sql = "delete FROM db_itensmenu where id_item = '.$id_item.'";'."\n");
     fputs($fd,'$result = $db_stdlib->db_query($sql);'."\n");
     fputs($fd,'$sql = "insert into db_itensmenu(id_item,descricao,help,funcao,itemativo,manutencao,desctec) value ('.$id_item.",'".$descricao."','".$help."','".$funcao."','".$itemativo."','".$manutencao."','".$desctec.'\')";'."\n");
     fputs($fd,'$result = $db_stdlib->db_query($sql);'."\n");
  }
}

// menus 
$sql = "SELECT * FROM db_menu";
$result = $db_stdlib->db_query($sql);
if( $result->rowCount() > 0 ) {
  for( $i=0; $i<$result->rowCount(); $i++ ) {
     $db_stdlib->db_fieldsmemory($result,$i);
     fputs($fd,'$sql = "delete FROM db_menu where id_item = '.$id_item.' and id_item_filho = '.$id_item.' and modulo = '.$modulo.'";'."\n");
     fputs($fd,'$result = $db_stdlib->db_query($sql);'."\n");
     fputs($fd,'$sql = "insert into db_menu(id_item,id_item_filho,menusequencia,modulo) value ('.$id_item.",".$id_item_filho.",".$menusequencia.",".$modulo.')";'."\n");
     fputs($fd,'$result = $db_stdlib->db_query($sql);'."\n");
  }
}

// módulos dos menus
$sql = "SELECT * FROM db_modulos";
$result = $db_stdlib->db_query($sql);
if ( $result->rowCount() > 0 ) {
  for ( $i=0; $i<$result->rowCount(); $i++ ) {
     $db_stdlib->db_fieldsmemory($result,$i);
     fputs($fd,'$sql = "delete FROM db_modulo where id_item = '.$id_item.'";'."\n");
     fputs($fd,'$result = $db_stdlib->db_query($sql);'."\n");
     fputs($fd,'$sql = "insert into db_modulo(id_item,nome_modulo,descr_modulo,imagem,temexerc) value ('.$id_item.",'".$nome_modulo."','".$descr_modulo."','".$imagem."','".$temexerc."')\";"."\n");
     fputs($fd,'$result = $db_stdlib->db_query($sql);'."\n");
  }
}

// permissoes do usuário ID 1
fputs($fd,'$sql = "delete FROM db_permissao where id_usuario = 1";'."\n");
fputs($fd,'$result = $db_stdlib->db_query($sql);'."\n");

$sql = "SELECT * FROM db_permissao where id_usuario = 1";
$result = $db_stdlib->db_query($sql);
if ( $result->rowCount() > 0 ) {
for( $i=0; $i<$result->rowCount(); $i++ ) {
     $db_stdlib->db_fieldsmemory($result,$i);
     fputs($fd,'$sql = "insert into db_permissao(id_usuario,id_item,permissaoativa,anousu,id_instit,id_modulo) value ('.$id_usuario.",".$id_item.",'".$permissaoativa."',".$anousu.",".$id_instit.",".$id_modulo.")\";"."\n");
     fputs($fd,'$result = $db_stdlib->db_query($sql);'."\n");
  }
}
fputs($fd,'?>'."\n");
fclose($fd);
?>
<div class="card-content collapse show">
  <section id="basic-form-layouts">
    <div class="row match-height">
      <div class="col-md-5">
        <div class="card">
          <div class="form-body">
            <h4 class="form-section">
              <a name="arquivo" href="#" onclick="js_Dump()" title="Arquivo gerado no formato php">Clique aqui para baixar o arquivo dos menus</a>
            </h4>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script>
  function js_Dump(){
    jan = window.open('<?php echo $Services_Funcoes->url_acesso(); ?>tmp/<?=basename($arquivo)?>');
    jan.moveTo(0,0);
  }
</script>