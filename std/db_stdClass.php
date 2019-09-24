<?php
/**
 * db_stdClass
 * 
 * @author Maurício Costa
 * @author Vitor Rocha
 */

namespace std;

use libs\db_utils;

class db_stdClass extends db_stdlib {

   /**
    * Retorna os dados da instituicao. caso nao seje informado a instituição sera
    * retornado a instituicao da sessao
    *
    * @param integer $iInstit Código da instituicao;
    * @return object
    */
  static function getDadosInstit($iInstit = null) {

      if (empty($iInstit)) {
        $iInstit = parent::db_getsession("DB_instit");
      }

      $sSqlInstit  = "select *,z01_nome, z01_cgccpf";
      $sSqlInstit .= "  from db_config ";
      $sSqlInstit .= "  inner join cgm on z01_numcgm = numcgm";
      $sSqlInstit .= " where codigo = {$iInstit}";
      $rsInstit    = parent::db_query($sSqlInstit);
      return $rsInstit->fetch();
   }

   /**
    * Retorna os parametros configurados para a tabela de configuracao especificada.
    *
    * @param string $sClassParametro nome da classe de parametro
    * @param array $aKeys  parametros chaves da classe (metodo sql_query_file)
    * @param string $sFields lista de campos
    * @return object db_utils
    */
    static function getParametro($sClassParametro, $aKeys = null, $sFields = "*") {

     if (empty($sFields)) {
       $sFields = "*";
     }
     $oRetorno       = array();
     $oClass         = db_utils::getDao($sClassParametro);
     $oReflectMethod = new ReflectionMethod ("cl_{$sClassParametro}::sql_query_file");
     $i = 0;
     foreach ($oReflectMethod->getParameters() as $i => $param) {

       $svar   = $param->getName();
       if (!$param->isOptional() || isset($aKeys[$i])) {
         $aParam[] = $aKeys[$i];
       } else if ($param->getName() == "campos" ){
         $aParam[] = $sFields;
       } else {
         $aParam[] = null;
       }
       $i++;
     }
     $sRetornoSql  = @call_user_func_array(array(&$oClass,"sql_query_file"), $aParam);
     $rsRetornoSql = @call_user_func_array(array(&$oClass,"sql_record"), array($sRetornoSql));

     $iNumRows     = $oClass->numrows;
     $oRetorno     = db_utils::getCollectionByRecord($rsRetornoSql);
     //print_r($oRetorno);
     return $oRetorno;
   }


   /**
    * Retorna a data final sendo ele um dia util
    *
    * @param date    $dtDataIni
    * @param integer $iNroDias
    * @return date
    */
  static function getIntervaloDiasUteis($dtDataIni="",$iNroDias=0) {

     $iDias     = $iNroDias;
     $iSomaDia  = 1;
     $lFeriado  = true;
     $dtDataFim = $dtDataIni;

     $oCalend = db_utils::getDao('calend');

     while ( $lFeriado ) {
       $rsConsultaFeriado = $oCalend->sql_record($oCalend->sql_query_file(date('Y-m-d',$dtDataFim)));
       if ( $oCalend->numrows > 0 ) {
         $dtDataFim = strtotime("+1 day",$dtDataFim);
         ++$iDias;
       } else {
         if ($iSomaDia >= $iDias) {
           $lFeriado = false;
         } else {
           $dtDataFim  = strtotime("+1 day",$dtDataFim);
         }
      }
      ++$iSomaDia;
    }

   	return $dtDataFim;
  }

  /**
   * Troca algumas tags especiais pelo seu caractere correspondente
   *
   * @param string $sString string
   * @return string
   * @deprecated db_stripTagsJsonSemEscape
   * @see db_stripTagsJsonSemEscape
   */
  static function db_stripTagsJson($sString) {

    $aReferences = array(
                         "<arroba>",
                         "<quebralinha>",
                         "<aspa>",
                         "<aspasimples>",
                         "<interrogacao>",
                         "<percentual>",
                         "<abreparenteses>",
                         "<fechaparenteses>",
                         "<abrechaves>",
                         "<fechachaves>",
                         "<abrecolcheltes>",
                         "<fechacolchetes>",
                         "<mais>",
                         "<sustenido>",
                         "<ecomercial>",
                         "<barra>"
                        );
   $aMappTo      = array("@",
                         "\\n",
                         "\"",
                         "'",
                         "?",
                         "%",
                         "(",
                         ")",
                         "{",
                         "}",
                         "[",
                         "]",
                         "+",
                         "#",
                         "&",
                         "/"
                        );
   $sString = str_replace($aReferences, $aMappTo, $sString);
   return $sString;

  }

