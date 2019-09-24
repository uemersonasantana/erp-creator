<?php

/**
 * Funções de cabeçalho para as páginas: Instituições, Áreas, Módulos e Módulo.
 */
$db_stdlib->log_db_usuariosonline('udpate','Selecionando Instituição');


$sDataSistema   					= 	date('Y-m-d', $db_stdlib->db_getsession("DB_datausu", false) ?: time());
$iUsuarioLogado 					=	$db_stdlib->db_getsession("DB_id_usuario");

/**
 * Caso usuário não esteja atualizado ele não tenha cancelado a atualizacao do cadastro
 * 
 
if ( !$oUsuario->isAtualizado() && ( !isset($_SESSION['DB_atualiza_cadastro']) || $_SESSION['DB_atualiza_cadastro'] === true ) ) {
  db_redireciona('con4_atualizacadastro001.php');
  exit;
}
*/


$sSqlInstit =  "SELECT 
                    c.codigo
                    ,c.nomeinst 
                    ,c.figura, db21_tipoinstit
                FROM db_config c
                    INNER JOIN db_userinst u ON u.id_instit = c.codigo
                WHERE c.db21_ativo = 1 
                  and (c.db21_datalimite is null or  c.db21_datalimite > '$sDataSistema')
                  and u.id_usuario =  $iUsuarioLogado
                ORDER BY c.prefeitura DESC, c.codigo";

if ( $db_stdlib->db_getsession("DB_id_usuario") == "1" ||$db_stdlib->db_getsession('DB_administrador') == "1") {

$sSqlInstit =   "SELECT 
                        codigo 
                        ,nomeinst 
                        ,figura
                        ,db21_tipoinstit 
                    FROM 
                        db_config
                    WHERE 
                        (db21_datalimite is null or db21_datalimite <  '$sDataSistema') 
                    ORDER BY prefeitura DESC, codigo";

}     

$rsInstituicoes = $db_stdlib->db_query( $sSqlInstit );

?>
<div class="row">
    <?php 
        //  Se tiver somente uma instituição o sistema redireciona automaticamente para o area.
        if( $rsInstituicoes->rowCount() == 1 and !$tem_atualizacoes ) { 
            $Services_Funcoes->redireciona("area/".base64_encode("instit=".$rsInstituicoes->fetch()->codigo)."");
        } else {              
            foreach ( $rsInstituicoes as $linha ) {
                $sLogoImagem   =    $Services_Skins->getPathFile('img','TiposInstituicao') . '/' . $linha->figura;
                /**
                 * Quando não encontrar imagem configurada deve setar a imagem default
                 */
                if( !file_exists( $sLogoImagem ) ){
                    $sLogoImagem   =    $Services_Skins->getSkinLink() . 'img/TiposInstituicao/' . $linha->db21_tipoinstit . '.png';
                } 

                echo "
                        <div class=\"col-xl-3 col-md-6 col-sm-12\">
                            <a href=\"".$Services_Funcoes->url_acesso_in()."areas/".base64_encode("instit=".$linha->codigo )."\">
                                <div class=\"card text-white box-shadow-0 bg-info\">
                                    <div class=\"card-content collapse show\">
                                        <div class=\"card-body text-center\">
                                            <img src=\"".$sLogoImagem."\" alt=\"".$linha->nomeinst."\" onmouseover=\"js_msg_status(this.alt)\" onmouseout=\"js_lmp_status()\" border=\"0\" width=\"100\" height=\"100\">
                                            <p style='white-space: nowrap;' class=\"card-text\"><b>".$linha->nomeinst."</b></p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    ";
            }
        } ?>

</div>