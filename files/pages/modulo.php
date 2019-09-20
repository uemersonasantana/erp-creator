<?php

$nomemod = $db_stdlib->db_getsession("DB_nome_modulo");

/**
 * Update na usuariosonline
 */
$sSqlUsuariosOnline  = "update db_usuariosonline                  ";
$sSqlUsuariosOnline .= "   set uol_arquivo = '',                  ";
$sSqlUsuariosOnline .= "       uol_modulo  = '" . $nomemod . "',  ";
$sSqlUsuariosOnline .= "       uol_inativo = " . time();
$sSqlUsuariosOnline .= " where uol_id   = " . $db_stdlib->db_getsession("DB_id_usuario");
$sSqlUsuariosOnline .= "   and uol_ip   = '" . $_SERVER['REMOTE_ADDR'] . "'";
$sSqlUsuariosOnline .= "   and uol_hora = " . $db_stdlib->db_getsession("DB_uol_hora");

$db_stdlib->db_query($sSqlUsuariosOnline);

/**
 * Verifica registro na usumod
 * Insere caso não exista ou atualiza o registro
 */
$sSqlUsuariosModulo  = "select id_item   ";
$sSqlUsuariosModulo .= "  from db_usumod ";
$sSqlUsuariosModulo .= " where id_usuario = " . $db_stdlib->db_getsession("DB_id_usuario");
$sSqlUsuariosModulo .= "   and id_item    = " . $db_stdlib->db_getsession("DB_modulo");
$result              = $db_stdlib->db_query($sSqlUsuariosModulo);

if( $result->rowCount() == 0 ) {

  $sSqlInsertUsariosModulo = "insert into db_usumod values(".$db_stdlib->db_getsession("DB_modulo").",".$db_stdlib->db_getsession("DB_anousu").",".$db_stdlib->db_getsession("DB_id_usuario").")";
  
  $db_stdlib->db_query($sSqlInsertUsariosModulo);
} else {

  $sSqlUpdateUsariosModulo  = "update db_usumod                                  ";
  $sSqlUpdateUsariosModulo .= "   set id_item = " . $db_stdlib->db_getsession("DB_modulo") . ",";
  $sSqlUpdateUsariosModulo .= "       anousu  = " . $db_stdlib->db_getsession("DB_anousu");
  $sSqlUpdateUsariosModulo .= " where id_usuario = " . $db_stdlib->db_getsession("DB_id_usuario");
  $sSqlUpdateUsariosModulo .= "   and id_item    = " . $db_stdlib->db_getsession("DB_modulo");
  
  $db_stdlib->db_query($sSqlUpdateUsariosModulo);
}

if ( $db_stdlib->db_getsession("DB_id_usuario") == 1 ) {

  $sSql  = "select id_usuario, anousu   ";
  $sSql .= "  from db_permissao         ";
  $sSql .= " where id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario");
  $sSql .= "group by id_usuario, anousu ";
  $sSql .= "order by anousu desc        ";
} else {

  $sSql  = " select distinct on (anousu) anousu, id_usuario                                     ";
  $sSql .= "   from (select id_usuario, anousu                                                  ";
  $sSql .= "           from db_permissao                                                        ";
  $sSql .= "          where id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario");
  $sSql .= "       group by id_usuario, anousu                                                  ";
  $sSql .= "       union all                                                                    ";
  $sSql .= "         select db_permissao.id_usuario, anousu                                     ";
  $sSql .= "           from db_permissao                                                        ";
  $sSql .= "                inner join db_permherda h on h.id_perfil  = db_permissao.id_usuario ";
  $sSql .= "                inner join db_usuarios  u on u.id_usuario = h.id_perfil             ";
  $sSql .= "                                         and u.usuarioativo = '1'                   ";
  $sSql .= "          where h.id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario");
  $sSql .= "         group by db_permissao.id_usuario, anousu                                   ";
  $sSql .= "         ) as x                                                                     ";
  $sSql .= "order by anousu desc                                                                ";
}

$result = $db_stdlib->db_query($sSql);
if( $result->rowCount() == 0 ) {
  echo "Você não tem permissão de acesso para exercício ".$db_stdlib->db_getsession("DB_anousu").". <br/>
  Contate o administrador para maiores informações ou selecione outro exercício.\n";
}

$sSqlModulos      = "select nome_modulo, descr_modulo from db_modulos where id_item = ".$db_stdlib->db_getsession("DB_modulo");
$rsModulos        = $db_stdlib->db_query($sSqlModulos)->fetch();