  /**
   * Cópia da db_stripTagsJson porém não escapa nenhum caracter
   *
   * @param string $sString string
   * @return string
   */
  static function db_stripTagsJsonSemEscape($sString) {

  	$aReferences = array("<arroba>",
  		                	 "<quebralinha>",
  		                	 "<aspa>",
  		                	 "<aspasimples>",
  		                	 "<interrogacao>",
  		                	 "<percentual>",
  		                	 "<abreparenteses>",
  		                	 "<fechaparenteses>",
  		                	 "<abrechaves>",
  		                	 "<fechachaves>",
  		                	 "<abrecolcheltes>",
  		                	 "<fechacolchetes>",
  		                	 "<mais>",
  		                	 "<sustenido>",
  		                	 "<ecomercial>",
  		                	 "<tab>",
  		                	 "<barra>",
  		                	 "<hifengrande>",
             	          );
  	$aMappTo = array("@",
  	             		 "\n",
  	             		 '"',
  	             		 "'",
  	             		 "?",
  	             		 "%",
  	             		 "(",
  	             		 ")",
  	             		 "{",
  	             		 "}",
  	             		 "[",
  	             		 "]",
  	             		 "+",
  	             		 "#",
  	             		 "&",
  	             		 "\t",
  	             		 "/",
  	             		 "-",
  	                );
  	$sString = str_replace($aReferences, $aMappTo, $sString);
  	return $sString;

  }

  /**
   * Gera pdf a partir de template (sxw) do agata.
   *
   * @param integer $tipoDoc Código do db_documentotemplatetipo
   *        integer $codDoc  Código do db_documentotemplate
   *        array   $aParam - array("<nome_parametro>"=>"<valor_parametro")
   *        String  $sCaminhoSalvoSxw
   *        String  $sNomeRelatorio
   *
   * @return void
   */
   static function oo2pdf($tipoDoc, $codDoc=null, $sAgt, $aParam, $sCaminhoSalvoSxw, $sNomeRelatorio) {
     $clagata = new cl_dbagata($sAgt);
     $api     = $clagata->api;
     $api->setOutputPath($sCaminhoSalvoSxw);

     foreach ($aParam as $sParN=>$sParVal) {
       $api->setParameter($sParN,$sParVal);
     }

     try {
       $oDocumentoTemplate = new documentoTemplate($tipoDoc,$codDoc);
     } catch (Exception $eException){
       $sErroMsg  = $eException->getMessage();
       db_redireciona("db_erros.php?fechar=true&db_erro={$sErroMsg}");
     }

     $lProcessado = $api->parseOpenOffice($oDocumentoTemplate->getArquivoTemplate());

     if ( $lProcessado ) {

       try {
         DocumentConverter::docToPdf($sCaminhoSalvoSxw, $sNomeRelatorio);
       } catch (Exception $e) {
         db_redireciona("db_erros.php?fechar=true&db_erro=" . $e->getMessage() );
       }
       db_redireciona($sNomeRelatorio);

     } else {
       db_redireciona("db_erros.php?fechar=true&db_erro=Falha ao gerar relatório !!!");
     }

   }

	/**
   * Executa comando para iniciar openoffice como serviço.
   *
   * @param String  $sCaminhoSalvoSxw
   *        String  $sNomeRelatorio
   *
   * @return void
   */
   static function ex_oo2pdf($sCaminhoSalvoSxw, $sNomeRelatorio) {
     try {
       DocumentConverter::docToPdf($sCaminhoSalvoSxw, $sNomeRelatorio);
     } catch (Exception $e) {
       db_redireciona("db_erros.php?fechar=true&db_erro=" . $e->getMessage() );
       return false;
     }
     return true;
   }

