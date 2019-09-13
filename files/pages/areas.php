<?php

$_SESSION["DB_itemmenu_acessado"] 	=	"0";

$db_stdlib->db_query("UPDATE 
                            db_usuariosonline
                        SET 
                            uol_arquivo = ''
                            ,uol_modulo = 'Selecionando Área'
                            ,uol_inativo = ".time()."
                        WHERE uol_id = ".$db_stdlib->db_getsession("DB_id_usuario")."
                            and uol_ip = '".$_SERVER['REMOTE_ADDR']."'
                            and uol_hora = ".$db_stdlib->db_getsession("DB_uol_hora")) or die("Erro(26) atualizando db_usuariosonline");

$rsInstituicao = $db_stdlib->db_query("SELECT nomeinst as nome, ender, telef, cep, email, url FROM db_config WHERE codigo = ".$db_stdlib->db_getsession("DB_instit"));

if( $db_stdlib->db_getsession("DB_id_usuario") == "1" || $db_stdlib->db_getsession("DB_administrador") == 1 ) {
    $rsArea = $db_stdlib->db_query("SELECT 
	    									distinct at26_sequencial
	    									,at25_descr
	    									,at25_figura 
                         				FROM atendcadarea 
                              				INNER JOIN atendcadareamod ON at26_sequencial = at26_codarea
                        				ORDER BY at25_descr");
} else {
  $rsArea = $db_stdlib->db_query("SELECT 
  										distinct at26_sequencial
  										,at25_descr
  										, at25_figura
						            FROM atendcadarea 
						            	INNER JOIN atendcadareamod on at26_sequencial=at26_codarea
						            WHERE 
						             	at26_id_item in (
						     
								            select id_item from (
								                    select distinct i.id_modulo as id_item,m.descr_modulo,it.help,it.funcao,m.imagem,m.nome_modulo,
								                           case when u.anousu is null then to_char(CURRENT_DATE,'YYYY')::int4 else u.anousu end
								                    from ( select distinct i.itemativo,p.id_modulo,p.id_usuario,p.id_instit
								                           from db_permissao p 
								                                inner join db_itensmenu i on p.id_item = i.id_item 
								                           where i.itemativo = 1
								                             and p.id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario")."
								                             and p.id_instit = ".$db_stdlib->db_getsession("DB_instit")." 
								                             and p.anousu = ".(isset($HTTP_SESSION_VARS["DB_datausu"])?date("Y",$db_stdlib->db_getsession("DB_datausu")):date("Y"))." 
								                         ) as i           
								                         inner join db_modulos m on m.id_item = i.id_modulo
								                         inner join db_itensmenu it on it.id_item = i.id_modulo
								                         left outer join db_usumod u on u.id_item = i.id_modulo and u.id_usuario = i.id_usuario
								                    where i.id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario")."
								                      and i.id_instit = ".$db_stdlib->db_getsession("DB_instit")."    and libcliente is true 
								                 
								                   union
								          
								                   select distinct i.id_modulo as id_item,m.descr_modulo,it.help,it.funcao,m.imagem,m.nome_modulo,
								                         case when u.anousu is null then to_char(CURRENT_DATE,'YYYY')::int4 else u.anousu end
								                   from  (
								                           select distinct i.itemativo,p.id_modulo,h.id_usuario,p.id_instit
																from db_permissao p 
																      inner join db_permherda h on h.id_perfil = p.id_usuario
																inner join db_usuarios u on u.id_usuario = h.id_perfil and u.usuarioativo = '1'
																inner join db_itensmenu i 
																on p.id_item = i.id_item 
																where i.itemativo = 1
																and h.id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario")."
																and p.id_instit = ".$db_stdlib->db_getsession("DB_instit")." 
																and p.anousu = ".(isset($HTTP_SESSION_VARS["DB_datausu"])?date("Y",$db_stdlib->db_getsession("DB_datausu")):date("Y"))." 
																) as i            
												        		
												        		inner join db_modulos m on m.id_item = i.id_modulo
																inner join db_itensmenu it on it.id_item = i.id_modulo
												          		left outer join db_usumod u on u.id_item = i.id_modulo and u.id_usuario = i.id_usuario
												          
												          	where i.id_usuario = ".$db_stdlib->db_getsession("DB_id_usuario")." and libcliente is true and i.id_instit = ".$db_stdlib->db_getsession("DB_instit") . "
												)  as yyy " . ( isset($area_de_acesso) ? " 
												inner join atendcadareamod on yyy.id_item = at26_id_item where at26_codarea = $area_de_acesso
												       ": "" )." order by nome_modulo 
												) order by at25_descr");
}

?>
<div class="row">
    <?php 
        //  Se tiver somente uma instituição o sistema redireciona automaticamente para o corpo.     
        foreach ( $rsArea as $linha ) {
            $sLogoImagem   =    $Services_Skins->getPathFile('img','') . '/' . $linha->at26_sequencial;
  
            /**
             * Quando não encontrar imagem configurada deve setar a imagem default
             */
            if( !file_exists( $sLogoImagem ) ){
                $sLogoImagem   =    $Services_Skins->getSkinLink() . 'img/' . $linha->at26_sequencial . '.png';
            } else {
                $sLogoImagem   =    $Services_Skins->getSkinLink() . 'img/logoFallBack.png';
            }

            echo "
                    <div class=\"col-xl-3 col-md-6 col-sm-12\">
                        <a title='".$linha->at25_descr."' href=\"".$Services_Funcoes->url_acesso_in()."modulos/".base64_encode("instit=".$db_stdlib->db_getsession("DB_instit")."&area_de_acesso=".$linha->at26_sequencial )."\">
                            <div class=\"card text-white box-shadow-0 bg-info\">
                                <div class=\"card-content collapse show\">
                                    <div class=\"card-body text-center\">
                                        <img src=\"".$sLogoImagem."\" alt=\"".$linha->at25_descr."\" onmouseover=\"js_msg_status(this.alt)\" onmouseout=\"js_lmp_status()\" border=\"0\" >
                                        <p style='white-space: nowrap;' class=\"card-text\"><b>".$linha->at25_descr."</b></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                ";
        }
     ?>
</div>