$sNomeModulo      = $rsModulos->nome_modulo;
$sDescricaoModulo = $rsModulos->descr_modulo;

$sSqlUsuarioLogado = "select login, nome from db_usuarios where id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario");
$rsUsuarioLogado   = $db_stdlib->db_query($sSqlUsuarioLogado)->fetch();
$sLogin            = $rsUsuarioLogado->login;
$sNome             = $rsUsuarioLogado->nome;

//  Avisa o usuário que o exercício está diferente do exercício da data.
if( $db_stdlib->db_getsession("DB_anousu")!= date("Y",$db_stdlib->db_getsession("DB_datausu")) ) {
  echo "<script>alert('Exercício diferente do exercício da data. Verifique!');</script>";
}
?>

<form name="modulo" method="post">

<div class="table-responsive">

<table class="table" border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td>Módulo:</td>
    <td nowrap>
      <?php echo $sNomeModulo; ?>
      &nbsp;&nbsp;<font style="font-size:10px">(<?php echo $sDescricaoModulo; ?>)</font>
    </td>
  </tr>

  <tr>
    <td>Leia as Atualizações:</td>
    <td nowrap>
      <font style="font-size:10px"><strong><a href='#' onclick='js_atualizacao_versao();'>Clique Aqui</a></strong></font>
    </td>
  </tr>

  <tr>
    <td>Usuário:</td>
    <td nowrap>
      <?php echo $sLogin; ?>
      &nbsp;&nbsp;<font style="font-size:10px">(<?php echo $sNome; ?>)</font>
    </td>
  </tr>

  <tr>
    <td>Exercício:</td>
    <td>
      <?php
        if( $db_stdlib->db_getsession("DB_anousu") != date("Y",$db_stdlib->db_getsession("DB_datausu")) ) {
          echo "<span class='bold' style='font-size:15px;'>".$db_stdlib->db_getsession("DB_anousu")."</span>";
        }else{
          echo $db_stdlib->db_getsession("DB_anousu");
        }
      ?>
    </td>
  </tr>

  <tr>
    <td>Alternar exercício:</td>
    <td>
      <select name="formAnousu" size="1" onChange="document.modulo.submit();">
      <option value="">&nbsp;</option>
      <?php
        foreach ( $result as $linha ) {
          echo "<option value=\"".$linha->anousu."\">".$linha->anousu."</option>\n";
        }
      ?>
      </select>
    </td>
  </tr>

  <tr>
    <td>
  <?php

    $mostra_menu = false;

    $sSql  = " select distinct d.coddepto, d.descrdepto, u.db17_ordem               ";
    $sSql .= "   from db_depusu u                                                   ";
    $sSql .= "        inner join db_depart d   on u.coddepto     = d.coddepto       ";
    $sSql .= "        left join db_departorg o on u.coddepto     = o.db01_coddepto  ";
    $sSql .= "        left join orcdotacao     on o58_anousu     = ".$db_stdlib->db_getsession("DB_anousu");
    $sSql .= "                                and o.db01_orgao   = o58_orgao        ";
    $sSql .= "                                and o.db01_unidade = o58_unidade      ";
    $sSql .= "   where u.id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario");
    $sSql .= "     and o58_instit   = ".$db_stdlib->db_getsession("DB_instit");
    $sSql .= "     and o58_anousu   = ".$db_stdlib->db_getsession("DB_anousu");
    $sSql .= "order by u.db17_ordem";

    /**
     * Se o usuario tiver departamento, aparecem os departamentos
     * Se não tiver, aparecem todos e monta os menus que tiver permissao
     */
    $sSql   = "  select distinct d.coddepto, d.descrdepto, u.db17_ordem   ";
    $sSql  .= "    from db_depusu u                                       ";
    $sSql  .= "         inner join db_depart d on u.coddepto = d.coddepto ";
    $sSql  .= "   where instit       = ".$db_stdlib->db_getsession("DB_instit");
    $sSql  .= "     and u.id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario");
    $sSql  .= "     and (d.limite is null or d.limite >= '" . date("Y-m-d",$db_stdlib->db_getsession("DB_datausu")) . "')";
    $sSql  .= "order by u.db17_ordem ";
    $result = $db_stdlib->db_query($sSql);

    if( $result->rowCount() == 0 ) {

      echo "<hr>";
      echo "Usuário sem departamento para acesso cadastrado!";
    } else {
      // Caso o usuario entre no modulo e mude o departamento na lista entra aqui
      if( isset($coddepto) ) {

        $db_stdlib->db_putsession("DB_coddepto",$coddepto);
        $result    = $db_stdlib->db_query("select descrdepto from ($sSql) as x where coddepto = $coddepto");
        $nomedepto = $result->fetch()->descrdepto;
        $db_stdlib->db_putsession("DB_nomedepto",$nomedepto);

      // Caso o usuario acesse o modulo pela primeira vez
      } else if ( isset($_SESSION["DB_coddepto"]) ) {

        $coddepto       = $db_stdlib->db_getsession("DB_coddepto");
        $sSqlVerifica   = "select instit from db_depart where coddepto = $coddepto";
        $resultverifica = $db_stdlib->db_query($sSqlVerifica);

        if ( $resultverifica->fetch()->instit != $db_stdlib->db_getsession("DB_instit")) {

          $result = $db_stdlib->db_query($sSql)->fetch();
          $db_stdlib->db_putsession("DB_coddepto" ,$result->coddepto);
          $db_stdlib->db_putsession("DB_nomedepto",$result->descrdepto);
        }

        $sSqlDepusu   = "select *                                                        ";
        $sSqlDepusu  .= "  from db_depusu                                                ";
        $sSqlDepusu  .= "     inner join db_depart d on db_depusu.coddepto = d.coddepto  ";
        $sSqlDepusu  .= " where db_depusu.id_usuario = " . $db_stdlib->db_getsession("DB_id_usuario");
        $sSqlDepusu  .= "    and db_depusu.coddepto  = " . $coddepto;
        $sSqlDepusu  .= "   and (d.limite is null or d.limite >= '" . date("Y-m-d",$db_stdlib->db_getsession("DB_datausu")) . "')";
        $resultdepusu = $db_stdlib->db_query($sSqlDepusu);

        if ( $resultdepusu->rowCount() == 0 ) {

          $result = $db_stdlib->db_query($sSql)->fetch();
          $db_stdlib->db_putsession("DB_coddepto" ,$result->coddepto);
          $db_stdlib->db_putsession("DB_nomedepto",$result->descrdepto);
        }
      }

      echo "Departamento:&nbsp;&nbsp;</td><td>";

      $mostra_menu = true;
      
      $result      = $db_stdlib->db_query($sSql);

      $db_funcoes         =   new dbforms\db_funcoes; 
      $db_funcoes->db_selectrecord(
                                    'modulo'
                                    ,'coddepto'
                                    ,$result
                                    ,true
                                    ,2
                                    ,''
                                    ,''
                                    ,''
                                    ,''
                                    ,'js_mostramodulo(document.modulo.coddepto.value,document.modulo.coddeptodescr.options.text)');

      if( !isset($_SESSION["DB_coddepto"]) ) {
        $result_temp  = $db_stdlib->db_query($sSql)->fetch();

        $db_stdlib->db_putsession("DB_coddepto" ,$result_temp->coddepto);
        $db_stdlib->db_putsession("DB_nomedepto",$result_temp->descrdepto);
      }

      $db_stdlib->db_logsmanual_demais(
                                        "Acesso ao Módulo - Login: ".$db_stdlib->db_getsession("DB_login")
                                        ,$db_stdlib->db_getsession("DB_id_usuario")
                                        ,$db_stdlib->db_getsession("DB_modulo")
                                        ,0
                                        ,$db_stdlib->db_getsession("DB_coddepto")
                                        ,$db_stdlib->db_getsession("DB_instit")
                                      );
    }

    if( $db_stdlib->db_getsession("DB_modulo") == 1 ) {
      $mostra_menu = true;
    }

    $sSql   = "select * from db_datausuarios where id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario");
    $resusu = $db_stdlib->db_query($sSql);

    if( $resusu->rowCount() > 0 ) {

      if ( date("Y-m-d",$db_stdlib->db_getsession("DB_datausu")) != $resusu->fetch()->data ) {

        if ( $db_stdlib->db_permissaomenu($db_stdlib->db_getsession("DB_anousu"), 1, 3896) == true ) {
          // ATENCAO
          //$db_stdlib->db_redireciona("con4_trocadata.php");
        }else{
          $sSql = "delete from db_datausuarios where id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario");
          $resusu = $db_stdlib->db_query($sSql);
        }
      }
    }
    ?>
        </td>
      </tr>
    </table>
    </td>

    <td width="390" valign="top">
    <?
    if($mostra_menu==true){
      ?>
        <table width="40%" class="table-inverse table-striped">
          <thead>
            <tr>
              <th colspan="3" class="text-center">Últimos acessos ao Módulo</th>
            </tr>
          </thead>
          <tbody>
        <?php
        $sSql  = "select * from (                                                                          ";
        $sSql .= "                select descricao,                                                        ";
        $sSql .= "                       data,                                                             ";
        $sSql .= "                       hora,                                                             ";
        $sSql .= "                       id_item,                                                          ";
        $sSql .= "                       help,                                                             ";
        $sSql .= "                funcao from ( select distinct on (funcao) d.descricao,                   ";
        $sSql .= "                                                          x.data,                        ";
        $sSql .= "                                                          x.hora,                        ";
        $sSql .= "                                                          x.id_item,                     ";
        $sSql .= "                                                          help,                          ";
        $sSql .= "                                                          case when m.id_item is null    ";
        $sSql .= "                                                               then d.funcao else null   ";
        $sSql .= "                                                             end as funcao               ";
        $sSql .= "                              from ( select *                                            ";
        $sSql .= "                                       from db_logsacessa a                              ";
        $sSql .= "                                      where a.id_modulo  = ".$db_stdlib->db_getsession("DB_modulo");
        $sSql .= "                                        and a.id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario");
        $sSql .= "                                        and a.instit     = ".$db_stdlib->db_getsession("DB_instit");

        $dDataFim = date('Y-m-d');
        $dDataIni = date('Y-m-d', strtotime($dDataFim . ' -365 day'));

        $sSql .= "                                        and a.data between '{$dDataIni}' and '{$dDataFim}' ";
        $sSql .= "                                        and a.id_item <> 0 ";
        $sSql .= "                                      order by a.data desc, a.hora desc                  ";
        $sSql .= "                                         limit 20                                        ";
        $sSql .= "                                   ) as x                                                ";
        $sSql .= "                                   inner join db_itensmenu d    on x.id_item = d.id_item ";
        $sSql .= "                                   left outer join db_modulos m on m.id_item = d.id_item ";
        $sSql .= "                             where d.itemativo = '1'                                     ";
        $sSql .= "                               and d.libcliente is true                                  ";
        $sSql .= "                             ) as x                                                      ";
        $sSql .= "                                    ) as x                                               ";
        $sSql .= "                        order by data desc, hora desc                                    ";

        $result = $db_stdlib->db_query($sSql);

        if( $result->rowCount() > 0 ){

          foreach ( $result as $linha ) {
            ?>
            <tr>
              <td width="50%" title="<?php echo $help; ?>">
                <?php 
                if( $linha->funcao=="" ){
                  echo "<a href=\"\" >$linha->descricao</a>";
                }else{

                  $sSql  = "select descricao                                                                  ";
                  $sSql .= "           from db_menu                                                           ";
                  $sSql .= "                inner join db_itensmenu on db_menu.id_item = db_itensmenu.id_item ";
                  $sSql .= "          where id_item_filho = ".$linha->id_item;
                  $sSql .= "            and modulo        = ".$db_stdlib->db_getsession("DB_modulo");
                  $resultpai = $db_stdlib->db_query($sSql);

                  $linha->descrpai = "";
                  if( $resultpai->rowCount() > 0 ) {
                     $descrpai = $resultpai->fetch()->descricao;
                  }
                  echo "<a href=\"$linha->funcao\" title=\"".$linha->descrpai.">".$linha->descricao."\"onclick=\"return js_verifica_objeto('DBmenu_$linha->id_item');\">$linha->descricao</a>";
                }
                ?>
              </td>
              <td align="center" width="30%"><?php echo $db_stdlib->db_formatar($linha->data,'d'); ?></td>
              <td align="center" width="20%"><?php echo $linha->hora; ?></td>
            </tr>

            <?
          }
        }
        echo "</tbody>";
        echo "</table>";
      }
          ?>
      </td>
    </tr>
</table>

</div>

</form>

<script language="JavaScript" type="text/javascript" src="scripts/Services_Funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/Services_md5.js"></script>
<script type="text/javascript">
  function js_mostramodulo(chave1,chave2){
    parametros = btoa("coddepto="+chave1+"&retorno=true&nomedepto="+chave2);
    
    location.href="<?php echo $Services_Funcoes->url_acesso_in(); ?>modulo/"+parametros;
  }
                
      

  <?php /*function js_atualizacao_versao(){
    js_OpenJanelaIframe('CurrentWindow.corpo','dbiframe_atualiza','con3_versao004.php?id_item=<?=db_getsession("DB_modulo")."&tipo_consulta=M"?>',"Atualizacoes");
  }*/ ?>
</script>