  /**
   * Função que gera um arquivo de imagem registrado no banco com um campo oid
   * @param integer $iOidImg - identificar do objeto no banco de dados
   * @param $Con - ponteiro que identifica conexão com o banco
   * @param $sDir - diretorio para onde deseja enviar o arquivo
   * @param $sNomeArquivo - caso seja necessario definir o nome do arquivo
   * @param $iniciaTransacao - a funções pg_lo_open e pg_lo_read necessitam estar dentro de uma transação do banco
   * Caso o script ja esteja dentro de uma, setar false.
   */
  static public function geraObjetoOid($iOid, $Con, $sTipo = "jpg", $sDir = "tmp/", $sNomeArquivo = null) {

    if(!db_utils::inTransaction()) {
      parent::db_query("begin");
    }

    if($rs = pg_lo_open($Con, $iOid, "r")){
      $dadosOid = pg_lo_read($rs, 999999);
    }else {
      return false;
    }

    if(!db_utils::inTransaction()) {
      parent::db_query("commit");
    }

    if($sNomeArquivo != null) {
      $arquivo = "$sDir/$sNomeArquivo.$sTipo";
    }else {
      $arquivo ="$sDir/imgOid.$sTipo";
    }

    $file = fopen($arquivo, "w+");

    fwrite($file, $dadosOid);
    fclose($file);

    return $arquivo;

  }

  /**
   * Retorna o Caminho do Menu passado por parametro()
   * @Param Mixed $mMenu Codigo , ou Nome do Arquivo
   */
  static public function getCaminhoMenu($mMenu) {

    $sBusca = "'{$mMenu}'";
    if (is_int($mMenu)) {
      $sBusca = "{$mMenu}";
    }
    $rsCaminhoMenu = parent::db_query("select fc_montamenu({$sBusca}) as caminho");
    $sCaminhoMenu  = db_utils::fieldsMemory($rsCaminhoMenu, 0)->caminho;
    return $sCaminhoMenu;
  }

  /**
   * Retorna o primeiro objeto que possui a propriedade indicada em $sPropertie, com o Valor de $sValue
   * @param string $sPropertie nome da Propriedade
   * @param string $sValue valor da Propriedade
   * @param array $sValue Collection que deve ser Realizada a busca
   *
   * @return mixed Indice da Colecao, ou false, no caso de nao existir o valor
   */
  static public function inCollection($sPropertie, $sValue, $aColection) {

    foreach ($aColection as $iIndex => $oObject) {
      if (isset($oObject->$sPropertie)) {
        if ($oObject->$sPropertie == $sValue) {
           return $iIndex;
        }
      }
    }
    return false;
  }

  /**
   *
   * Entra uma String com função javastring tagString
   * @param String $sString codificada
   * @return String decodificada
   * @deprecated normalizeStringJsonEscapeString
   * @see db_stdClass::normalizeStringJsonEscapeString
   */
  static public function normalizeStringJson($sString) {
    return urlDecode(\DBString::utf8_decode_all(db_stdclass::db_stripTagsJson($sString)));
  }


  /**
   * Entra uma String com função javastring tagString e com caractéres codificada
   * OBSERVAÇÂO Ao utilizar esta função deve ser retirado o pg_escape_string das variáveis.
   * Esta função retorna uma string tratada para inclusao no banco de dados
   *
   * @param String $sString codificada
   * @return String decodificada
   * @param string $sString
   */
  static public function normalizeStringJsonEscapeString($sString) {
    return pg_escape_string(urlDecode(\DBString::utf8_decode_all(db_stdclass::db_stripTagsJsonSemEscape($sString))));
  }

  /**
   * Método que valida se o PCASP esta ativado
   * @return boolean
   */
  static public function possuiPCASPAtivo() {

    $oDaoConParametro = db_utils::getDao('conparametro');
    $sSqlBuscaParametro = $oDaoConParametro->sql_query_file(null, "c90_usapcasp");
    $rsBuscaParametro   = $oDaoConParametro->sql_record($sSqlBuscaParametro);

    $lPCASP = db_utils::fieldsMemory($rsBuscaParametro, 0)->c90_usapcasp == "t" ? true : false;
    return $lPCASP;
  }

  /**
   * Decodifica uma string trocando os caracteres acentuados pelo underscore para uso em pesquisa.
   * Usado para decodificar strings envidas pelo autocomplete para pesquisa no banco
   * @param  string $sString
   * @return string
   */
  static public function crossUrlDecode($sString) {

    // Troco os caracteres especiais por pelo coringa
    $aOrig   = array('á', 'é', 'í', 'ó', 'ú', 'â', 'ê', 'ô', 'ã', 'õ', 'à', 'è', 'ì', 'ò', 'ù', 'ç',
                     'Á', 'É', 'Í', 'Ó', 'Ú', 'Â', 'Ê', 'Ô', 'Ã', 'Õ', 'À', 'È', 'Ì', 'Ò', 'Ù', 'Ç'
                    );

    return str_replace($aOrig, '_', mb_convert_encoding($sString, "ISO-8859-1", "UTF-8"));

  }
}
?>
