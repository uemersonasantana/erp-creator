<?php

$_SESSION["DB_itemmenu_acessado"] 	=	"0";

$db_stdlib->db_query("UPDATE 
                            db_usuariosonline
                        SET 
                            uol_arquivo = ''
                            ,uol_modulo = 'Selecionando Módulo'
                            ,uol_inativo = ".time()."
                        WHERE uol_id = ".$db_stdlib->db_getsession("DB_id_usuario")."
                            and uol_ip = '".$_SERVER['REMOTE_ADDR']."'
                            and uol_hora = ".$db_stdlib->db_getsession("DB_uol_hora")) or die("Erro(26) atualizando db_usuariosonline");

if( !empty($DB_SELLER) ){ 
	if( !empty($_SESSION["DB_SELLER"]) ) {
		$db_stdlib->db_putsession("DB_SELLER","on");
	}
	if( !empty($_SESSION["DB_NBASE"]) ) {
		$db_stdlib->db_putsession("DB_NBASE",$DB_BASE);
	}
} else if( isset($_SESSION["DB_NBASE"]) ) {
	unset($_SESSION["DB_NBASE"]);
}

//	Converte a string em variáveis
if ( isset($get) ) {
	parse_str(base64_decode($get));
}

if( !isset($_SESSION["DB_instit"]) ) {
	if ( isset($instit) ) {
		$db_stdlib->db_putsession("DB_instit",$instit);
	} else {
		// Caso não esteja selecionado uma instituição e o usuário tentar acessar as áreas.
		echo "<script>location.href='instit';</script>";
		exit;
	}

  	$db_stdlib->db_logsmanual_demais("Acesso instituição - Login: ".$db_stdlib->db_getsession("DB_login"),$db_stdlib->db_getsession("DB_id_usuario"),0,0,0,$db_stdlib->db_getsession("DB_instit"));
} else {  
  if( isset($area_de_acesso) ){
  	$db_stdlib->db_putsession("DB_Area",$area_de_acesso);
  }

}

if( $db_stdlib->db_getsession("DB_instit") == "" ) {
	$db_stdlib->db_erro("Instituição não selecionada.",0);
}

$rsInstituicao = $db_stdlib->db_query("select nomeinst as nome,ender,telef,cep,email,url from db_config where codigo = ".$db_stdlib->db_getsession("DB_instit"));

if(	isset($_SESSION["DB_Area"]) )	{
	$area_de_acesso = $db_stdlib->db_getsession("DB_Area");
}

if ( $db_stdlib->db_getsession("DB_id_usuario") == 1 || $db_stdlib->db_getsession("DB_administrador") == 1 ) {
  	$sSqlmodulos = "SELECT 
  							distinct  db_modulos.id_item
                           	,db_modulos.descr_modulo
                           	,db_itensmenu.help
                           	,db_itensmenu.funcao
                           	,db_modulos.imagem
                           	,db_modulos.nome_modulo
                           	,extract (year from current_date) as anousu
                      	FROM db_itensmenu
                           INNER JOIN db_menu 		ON  db_itensmenu.id_item = db_menu.id_item
                           INNER JOIN db_modulos 	ON 	db_itensmenu.id_item = db_modulos.id_item
                           ";

	if ( isset($area_de_acesso) ) {
		$sSqlmodulos 	.= " 
	 				inner join atendcadareamod on db_modulos.id_item = at26_id_item
	                where libcliente is true and at26_codarea = $area_de_acesso";
	} else {
		$sSqlmodulos 	.= " where libcliente is true ";
	}

 	$sSqlmodulos		.= "	order by db_modulos.nome_modulo";
} else { 
  $sSqlmodulos = "SELECT * FROM 
  								(
							      select 
							      		distinct i.id_modulo as id_item
							      		,m.descr_modulo
							      		,it.help
							      		,it.funcao
							      		,m.imagem
							      		,m.nome_modulo
							      		,case when u.anousu is null then to_char(CURRENT_DATE,'YYYY')::int4 else u.anousu end
							                    FROM
											        (
											          select distinct 
											          		i.itemativo
											          		,p.id_modulo
											          		,p.id_usuario
											          		,p.id_instit
											          from db_permissao p
												          inner join db_itensmenu i
												          on p.id_item = i.id_item
											          where i.itemativo = 1
												          and p.id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario")."
												          and p.id_instit = ".$db_stdlib->db_getsession("DB_instit")."
												          and (p.anousu = ".(isset($HTTP_SESSION_VARS["DB_datausu"])?date("Y",$db_stdlib->db_getsession("DB_datausu")):date("Y"))."
												           or  p.anousu = ".(isset($HTTP_SESSION_VARS["DB_datausu"])?date("Y",$db_stdlib->db_getsession("DB_datausu")):date("Y"))."+1)
											        ) as i
								                    inner join db_modulos m on m.id_item = i.id_modulo
								        			inner join db_itensmenu it on it.id_item = i.id_modulo
								                    left outer join db_usumod u on u.id_item = i.id_modulo and u.id_usuario = i.id_usuario
                    								
                    								where i.id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario")." and i.id_instit = ".$db_stdlib->db_getsession("DB_instit")." and libcliente is true
      									union
    									
    									select 
    										distinct i.id_modulo as id_item
    										,m.descr_modulo
    										,it.help
    										,it.funcao
    										,m.imagem
    										,m.nome_modulo
    										,case when u.anousu is null then to_char(CURRENT_DATE,'YYYY')::int4 else u.anousu end
										from
									       (
									         select 
									         	distinct i.itemativo
									         	,p.id_modulo
									         	,h.id_usuario
									         	,p.id_instit
									         from db_permissao p
									            inner join db_permherda h on h.id_perfil = p.id_usuario
									         	inner join db_usuarios u on u.id_usuario = h.id_perfil and u.usuarioativo = '1'
									         	inner join db_itensmenu i on p.id_item = i.id_item
									         
									         where 
									         	i.itemativo = 1
									         	and h.id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario")."
										        and p.id_instit = ".$db_stdlib->db_getsession("DB_instit")."
										        and (p.anousu = ".(isset($HTTP_SESSION_VARS["DB_datausu"])?date("Y",$db_stdlib->db_getsession("DB_datausu")):date("Y"))." or  p.anousu = ".(isset($HTTP_SESSION_VARS["DB_datausu"])?date("Y",$db_stdlib->db_getsession("DB_datausu")):date("Y"))."+1)
									       ) as i
										    inner join db_modulos m on m.id_item = i.id_modulo
										    inner join db_itensmenu it on it.id_item = i.id_modulo
											left outer join db_usumod u on u.id_item = i.id_modulo and u.id_usuario = i.id_usuario
										
										where 
											i.id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario")." and libcliente is true and i.id_instit = ".$db_stdlib->db_getsession("DB_instit") . "
										       )  as yyy ";

  $iNumModulos = isset($_SESSION["DB_totalmodulos"]) == true ? $_SESSION["DB_totalmodulos"] : 0;

  if( (isset($area_de_acesso) && $iNumModulos > 20) || (!isset($_GET["link"]) && isset($area_de_acesso))){
    $sSqlmodulos .= "
            inner join atendcadareamod on yyy.id_item = at26_id_item
            where at26_codarea = $area_de_acesso
             ";
  }

  $sSqlmodulos .= " order by nome_modulo ";
}

$rsModulos 			  = $db_stdlib->db_query($sSqlmodulos);
$iNumRowsModulos 	= $rsModulos->rowCount();


if ( $iNumRowsModulos == 0 ) {
	db_erro("Usuário sem nenhuma permissao de acesso! Contate suporte!",0);
   	exit;
}

?>
<div class="row">
    <?php 
        //  Se tiver somente uma instituição o sistema redireciona automaticamente para o corpo.     
        foreach ( $rsModulos as $linha ) {
            $sLogoImagem   =    $Services_Skins->getPathFile('img','Modulos') . '/' . $linha->imagem;
  
            /**
             * Quando não encontrar imagem configurada deve setar a imagem default
             */
            if( !file_exists( $sLogoImagem ) ){
                $sLogoImagem   =    $Services_Skins->getSkinLink() . 'img/Modulos/' . $linha->db21_tipoinstit . '.png';
            } else {
                $sLogoImagem   =    $Services_Skins->getSkinLink() . 'img/logoFallBack.png';
            }

            echo "
                    <div class=\"col-xl-3 col-md-6 col-sm-12\">
                        <a title='".$linha->help."' href=\"".$Services_Funcoes->url_acesso_in()."modulo.php?".base64_encode("anousu=".$linha->anousu."&modulo=".$linha->id_item."&nomemod=".$linha->nome_modulo)."\">
                            <div class=\"card text-white box-shadow-0 bg-info\">
                                <div class=\"card-content collapse show\">
                                    <div class=\"card-body text-center\">
                                        <img src=\"".$sLogoImagem."\" alt=\"".$linha->help."\" onmouseover=\"js_msg_status(this.alt)\" onmouseout=\"js_lmp_status()\" border=\"0\" width=\"100\" height=\"100\">
                                        <p style='white-space: nowrap;' class=\"card-text\"><b>".$linha->nome_modulo."</b></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                ";
        }
     ?>
</div>