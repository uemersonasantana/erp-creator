<?php
/**
 * This file contains common functions used throughout the application.
 *
 * @package    libs
 * @author     Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

use libs\db_conecta;

use model\TraceLog;
use model\PreMenus;
use model\DBMensagem;

use std\DBMenu;

set_time_limit(0);

/**
 * db_stdlib
 */
class db_stdlib
{

public function __construct(){
  if (!isset($_SESSION)) {
    session_start();
  }
}

/***
 *
 * Funcao para montar uma string com o backtrace do PHP **SEM PARAMETROS***
 * nas chamadas de funções e métodos
 *
 */
static function db_debug_backtrace() {
  $aBacktrace = debug_backtrace();
  $iCount     = count($aBacktrace);
  $sBacktrace = "";

  for($i=1; $i<$iCount; $i++) {
    $sBacktrace .=
      sprintf(" * #%s %s(%s) called at [%s:%d]\n",
        $i,
        $aBacktrace[$i]["function"],
        ($aBacktrace[$i]["args"]?"...":""),
        $aBacktrace[$i]["file"],
        $aBacktrace[$i]["line"]);
  }

  if($sBacktrace <> "") {
    $sBacktrace = "\n/***\n * UAS backtrace\n{$sBacktrace} */\n";
  }

  return $sBacktrace;
}


/***
 *
 * Funcao wrapper para executar a pg_query (PostgreSQL)
 *
 */
static function db_query($param1, $param2=null, $param3="SQL"){

  $lMostraBackTrace = false;
  $sBackTrace = "";
  if ($lMostraBackTrace) { 
    $sBackTrace = self::db_debug_backtrace();
  }

  $lExecutaAccount = true;
  $lSessaoDesativarAccount = self::db_getsession("DB_desativar_account", false);

  if (isset($lSessaoDesativarAccount) && $lSessaoDesativarAccount === true) {
    $lExecutaAccount = false;
  }

  if($param2==null){

    $dbsql    = $sBackTrace . $param1;
    if ( !$lExecutaAccount) {

      $aWordsBlock = array(" db_acount ",
                           " db_acountkey "
      );

      foreach($aWordsBlock as $sWord) {

        $mAchouString = strpos($dbsql, $sWord);
        if ($mAchouString) {
          return true;
        }
      }
    }

    $dbresult = db_conecta::prepare($dbsql);
    $dbresult->execute();
    
    if (!$dbresult) {
      $dbresult = $dbresult->errorInfo();
    }
  }else{

    $dbsql    = $sBackTrace . $param2;
    if ($lExecutaAccount) {
      $dbresult = pg_query($param1, $dbsql);
    }
  }

  /*
   * Trecho comentado devido a um problema na execução do Duplos CGM executado via crontab.
   */
  if( self::db_getsession("DB_traceLog", false) != null ) {

    $oTraceLog = TraceLog::getInstance();
    $oTraceLog->makeMessage($dbsql,(!$dbresult?true:false));
  }

  if ( self::db_getsession("DB_premenus", false) != null  ) {

    $oPreMenus = PreMenus::getInstance();
    $oPreMenus->verificaInstrucaoSql($dbsql);
  }

  return $dbresult;
}

// Retorna a quantidade de dias do mês no ano informadoo
function db_dias_mes($ano,$mes,$ret_data = false){
  $data = getdate(mktime(0,0,0,$mes+1,0,$ano));
  if($ret_data == false){
    return $data["mday"];
  }else{
    return date('Y-m-d',mktime(0,0,0,$mes,$data["mday"],$ano));
  }
}

function db_diasemana($dia,$opcao="s"){
  // Opcao = S - Sigla
  //         E - Extenso
  $arr_SdiasUS = array(
    "Sun"=>"Dom",
    "Mon"=>"Seg",
    "Tue"=>"Ter",
    "Wed"=>"Qua",
    "Thu"=>"Qui",
    "Fri"=>"Sex",
    "Sat"=>"Sab"
  );
  $arr_EdiasUS = array(
    "Sun"=>"Domingo",
    "Mon"=>"Segunda",
    "Tue"=>"Terca",
    "Wed"=>"Quarta",
    "Thu"=>"Quinta",
    "Fri"=>"Sexta",
    "Sat"=>"Sabado"
  );
  if($opcao == "s"){
    return $arr_SdiasUS[date("D",mktime(0,0,0,db_subdata($dia,"m"),db_subdata($dia,"d"),db_subdata($dia,"a")))];
  }else{
    return $arr_EdiasUS[date("D",mktime(0,0,0,db_subdata($dia,"m"),db_subdata($dia,"d"),db_subdata($dia,"a")))];
  }
}
// Se opcao == "d" retorna o dia da data
//          == "m" retorna o mês da data
//          == "a" retorna o ano da data
// formatar == "b" default - Data no formato do banco YYYY-mm-dd
//          == "f" data formatada dd/mm/YYYY
//          == "t" timestamp
function db_subdata($data,$opcao,$formatar="b"){
  if($formatar == "b"){
    $data = db_formatar($data,"d");
  }else if($formatar == "t"){
    $data = date("d/m/Y",$data);
  }
  $arr_data = explode("/",$data);
  if(trim($arr_data[0]) == "" || !isset($arr_data[1]) || !isset($arr_data[2])){
    return 0;
  }
  if($opcao == "d"){
    return db_formatar($arr_data[0],"s","0",2,"e",0);
  }else if($opcao == "m"){
    return db_formatar($arr_data[1],"s","0",2,"e",0);
  }else if($opcao == "a"){
    return $arr_data[2];
  }
}

function db_hora($id_timestamp = 0, $formato = "H:i") {
  //#00#//db_hora
  //#10#// retorna a hora do servidor em no formato HH:MM
  //#15#//$hora = db_hora($timestamp,$formato);
  //#20#//$id_timestamp =        Data e hora no formato timestamp
  //#20#//$formato      = Formato do retorno da hora ou data
  //#20#//                Padrao: H:i - Hora e minuto com :.
  //#99#//Os tipos de formato de retorno são:
  //#99#//a        Meridiano da Hora no formato am ou pm
  //#99#//A        Meridiano da Hora no formato AM or PM
  //#99#//B        Hora na internet de 000 a 999
  //#99#//c        Formato ISO 8601 da data (PHP 5) 2004-02-12T15:19:21+00:00
  //#99#//d        Dia do Mes com dois digitos 01 to 31
  //#99#//D        3 primeiras letras do dia
  //#99#//F        Nome do mes
  //#99#//g        Hora no formato de 1 a 12
  //#99#//G        Hora no formato 24 horas ( 0 a 23 )
  //#99#//h        Hora no formato 12 com zeros 01 through 12
  //#99#//H        Hora no formato 24 horas 00 through 23
  //#99#//i        Minutos com zero a esquerda 00 to 59
  //#99#//j        Dia do mes sem zero a esquerda 1 to 31
  //#99#//l       Nome Dia da semana
  //#99#//m        Mes numericpo com dois digitos  01 a 12
  //#99#//M        3 primeiras letras do nome do mes Jan through Dec
  //#99#//n        Mes numerico sem zero a esquerda 1 a 12
  //#99#//O        Diferença para hora Greenwich (GMT) em horas        Example: +0200
  //#99#//r        Data no formato RFC 2822 Exemplo: Thu, 21 Dec 2000 16:01:07 +0200
  //#99#//s        Segundos com zeros a esquerda 00 through 59
  //#99#//S        Ordinal sufixo em Ingles do mes, 2 caracteres st, nd, rd or th.
  //#99#//t        Numero de dias do mes 28 a 31
  //#99#//T        Zona da hora setada na máquina        Exemplo: EST, MDT ...
  //#99#//U        Segundos em relação a 1/1/1970  timestamp.
  //#99#//w        Nnumero do dia da semana 0 a 6
  //#99#//W        Numero da semana do ano conforme ISO-8601
  //#99#//Y        Ano com 4 digitos Exemplo: 1999 or 2003
  //#99#//y        Ano em 2 digitos Exemplo: 99 or 03
  //#99#//z        Dia do ano da data 0 a 365
  //#99#//Z        Hora em segundos do timezona. -43200 a 43200
  if ($id_timestamp != 0) {
    return date($formato, $id_timestamp);
  } else {
    return date($formato);
  }
}
//  db_verifica_ip_anco
function db_verifica_ip_banco() {
  //#00#//db_verifica_ip_anco
  //#10#//Verifica se o IP que esta acessando poderá abrir o dbportal, pesquisando o arquivo db_acessa
  //#10#//e verificando as permissões
  //#15#//db_verifica_ip();
  //#40#//"1" para acesso permitido e "0" para não permitido
  //#99#//O arquido db_acessa possue a matriz com os IPs que poderão efetuar o acesso
  //#99#//Nome da matriz: db_acessa
  //#99#//db_acessa[1][1] = Máscara do IP que pode acessar, quando colocado um astesrisco(*) no final, o sistema
  //#99#//                  testa o tamanho do IP até o asterisco e desconsidera a partir dele.
  //#99#//db_acessa[1][2] = Campo Lógico, quando verdadeiro, poderá acessar, o IP ou máscara do IP e quando
  //#99#//                  falso nao poderá acessar o db_portal
  $db_ip = $_SERVER['REMOTE_ADDR'];

  $usuario_liberado = '0';
  
  //  Verifica pelo código do usuário.
  $sql = "SELECT 
                db47_id_usuario
                ,db48_ip
            FROM db_sysregrasacesso
                INNER  JOIN db_sysregrasacessousu  ON db46_idacesso = db47_idacesso
                INNER  JOIN db_sysregrasacessoip   ON db46_idacesso = db48_idacesso
                LEFT   JOIN db_sysregrasacessocanc ON db46_idacesso = db49_idacesso
            WHERE 
                db49_idacesso is null
                and db47_id_usuario   = ".self::db_getsession("DB_id_usuario")."
                and (( db46_dtinicio  < '".date('Y-m-d')."' and db46_datafinal > '".date('Y-m-d')."' )
                or  ( db46_dtinicio   = '".date('Y-m-d')."' and db46_horaini   <= '".date("G:i")."' )
                or  ( db46_datafinal  = '".date('Y-m-d')."' and db46_horafinal >= '".date("G:i")."' ))
                ";
  $result   = self::db_query($sql);

  foreach ( $result as $linha ) {
    if ( $linha->db48_ip == "" ) {
      $usuario_liberado = '1';
    }else{
      if ( $linha->db48_ip == $db_ip ) {
        $usuario_liberado = '1';
      }else{
        $aster = strpos("#".$linha->db48_ip, "*");
        if ($aster != 0) {
          $quantos = substr($linha->db48_ip, 0, $aster -1);
          if ( substr($linha->db48_ip, 0, strlen($quantos)) == substr($db_ip, 0, strlen($quantos)) ) {
            $usuario_liberado = '1';
          }
        }
      }
    }
  }

  // verifica somente ips liberados sem usuarios
  $sql = "SELECT 
                db48_ip
            FROM db_sysregrasacesso
                INNER JOIN db_sysregrasacessoip   on db46_idacesso = db48_idacesso
                LEFT JOIN db_sysregrasacessousu  on db46_idacesso = db47_idacesso
                LEFT  JOIN db_sysregrasacessocanc on db46_idacesso = db49_idacesso
            WHERE 
                db49_idacesso is null
                and db47_id_usuario is null
                and (( db46_dtinicio  < '".date('Y-m-d')."' and db46_datafinal > '".date('Y-m-d')."' )
                or  ( db46_dtinicio = '".date('Y-m-d')."' and db46_horaini   <= '".date("H:i")."' )
                or  ( db46_datafinal = '".date('Y-m-d')."' and db46_horafinal >= '".date("H:i")."' ))"
  ;
  $result = self::db_query($sql);
  
  foreach ( $result as $linha ) { 
    if ( $linha->db48_ip == $db_ip) {
      $usuario_liberado = '1';
    } else {
      $aster = strpos("#".$linha->db48_ip, "*");
      if ($aster != 0) {
        $quantos = substr($linha->db48_ip, 0, $aster -1);
        if (substr($linha->db48_ip, 0, strlen($quantos)) == substr($db_ip, 0, strlen($quantos))) {
          $usuario_liberado = '1';
        }
      }
    }
  }
  return $usuario_liberado;
}

function db_verifica_ip() {
  //#00#//db_verifica_ip
  //#10#//Verifica se o IP que esta acessando poderá abrir o dbportal, pesquisando o arquivo db_acessa
  //#10#//e verificando as permissões
  //#15#//db_verifica_ip();
  //#40#//"1" para acesso permitido e "0" para não permitido
  //#99#//O arquido db_acessa possue a matriz com os IPs que poderão efetuar o acesso
  //#99#//Nome da matriz: db_acessa
  //#99#//db_acessa[1][1] = Máscara do IP que pode acessar, quando colocado um astesrisco(*) no final, o sistema
  //#99#//                  testa o tamanho do IP até o asterisco e desconsidera a partir dele.
  //#99#//db_acessa[1][2] = Campo Lógico, quando verdadeiro, poderá acessar, o IP ou máscara do IP e quando
  //#99#//                  falso nao poderá acessar o db_portal
  global $SERVER, $HTTP_SERVER_VARS;
  if (isset ($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $db_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
  } else {
    $db_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
  }

  include("libs/db_acessa.php");

  for ($i = 1; $i -1 < sizeof($db_acessa); $i ++) {
    if ($db_acessa[$i][1] == $db_ip) {
      if ($db_acessa[$i][2] == false) {
        return '0';
      }
    }
  }
  $pode_acessar = "0";
  for ($i = 1; $i -1 < sizeof($db_acessa); $i ++) {
    $aster = strpos("#".$db_acessa[$i][1], "*");
    if ($aster != 0) {
      $quantos = substr($db_acessa[$i][1], 0, $aster -1);
      if (substr($db_acessa[$i][1], 0, strlen($quantos)) == substr($db_ip, 0, strlen($quantos))) {
        if ($db_acessa[$i][2] == false) {
          return '0';
        }
        $pode_acessar = "1";
      }
    }
  }
  return $pode_acessar;

}

function db_mes($xmes,$tipo=0) {
  //#00#//db_mes
  //#10#//Retorna o nome do mes por extenso
  //#15#//db_mes($mes);
  //#20#//mes : Número do mes 01,02,03,04,05,06,07,08,09,10,11,12 como string
  //#20#//      Número do mes 1,2,3,4,5,6,7,8,9,10,11,12 como numero
  //#20#//Tipo : 0 - minusculo  1 - MAIUSCULO  2 - Primeira Maiusculo
  //#40#//Nome do mes por extenso
  $Mes = "";
  if ($xmes == '01' || $xmes == 1) {
    $Mes = 'janeiro';
  } else
    if ($xmes == '02' || $xmes == 2) {
      $Mes = 'fevereiro';
    } else
      if ($xmes == '03 '|| $xmes == 3 ) {
        $Mes = 'março';
      } else
        if ($xmes == '04' ||$xmes == 4) {
          $Mes = 'abril';
        } else
          if ($xmes == '05' || $xmes == 5) {
            $Mes = 'maio';
          } else
            if ($xmes == '06' || $xmes == 6) {
              $Mes = 'junho';
            } else
              if ($xmes == '07' || $xmes == 7) {
                $Mes = 'julho';
              } else
                if ($xmes == '08' || $xmes == 8) {
                  $Mes = 'agosto';
                } else
                  if ($xmes == '09' || $xmes == 9) {
                    $Mes = 'setembro';
                  } else
                    if ($xmes == '10' || $xmes == 10) {
                      $Mes = 'outubro';
                    } else
                      if ($xmes == '11' || $xmes == 11) {
                        $Mes = 'novembro';
                      } else
                        if ($xmes == '12' || $xmes == 12) {
                          $Mes = 'dezembro';
                        }
  if($tipo==0){
    return $Mes;
  }elseif ($tipo==1){
    return strtoupper(str_replace("ç","Ç",$Mes));
  }else{
    return ucfirst($Mes);
  }
}

function db_geratexto($texto) {
  $texto .= "#";
  $txt = explode("#", $texto);
  $texto1 = '';
  for ($x = 0; $x < sizeof($txt); $x ++) {
    if (substr($txt[$x], 0, 1) == "$") {
      $txt1 = substr($txt[$x], 1);
      global $$txt1;
      $texto1 .= $$txt1;
    } else
      if ((substr($txt[$x], 0, 2) == '\n')or(substr($txt[$x], 0, 4) == '<br>')) {
        $texto1 .= "\n";
      } else
        if (substr($txt[$x], 0, 2) == '\t') {
          $texto1 .= "\t";
        } else {
          $texto1 .= $txt[$x];
        }
  }
  return $texto1;
}

// Verifica se esta sendo passado algum comando SQL
function db_verfPostGet($post) {

  //db_postmemory($GLOBALS["HTTP_POST_VARS"],2);

  $tam_vetor = sizeof($post);
  reset($post);
  for ($i = 0; $i < $tam_vetor; $i ++) {

    if (key($post) != 'triggerfuncao' && key($post) != 'corpofuncao' && key($post) != 'eventotrigger' && key($post) != 'codigoclass' && key($post) != 'db33_obs' && key($post) != 'db33_obscpd' && key($post) != 'descricao' && key($post) != 'descr') {
      $dbarraypost = (gettype($post[key($post)]) != "array" ? $post[key($post)] : "");
    } else {
      $dbarraypost = "";
    }
    if (findword(strtoupper($dbarraypost), "INSERT") || findword(strtoupper($dbarraypost), "UPDATE") || findword(strtoupper($dbarraypost), "DELETE") || db_indexOf(strtoupper($dbarraypost), "EXEC(") > 0 || db_indexOf(strtoupper($dbarraypost), "SYSTEM(") > 0 || db_indexOf(strtoupper($dbarraypost), "<SCRIPT>") > 0 || db_indexOf(strtoupper($dbarraypost), "PASSTHRU(") > 0) {
      if(defined("TAREFA")==false) {
        echo "<script>alert('Voce está passando parametros inválidos e sera redirecionado. Verifique INSERT/UPDATE e ... nos campos enviados.');(window.CurrentWindow || parent.CurrentWindow).corpo.location.href='instit.php'</script>\n";
        exit;
      }
    }
    //$post[key($post)] = htmlspecialchars(gettype($post[key($post)])!="array"?$post[key($post)]:"");
    next($post);
  }
}

//  Esta funcao mostra os dados de um record set na tela, em uma tabela.
function db_criatabela($result, $columns=array()) {
  //#00#//db_criatabela
  //#10#//Esta funcao mostra os dados de um record set na tela, em uma tabela
  //#15#//db_criatabela($result);
  //#20#//result  : Record set gerado
  //#20#//columns : Array com nomes das colunas a exibir, se nao passar nada mostra todas colunas do recordset

  $numrows = $result->rowCount();
  if ( count($columns) == 0 ) {
    $numcols  = $result->columnCount();
    $bycolumn = false;
  } else {
    $numcols  = count($columns);
    $bycolumn = true;
  }
  echo "<br><br><table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<tr bgcolor=\"#00CCFF\">\n";

  $nomesColuna  = array();
  $linha        = $result->fetchAll();  

  for ( $j = 0; $j < $numcols; $j++ ) { 
    if(!$bycolumn) {
      echo "<td>".$result->getColumnMeta($j)['name']."</td>\n";
      
      $nomesColuna[$j] = $result->getColumnMeta($j)['name'];
    } else {
      echo "<td>".$columns[$j]."</td>\n";
    }
  }

  $cor = "#07F89D";
  echo "</tr>\n";

  for ( $i = 0; $i < $numrows; $i++ ) {
    
    echo "<tr bgcolor=\"". ($cor = ($cor == "#07F89D" ? "#51F50A" : "#07F89D"))."\">\n";
    for ( $j = 0; $j < $numcols; $j++ ) {
      if( !$bycolumn ) {
        echo "<td nowrap>".$linha[$i]->$nomesColuna[$j]."</td>\n";
      } else {
        echo "<td nowrap>".$linha[$i]->$columns[$j]."</td>\n";
      }
    }
    echo "</tr>\n";
  }

  echo "</table><br><br>";


}

//retorna o tamanho do maior registro
function db_getMaxSizeField($recordset, $campo = 0) {
  //#00#//db_getMaxSizeField
  //#10#//Esta funcao retorna o maior valor do size de um determinado campo de um record set
  //#15#//db_getMaxSizeField($recordset,$campo = 0);
  //#20#//recordset : Record que será pesquisado
  //#20#//campo     : Número do campo do record set que será pesquisado

  $numrows = pg_numrows($recordset);
  $val = strlen(trim(pg_result($recordset, 0, $campo)));
  for ($i = 1; $i < $numrows; $i ++) {
    $field = strlen(trim(pg_result($recordset, $i, $campo)));
    if ($val < $field)
      $val = $field;
  }
  return (int) $val;
}
//  Pega um vetor e cria variáveis globais pelo índice do vetor.
//atualiza a classe dos arquivos
function db_postmemory($vetor, $verNomeIndices = 0) {
  //#00#//db_postmemory
  //#10#//Esta funcao cria as variáveis que são passadas por POST no array $HTTP_POST_VARS do apache
  //#15#//db_postmemory($vetor,$verNomeIndices = 0);
  //#20#//vetor         : Array que será pesquisado
  //#20#//verNomeIndice : 1 - para gerar as variáveis
  //#20#//                2 - para gerar as variáveis e mostrar no formulário com o nome e conteúdo
  if (!is_array($vetor)) {
    echo "Erro na função postmemory: Parametro não é um array válido.<Br>\n";
    return false;
  }
  $tam_vetor = sizeof($vetor);
  reset($vetor);
  if ($verNomeIndices > 0)
    echo "<br><br>\n";
  for ($i = 0; $i < $tam_vetor; $i ++) {
    $matriz[$i] = key($vetor);
    $GLOBALS[$matriz[$i]] = $vetor[$matriz[$i]];

    if ($verNomeIndices == 1)
      echo "$".$matriz[$i]."<br>\n";
    else
      if ($verNomeIndices == 2)
        echo "$".$matriz[$i]." = '".$$matriz[$i]."';<br>\n";
    next($vetor);
  }
  if ($verNomeIndices > 0)
    echo "<br><br>\n";
}
function db_numpre_sp($qn, $qnp = "x", $qnt = "x", $qnd = "x") {
  //#00#//db_numpre_sp
  //#10#//Esta funcao coloca a mascara no numpre SEM os pontos entre os número
  //#15#//db_numpre_sp($qn,$qnp="x",$qnt="x",$qnd="x");
  //#20#//qn  : Número do numpre, normalmento k00_numpre
  //#20#//qnp : Número da parcela do numpre
  //#20#//qnt : Número da quantidade de parcelas do numpre
  //#20#//qnd : Dígito verificador do numpre
  //#40#//Código de arrecadação formatado SEM os pontos
  //#99#//Exemplo:
  //#99#//db_numpre_sp(123456,1,12,0); // numpre 123456 - parcela 1 - total de parcelas 12 - digito 0
  //#99#//Retorno será : 001234560010120
  //#99#//
  //#99#//Para formatar os números o sistema utiliza a função |db_formatar|
  $retorno = db_formatar($qn, 's', "0", 8, "e");
  if ($qnp != "x") {
    // $retorno .= ".000";
    $retorno .= db_formatar($qnp, 's', "0", 3, "e");
  }
  if ($qnt != "x") {
    $retorno .= db_formatar($qnt, 's', "0", 3, "e");
  }
  if ($qnd != "x") {
    $retorno .= db_formatar($qnd, 's', "0", 1, "e");
  }
  return $retorno;
}

function db_numpre($qn, $qnp = "x", $qnt = "x", $qnd = "x") {
  //#00#//db_numpre
  //#10#//Esta funcao coloca a mascara no numpre COM os pontos entre os número
  //#15#//db_numpre_sp($qn,$qnp="x",$qnt="x",$qnd="x");
  //#20#//qn  : Número do numpre, normalmento k00_numpre
  //#20#//qnp : Número da parcela do numpre
  //#20#//qnt : Número da quantidade de parcelas do numpre
  //#20#//qnd : Dígito verificador do numpre
  //#40#//Código de arrecadação formatado COM os pontos
  //#99#//Exemplo:
  //#99#//db_numpre_sp(123456,1,12,0); // numpre 123456 - parcela 1 - total de parcelas 12 - digito 0
  //#99#//Retorno será : 00123456.001.012.0
  //#99#//
  //#99#//Para formatar os números o sistema utiliza a função |db_formatar|
  $retorno = db_formatar($qn, 's', "0", 8, "e");
  if ($qnp != "x") {
    // $retorno .= ".000";
    $retorno .= ".".db_formatar($qnp, 's', "0", 3, "e");
  }
  if ($qnt != "x") {
    $retorno .= ".".db_formatar($qnt, 's', "0", 3, "e");
  }
  if ($qnd != "x") {
    $retorno .= ".".db_formatar($qnd, 's', "0", 1, "e");
  }
  return $retorno;
}
function db_translate($db_transforma = null,$expresAdicional = "",$stringAdicional = ""){

  // Array com expressões regulares
  $arr_regexp = Array(
    "/º/",
    "/ç/",
    "/Ç/",
    "/á|à|ã|â|ä/",
    "/Á|À|Ã|Â|Ä/",
    "/é|è|ê|ë/",
    "/É|È|Ê|Ë|&/",
    "/í|ì|î|ï/",
    "/Í|Ì|Î|Ï/",
    "/ó|ò|õ|ô|ö/",
    "/Ó|Ò|Õ|Ô|Ö/",
    "/ú|ù|û|ü/",
    "/Ú|Ù|Û|Ü/",
    "/'|;|:/",
    "/$expresAdicional/"
  );
  // Array com substitutos
  $arr_replac = Array("o","c","C","a","A","e","E","i","I","o","O","u","U"," ","$stringAdicional");

  // $arr_regexp[0] substituído por $arr_replac[0], ou seja, ç por c
  // $arr_regexp[1] substituído por $arr_replac[1], ou seja, Ç por C
  // $arr_regexp[2] substituído por $arr_replac[2], ou seja, á ou à ou ã ou â ou ä por a
  // $arr_regexp[3] substituído por $arr_replac[3], ou seja, Á ou À ou Ã ou Â ou Ä por A
  // $arr_regexp[n] substituído por $arr_replac[n]
  // ...
  $db_transforma = preg_replace($arr_regexp,$arr_replac,$db_transforma);

  return $db_transforma;

}

// retorna uma string formatada, retorna false se alguma opção estiver errada
// $tipo pode ser:
// "b" formata boolean s / n
// "f" formata a string pra float
// "d" formata a string pra data
// "v" tira a formatação
// "cpf" formata cpf
// "cnpj" formata cnpj
// "s"  Preenche uma string para um certo tamanho com outra string
// se for "s":
//   $caracter             caracter ou espaço pra acrecentar a esquerda, direita ou meio
//   $quantidade           tamanho que ficará a string com os espaços ou caracteres
//   $TipoDePreenchimento  informa se vai aplicar a string a:
//                         esquerda       "e"
//                         direita        "d"
//                         ambos os lados "a"

function db_formatar($str, $tipo, $caracter = " ", $quantidade = 0, $TipoDePreenchimento = "e", $casasdecimais = 2) {
  //#00#//db_formatar
  //#10#//Esta funcao coloca a mascara no numpre SEM os pontos entre os número
  //#15#//db_formatar($str,$tipo,$caracter=" ",$quantidade=0,$TipoDePreenchimento="e",$casasdecimais=2) {
  //#20#//Str                   : String que será formatada
  //#20#//Tipo                  : Tipo de formatação que será executada
  //#20#//                        cpf  =  Formata para CPF
  //#20#//                        cnpj =  Formata para CNPJ
  //#20#//                        b    =  Formata falso ou verdadeiro (S = Verdadeiro N = Falso )
  //#20#//                        p    =  Formata ponto flutuante, com PONTO na casa decimal Ex: 1000.55
  //#20#//                        f    =  Formata ponto flutuante, com VIRGULA na casa decimal Ex: 1000,55
  //#20#//                        d    =  Formata data
  //#20#//                        s    =  Formata uma string alinhando conforme Tipo de Preenchimento
  //#20#//                        v    =  Variavel, ou seja, imprime quantas casas decimais o valor tiver, combustivel por exemplo, valor de 1,359
  //#20#//Caracter              : Caracter que será colocado para formatar
  //#20#//Quantidade            : Tamanho da string que será gerada
  //#20#//Tipo de Preenchimento : Se preenche a esquerda, direito ou centro
  //#20#//                        e = Esquerda   d = Direita  a = Centro
  //#20#//Casas Decimais        : Número de casas decimais, para valores flutuantes, que será gerada
  //#40#//String formatada conforme os parâmetros
  //#99#//Exemplo:
  //#99#//db_formatar(100.55,'f','0',15,'e',2)
  //#99#//Retorno será : 000000000100,55
  //#99#//db_formatar(100.55,'f') // formatação padrão de números
  //#99#//Retorno será : "         100,55"
  //#99#//
  //#99#//db_formatar(100.55,'p','0',15,'e',2)
  //#99#//Retorno será : 000000000100.55

  switch ($tipo) {
    case "sistema" :
      return substr($str, 0, 1).".".substr($str, 1, 1).".".substr($str, 2, 1).".".substr($str, 3, 1).".".substr($str, 4, 1).".".substr($str, 5, 2).".".substr($str, 7, 2).".".substr($str, 9, 2).".".substr($str, 11, 2);
    case "receita" :
      return substr($str, 0, 1).".".substr($str, 1, 1).".".substr($str, 2, 1).".".substr($str, 3, 1).".".substr($str, 4, 1).".".substr($str, 5, 2).".".substr($str, 7, 2).".".substr($str, 9, 2).".".substr($str, 11, 2).".".substr($str, 13, 2);
    case "receita_int" :
      return substr($str, 0, 1).".".substr($str, 1, 1).".".substr($str, 2, 1).".".substr($str, 3, 1).".".substr($str, 4, 1).".".substr($str, 5, 2).".".substr($str, 7, 2).".".substr($str, 9, 2).".".substr($str, 11, 2).".".substr($str, 13, 2);
    case "orgao" :
      return str_pad($str, 2, "0", STR_PAD_LEFT);
    case "unidade" :
      return str_pad($str, 2, "0", STR_PAD_LEFT);
    case "funcao" :
      return str_pad($str, 2, "0", STR_PAD_LEFT);
    case "subfuncao" :
      return str_pad($str, 3, "0", STR_PAD_LEFT);
    case "programa" :
      return str_pad($str, 4, "0", STR_PAD_LEFT);
    case "projativ" :
      return str_pad($str, 4, "0", STR_PAD_LEFT);
    case "elemento_int" :
      return substr($str, 0, 1).".".substr($str, 1, 1).".".substr($str, 2, 1).".".substr($str, 3, 1).".".substr($str, 4, 1).".".substr($str, 5, 2).".".substr($str, 7, 2).".".substr($str, 9, 2).".".substr($str, 11, 2);
    case "elemento" :
      return substr($str, 1, 1).".".substr($str, 2, 1).".".substr($str, 3, 1).".".substr($str, 4, 1).".".substr($str, 5, 2).".".substr($str, 7, 2).".".substr($str, 9, 2).".".substr($str, 11, 2);
    case "recurso" :
      return str_pad($str, 4, "0", STR_PAD_LEFT);
    case "atividade" :
      return str_pad($str, 4, "0", STR_PAD_LEFT);
    case "cpf" :
      return substr($str, 0, 3).".".substr($str, 3, 3).".".substr($str, 6, 3)."/".substr($str, 9, 2);
    case "CPF" :
      return substr($str, 0, 3).".".substr($str, 3, 3).".".substr($str, 6, 3)."-".substr($str, 9, 2);
    case "cep" :
      return substr($str, 0, 2).".".substr($str, 2, 3)."-".substr($str, 5, 3);
    case "cnpj" :
      return substr($str, 0, 2).".".substr($str, 2, 3).".".substr($str, 5, 3)."/".substr($str, 8, 4)."-".substr($str, 12, 2);
    //90.832.619/0001-55
    case "b" :
      // boolean
      if ($str == false) {
        return 'N';
      } else {
        return 'S';
      }
    case "p" :
      // ponto decimal com "."
      /*
      if (strpos($str,".") != 0) {
      if (strpos($str,",") == 0) {
      $casasdecimais = strlen($str) - strpos($str,".") - 1;
      if ($casasdecimais < 2) {
      $casasdecimais = 2;
      }
      }
      }
      */

      $str = $str == null ? 0 :$str;
      if ($quantidade == 0) {
        return str_pad(number_format($str, $casasdecimais, ".", ""), 15, "$caracter", STR_PAD_LEFT);
      } else {
        return str_pad(number_format($str, $casasdecimais, ".", ""), $quantidade, "$caracter", STR_PAD_LEFT);
      }
    case "v" :
      // ponto decimal com virgula
      if (strpos($str, ".") != 0) {
        if (strpos($str, ",") == 0) {
          $casasdecimais = strlen($str) - strpos($str, ".") - 1;
          if ($casasdecimais < 2) {
            $casasdecimais = 2;
          }
        }
      }
      if ($quantidade == 0)
        if ($str == 0)
          return "   ".str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
        else
          return str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
      else {
        //        return str_pad(number_format($str,$casasdecimais,",","."),$quantidade,"$caracter",STR_PAD_LEFT);
        $vlrreturn = str_pad(number_format($str, $casasdecimais, ",", "."), $quantidade +1, "$caracter", STR_PAD_LEFT);
        $posponto = strpos($vlrreturn, ",");
        return substr($vlrreturn, 0, $posponto + $quantidade +1);
      }
    case "vdec" :
      // ponto decimal sem virgula
      if (strpos($str, ".") != 0) {
        if (strpos($str, ",") == 0) {
          $casasdecimais = strlen($str) - strpos($str, ".") - 1;
          if ($casasdecimais < 2) {
            $casasdecimais = 2;
          }
        }
      }
      if ($quantidade == 0)
        if ($str == 0)
          return "   ".str_pad(number_format($str, $casasdecimais, ".", ""), 15, "$caracter", STR_PAD_LEFT);
        else
          return str_pad(number_format($str, $casasdecimais, ".", ""), 15, "$caracter", STR_PAD_LEFT);
      else {
        $vlrreturn = str_pad(number_format($str, $casasdecimais, ".", ""), $quantidade +1, "$caracter", STR_PAD_LEFT);
        $posponto = strpos($vlrreturn, ".");
        return substr($vlrreturn, 0, $posponto + $quantidade +1);
      }
    case "valsemform" :

      if ($quantidade == 0)
        if ($str == 0)
          $valretornar = "   ".str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
        else
          $valretornar = str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
      else
        $valretornar = str_pad(number_format($str, $casasdecimais, ",", "."), $quantidade, "$caracter", STR_PAD_LEFT);

      $valretornar = str_replace(",","",$valretornar);
      $valretornar = str_replace(".","",$valretornar);
      return str_pad($valretornar,$quantidade," ",STR_PAD_LEFT);

    case "f" :
      // ponto decimal com virgula
      /*
      if (strpos($str,".") != 0) {
      if (strpos($str,",") == 0) {
      $casasdecimais = strlen($str) - strpos($str,".") - 1;
      if ($casasdecimais < 2) {
      $casasdecimais = 2;
      }
      }
      }
      */
      $str = $str == null ? 0 :$str;
      if ($quantidade == 0)
        if ($str == 0)
          return "   ".str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
        else
          return str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
      else
        return str_pad(number_format($str, $casasdecimais, ",", "."), $quantidade, "$caracter", STR_PAD_LEFT);
    case "fff" :
      // ponto decimal com virgula

      if ($quantidade == 0)
        if ($str == 0)
          return "   ".str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
        else
          return str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
      else
        return str_pad(number_format($str, $casasdecimais, ",", "."), $quantidade, "$caracter", STR_PAD_LEFT);
    case "d" :

      if ($str != "") {
        $data = explode("-", str_replace("/","-",$str));
        return $data[2]."/".$data[1]."/".$data[0];
      } else {
        return $str;
      }
    case "s" :
      if ($TipoDePreenchimento == "e") {
        return str_pad($str, $quantidade, $caracter, STR_PAD_LEFT);
      } else
        if ($TipoDePreenchimento == "d") {
          return str_pad($str, $quantidade, $caracter, STR_PAD_RIGHT);
        } else
          if ($TipoDePreenchimento == "a") {
            return str_pad($str, $quantidade, $caracter, STR_PAD_BOTH);
          }
    case "xxxv" : // antigo "v"
      if (strpos($str, ",") != "") {
        $str = str_replace(".", "", $str);
        $str = str_replace(",", ".", $str);
        return $str;
      } else
        if (strpos($str, "-") != "") {
          $str = explode("-", $str);
          return $str[2]."-".$str[1]."-".$str[0];
        } else
          if (strpos($str, "/") != "") {
            $str = explode("/", $str);
            return $str[2]."-".$str[1]."-".$str[0];
          }
      break;
  }
  return false;
}

//Cria variaveis globais para a instituição passada
//Se instituição não for passada, buscará dados da instituição do db_getsession
//Retorna false se tiver problemas na execução do sql e numrows caso sql esteja correto (0 se não encontrar instituição e 1 caso encontre)
function db_sel_instit($instit=null,$campos=" * "){
  if($instit == null || trim($instit) == ""){
    $instit = db_getsession("DB_instit");
  }
  if(trim($campos) == ""){
    $campos = " * ";
  }
  $record_config = db_query("select ".$campos."
                            from db_config
                                 left join db_tipoinstit on db21_codtipo = db21_tipoinstit
                            where codigo = ".$instit);
  if($record_config == false){
    return false;
  }else{
    $num_rows = pg_numrows($record_config);
    if($num_rows > 0){
      $num_cols = pg_numfields($record_config);
      for($index=0; $index<$num_cols; $index++){
        $nam_campo = pg_fieldname($record_config, $index);
        global $$nam_campo;
        $$nam_campo = pg_result($record_config, 0, $nam_campo);
      }
    }
  }
  return $num_rows;
}

//Cria variaveis globais para usuário passado
//Se o usuário não for passado, buscará dados do usuário do db_getsession
//Retorna false se tiver problemas na execução do sql e numrows caso sql esteja correto (0 se não encontrar o usuário e 1 caso encontre)
function db_sel_usuario($usuario=null,$campos=" * "){
  if($usuario == null || trim($usuario) == ""){
    $usuario = db_getsession("DB_id_usuario");
  }
  if(trim($campos) == ""){
    $campos = " * ";
  }
  $record_usuarios = db_query("select ".$campos."
                            from db_usuarios
                            where id_usuario = ".$usuario);
  if($record_usuarios == false){
    return false;
  }else{
    $num_rows = pg_numrows($record_usuarios);
    if($num_rows > 0){
      $num_cols = pg_numfields($record_usuarios);
      for($index=0; $index<$num_cols; $index++){
        $nam_campo = pg_fieldname($record_usuarios, $index);
        global $$nam_campo;
        $$nam_campo = pg_result($record_usuarios, 0, $nam_campo);
      }
    }
  }
  return $num_rows;
}

//  Cria variáveis globais de todos os campos do recordset no indice $indice
function db_fieldsmemory($recordset, $indice, $formatar = "", $mostravar = false) {
  //#00#//db_fieldsmemory
  //#10#//Esta funcao cria as variáveis de uma determinada linha de um record set, sendo o nome da variável
  //#10#//o nome do campo no record set e seu conteúdo o conteúdo da variável
  //#15#//db_fieldsmemory($recordset,$indice,$formatar="",$mostravar=false);
  //#20#//Record Set        : Record set que será pesquisado
  //#20#//Indice            : Número da linha (índice) que será caregada as funções
  //#20#//Formatar          : Se formata as variáveis conforme o tipo no banco de dados
  //#20#//                    true = Formatar      false = Não Formatar (Padrão = false)
  //#20#//Mostrar Variáveis : Mostrar na tela as variáveis que estão sendo geradas
  //#99#//Esta função é bastante utilizada quando se faz um for para percorrer um record set.
  //#99#//Exemplo:
  //#99#//db_fieldsmemory($result,0);
  //#99#//Cria todas as variáveis com o conteúdo de cada uma sendo o valor do campo
  $fm_numfields = $recordset->columnCount();
  $fm_numrows   = $recordset->rowCount();
  //if(pg_numrows($recordset)==0){
  // echo "RecordSet Vazio: <br>";
  // for ($i = 0;$i < $fm_numfields;$i++){
  //    echo pg_fieldname($recordset,$i)."<br>";
  // }
  // exit;
  // }
  $result = $recordset->fetchAll();

  for ($i = 0; $i < $fm_numfields; $i ++) {
    $nomeCampo  = $recordset->getColumnMeta($i)['name'];

    $aux = $result[$indice]->$nomeCampo;

    if ( ($formatar != '') ) {
      switch ( $recordset->getColumnMeta($i)['native_type'] ) {
        case "float8" :
        case "float4" :
        case "float" :

          if (empty($aux) ){
            $aux = 0;
          }
          $valor = number_format($aux, 2, ".", "");
          if ($mostravar == true)
            echo $nomeCampo ."->".$valor."<br>";
          break;
        case "date" :
          if ($aux != "") {
            $data = explode("-", $aux);
            $valor = $data[2]."/".$data[1]."/".$data[0];
          } else {
            $valor = "";
          }
          if ($mostravar == true)
            echo $nomeCampo."->".$valor."<br>";
          break;
        default :
          $valor = stripslashes($aux);
          if ($mostravar == true)
            echo $nomeCampo."->".$valor."<br>";
          break;
      }
    } else {
      switch ( $recordset->getColumnMeta($i)['native_type'] ) {
        case "date" :
          $datav = explode("-", $aux);
          
          $GLOBALS[ $nomeCampo."_dia" ]  =  @$datav[2];
          if ($mostravar == true)
            echo $nomeCampo."->".@$datav[2]."<br";
          
          $GLOBALS[ $nomeCampo."_mes" ]  =  @$datav[1];
          if ($mostravar == true)
            echo $nomeCampo."->".@$datav[1]."<br>";

          $GLOBALS[ $nomeCampo."_ano" ]  =  @$datav[0];
          if ($mostravar == true)
            echo $nomeCampo."->".@$datav[0]."<br>";
          
          $GLOBALS[ $nomeCampo ]  = $aux;
          if ($mostravar == true)
            echo $nomeCampo."->".$aux."<br>";
          
          break;
        case "bool" :
          $GLOBALS[ $nomeCampo ]  = (int)$aux;
          if ($mostravar == true)
            echo $nomeCampo."->".(int)$aux."<br>";

          break;
        default :
          $GLOBALS[ $nomeCampo ]  = stripslashes($aux);
          
          if ($mostravar == true)
            echo $nomeCampo."[".$recordset->getColumnMeta($i)['native_type']."]->".stripslashes($aux)."<br>";
          break;
      }
    }

  }

}

//  Formata uma string pra cgc ou cpf
function db_cgccpf($str) {
  if (strlen($str) == 14)
    return substr($str, 0, 2).".".substr($str, 2, 3).".".substr($str, 5, 3)."/".substr($str, 8, 4)."-".substr($str, 12, 2);
  else
    if (strlen($str) == 11)
      return substr($str, 0, 3).".".substr($str, 3, 3).".".substr($str, 6, 3)."-".substr($str, 9, 2);
    else
      return $str;
}

function verifica_data($dia, $mes, $ano) {
  //#00#//db_verifica_data
  //#10#//Esta funcao verifica se uma data é válida ou não
  //#15#//db_verifica_data($dia,$mes,$ano);
  //#20#//Dia : Dia que será testado na data
  //#20#//Mes : Mês que será testado na data
  //#20#//Ano : Ano que será testado na data
  //#40#//Data correta
  //#99#//Caso não exista a data que foi enviada como parâmetro o sistema soma o dia, mes e ano até encontrar uma data

  while ((checkdate($mes, $dia, $ano) == false) or ((date("w", mktime(0, 0, 0, $mes, $dia, $ano)) == "0") or (date("w", mktime(0, 0, 0, $mes, $dia, $ano)) == "6"))) {
    if ($dia > 31) {
      $dia = 1;
      $mes ++;
      if ($mes > 12) {
        $mes = 1;
        $ano ++;
      }
    } else {
      $dia ++;
    }
  }
  return $ano."-".$mes."-".$dia;
}

function db_vencimento($dt = "") {
  //#00#//db_vencimento
  //#10#//Esta funcao coloca a data no formato ano-mes-dia para o postgres
  //#15#//db_vencimento($dt);
  //#20#//Dt : Data a ser convertida para o formato
  //#20#//     Caso a data em branco ou não informada, o sistema carrega a data da função |self::db_getsession|
  //#40#//Data formatada para postgres

  if (empty ($dt))
    $dt = self::db_getsession("DB_datausu");
  $data = date("Y-m-d", $dt);
  /*
if ( (date("H",$dt) >= "16" ) ) {
$data = verifica_data(date("d",$dt)+1,date("m",$dt),date("Y",$dt));
} else {
if ( ( date("w",mktime(0,0,0,date("m",$dt),date("d",$dt),date("Y",$dt))) == "0" ) or ( date("w",mktime(0,0,0,date("m",$dt),date("d",$dt),date("Y",$dt))) == "6" )  ) {
$data = verifica_data(date("d",$dt)+1,date("m",$dt),date("Y",$dt));
}
}
*/
  return $data;
}

/**
 * mostra uma mensagem na tela
 *
 * @param string $sMensagem - Mensagem a ser colocada no alert
 * @access public
 * @return void
 */
static function db_msgbox($sMensagem) {

  $sMensagem = str_replace("\n", '\n', $sMensagem);

  echo "<script>alert('".$sMensagem."');</script>\n";
}

//redireciona para uma url
function db_redireciona($url = "0") {
  //#00#//db_redireciona
  //#10#//Esta funcao executa um redrecionamento de página utilizando o javascript
  //#15#//db_redireciona($url="0")
  //#20#//Url : Nome completo da página a ser acessada pelo redirecionamento
  //#99#//Exemplo:
  //#99#//db_redireciona("index.php");
  //#99#//Irá abrir a página index.php
  if ($url == "0")
    $url = $_SERVER["PHP_SELF"];
  echo "<script>location.href='$url'</script>\n";
  exit;
}

//retorna uma variável de sessão
/*
 function self::db_getsession($var) {
 global $HTTP_SESSION_VARS;
 if(!class_exists("crypt_hcemd5"))
 include(modification("db_calcula.php"));
 $rand = 195728462;
 $key = "alapuchatche";
 $md = new Crypt_HCEMD5($key, $rand);
 return $md->decrypt($HTTP_SESSION_VARS[$var]);
 }
 */

//retorna uma variável de sessão
static function db_getsession($var = "0", $alertarExistencia = true) {
  //#00#//self::db_getsession
  //#10#//Esta funcao recupera da sessão do php uma determinada variável, ou todas as variáveis lá registradas
  //#15#//self::db_getsession($var="0", $alertarExistencia = true)
  //#20#//Var : Nome da variável que será recuperada
  //#20#//alertarExistencia : Se deseja alertar que váriavel de sessão não existe (conforme o caso).
  //#99#//Variaveis disponíveis na sessão
  //#99#//DB_acessado     Utilizado para Log
  //#99#//DB_login        Login do usuário
  //#99#//DB_id_usuario   Númedo do id do usuário na taela |db_usuarios|
  //#99#//DB_ip           Número do IP que esta acessando
  //#99#//DB_uol_hora     Hora de acesso do usuário
  //#99#//DB_SELLER       Variavel de controle
  //#99#//DB_NBASE        Nome da base de dados que esta sendo acessada
  //#99#//DB_modulo       Número do módulo que esta acessado
  //#99#//DB_nome_modulo  Nome do módulo que esta acessado
  //#99#//DB_anousu       Exercício que esta sendo acessado
  //#99#//DB_datausu      Data do servidor
  //#99#//DB_coddepto     Código do departamento do usuário
  //#99#//DB_instit       Código da instituição
  //#99#//
  //#99#//Exemplo:
  //#99#//self::db_getsession("DB_datausu");
  //#99#//Irá abrir a página index.php
  if ($var == "0") {
    reset($_SESSION);
    $str = "";
    $caract = "";
    for ($x = 0; $x < sizeof($_SESSION); $x ++) {
      $str .= $caract.key($_SESSION)."=".$_SESSION[key($_SESSION)];
      next($_SESSION);
      $caract = "&";
    }
    return $str;
  } else {
    if (isset ($_SESSION[$var])) {
      return $_SESSION[$var];
    } else {
      if($alertarExistencia == true){
        self::db_msgbox('Variavel de sessão nao encontrada: '.$var);
      }
      return null;
    }
  }
}

//atualiza uma variável de sessao
static function db_putsession($var, $valor) {
  //#00#//self::db_putsession
  //#10#//Esta funcao inclui na sessão do php uma determinada variável
  //#15#//self::db_putsession($var,$valor)
  //#20#//Var   : Nome da variável que será incluida na sessão
  //#20#//valor : Valor da variável incluída
  $_SESSION[$var] = $valor;
}

function db_destroysession($var){
  //#00#//db_destroysession
  //#10#//Esta funcao desregistra uma variável de sessão do php
  //#15#//db_destroysession($var)
  //#20#//Var   : Nome da variável que será desregistrada na sessão
  unset($_SESSION[$var]);
}

//coloca no tamanho e acrecenta caracteres '$qual' a esquerda
function db_sqlformatar($campo, $quant, $qual) {
  $aux = "";
  for ($i = strlen($campo); $i < $quant; $i ++)
    $aux .= $qual;
  return $aux.$campo;

}

//retorna uma string do inicio de $str, até primeiro caractere da ocorrencia em $pos
function db_strpos($str, $pos) {
  return substr($str, 0, (strpos($str, $pos) == "" ? strlen($str) : strpos($str, $pos)));
}

//imprime uma mensagem de erro, com um link pra voltar pra página anterior
function db_erro($msg, $voltar = 1) {
  $uri = $_SERVER["PHP_SELF"];
  echo "$msg<br>\n";
  if ($voltar == 1)
    echo "<a href=\"$uri\">Voltar</a>\n";
  exit;
}

//Tipo a parseInt do javascript
function db_parse_int($str) {
  $num = array ("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
  $tam = strlen($str);
  $aux = "";
  for ($i = 0; $i < $tam; $i ++) {
    if (in_array($str[$i], $num))
      $aux .= $str[$i];
  }
  return $aux;
}

// Tipo o indexOf do javascript
function db_indexOf($str, $proc) {
  // 0 nao encontrou
  // > 0 encontrou
  return strlen(strstr($str, $proc));
}


/**
 * Executa um SELECT e pagina na tela com os labels do sistema
 *
 * Obs.:
 * <ul>
 *   <li>Quando utilizar o parametro automatico, coloque no parametro NomeForm o seguinte "NoMe" e em variaveis_repassa array().</li>
 *   <li>O cabeçalho da tabela o sistema pega pelo nome do campo e busca na documentação, colcando o label</li>
 *   <li>Quando não desejar colocar o label da documentacao, o nome do campo deverá ser iniciado com dl_ e o sistema retirará
 *       estes caracteres e colocará o primeiro caracter em maiusculo
 *   </li>
 *   <li>Para omitir uma coluna, coloque um alias com 'db_' como prefixo usando o nome do campo que desejar omitir.</li>
 * <ul>
 *
 * @param  string  $query             Select que será executado
 * @param  integer $numlinhas         Número de linhas a serem mostradas
 * @param  string  $arquivo           Arquivo que será executado quando der um click em uma linha
 *                                    Na versão com iframe deverá ser colocado "()"
 * @param  string  $filtro            Filtro que será gerado, normalmente ""
 * @param  string  $aonde             Nome da função que será executada quando der um click
 * @param  string  $campos_layer      Campos que serão colocados na layer quando passar o mouse ( não esta implementado )
 * @param  string  $NomeForm          Nome do formulário para colocar variáveis complementares Padrão = "NoMe"
 * @param  array   $variaveis_repassa Array com as variáveis a serem reoassadas para o programa
 * @param  boolean $automatico
 * @param  array   $totalizacao       Deverá fornecer os campos que desejar fazer somatorio, conforme exemplo abaixo: <br>
 *                                    <pre>
 *                                      $totalizacao["e60_vlremp"] = "e60_vlremp"; totaliza o campo <br>
 *                                      $totalizacao["e60_vlranu"] = "e60_vlranu"; totaliza o campo <br>
 *                                      $totalizacao["e60_vlrpag"] = "e60_vlrpag"; totaliza o campo <br>
 *                                      $totalizacao["e60_vlrliq"] = "e60_vlrliq"; totaliza o campo <br>
 *                                      $totalizacao["dl_saldo"] = "dl_saldo";     totaliza o campo ( neste caso, o campo é um alias no sql) <br>
 *                                      $totalizacao["totalgeral"] = "z01_nome";   indica qual o campo sera colocado o total
 *                                    </pre>
 * @return mixed                      HTML com a estrutura do datagrid
 */
function db_lovrot($query, $numlinhas, $arquivo = "", $filtro = "%", $aonde = "_self", $campos_layer = "", $NomeForm = "NoMe", $variaveis_repassa = array (), $automatico = true, $totalizacao = array()) {
  //global $BrowSe;
  //cor do cabecalho
  //global $db_corcabec;
  //cor de fundo de cada registro
  //global $cor1;
  //global $cor2;
  //global $_POST;
  
  $db_corcabec   = $GLOBALS['db_corcabec'] == "" ? "#e1e1e1" : $db_corcabec;
  $mensagem      = "Clique Aqui";
  $cor1          = $GLOBALS['cor1'] == "" ? "#ffffff" : $cor1;
  $cor2          = $GLOBALS['cor2'] == "" ? "#e1dede" : $cor2;
  $tot_registros = "tot_registros".$NomeForm;
  $offset        = "offset".$NomeForm;
  
  //  Recebe os valores do campo hidden
  if( isset( $_POST["totreg".$NomeForm] ) ) {
    $$tot_registros = $_POST["totreg".$NomeForm];
  } else {
    $$tot_registros = 0;
  }

  if( isset( $_POST["offset".$NomeForm] ) ) {
    $$offset = $_POST["offset".$NomeForm];
  } else {
    $$offset = 0;
  }

  if( isset( $_POST["recomecar"] ) ) {
    $recomecar = $_POST["recomecar"];
  }

  // se for a primeira vez que é rodado, pega o total de registros e guarda no campo hidden
  if( ( empty ($$tot_registros) && !empty ($query) ) || isset($recomecar)) {

    if( isset( $recomecar ) ) {
      $query = self::db_getsession("dblov_query_inicial");
    }

    $Dd1 = "disabled";

    if( count( $totalizacao ) > 0 || isset( $totalizacao_rep ) ) {

      $total_campos     = "";
      $sep_total_campos = "";
      reset($totalizacao);

      for ( $j = 0; $j < count( $totalizacao ); $j ++) {

        if( key( $totalizacao ) == $totalizacao[key($totalizacao)] ) {

          $total_campos    .= $sep_total_campos . "sum(" . $totalizacao[key($totalizacao)] . ") as ";
          $total_campos    .= $totalizacao[key($totalizacao)];
          $sep_total_campos = ",";
        }

        next($totalizacao);
      }

      reset($totalizacao);
      $tot = self::db_query("SELECT count(*) as total, $total_campos FROM ($query) as temp");
    } else {
      $tot = self::db_query("SELECT count(*) as total FROM ($query) as temp");
    }

    self::db_putsession("dblov_query_inicial",$query);

    $$tot_registros = $tot->fetch()->total;

    if ( $$tot_registros == 0 ) {
      $Dd2 = "disabled";
    }
  }
  
  if( isset( $_POST["nova_quantidade_linhas"] ) && $_POST["nova_quantidade_linhas"] != '' ) {

    $_POST["nova_quantidade_linhas"] = $_POST["nova_quantidade_linhas"] + 0;
    $numlinhas                       = $_POST["nova_quantidade_linhas"];
  }

  // testa qual botao foi pressionado
  if( isset( $_POST["pri".$NomeForm] ) ) {

    $$offset = 0;
    $Dd1     = "disabled";
    $query   = base64_decode( $_POST["filtroquery"] );
  } else if ( isset( $_POST["ant".$NomeForm] ) ) {

    $query = base64_decode( @$_POST["filtroquery"] );

    if( $$offset <= $numlinhas ) {

      $$offset = 0;
      $Dd1     = "disabled";
    } else {
      $$offset = $$offset - $numlinhas;
    }
  } else if( isset( $_POST["prox".$NomeForm] ) ) {

    $query = base64_decode( $_POST["filtroquery"] );

    if( ( $$offset + ( $numlinhas * 2 ) ) >= $$tot_registros) {
      $Dd2 = "disabled";
    }

    if( $numlinhas >= ( $$tot_registros - $$offset ) ) {

      if( $$tot_registros - $$offset - $numlinhas >= $numlinhas ) {
        $$offset = $numlinhas;
      } else {
        $$offset = $$offset + $numlinhas;
      }

      if( $$offset > $$tot_registros ) {
        $$offset = 0;
      }

      $Dd2 = "disabled";
    } else {
      $$offset = $$offset + $numlinhas;
    }
  } else if( isset ( $_POST["ult".$NomeForm] ) ) {

    $query   = base64_decode( $_POST["filtroquery"] );
    $$offset = $$tot_registros - $numlinhas;
    if( $$offset < 0 ) {
      $$offset = 0;
    }

    $Dd2 = "disabled";
  } else {

    reset( $_POST );

    for( $i = 0; $i < sizeof( $_POST ); $i ++ ) {

      $ordem_lov = substr( key( $_POST ), 0, 11 );

      if( $ordem_lov == 'ordem_dblov' ) {

        $query           = base64_decode( $_POST["filtroquery"] );
        $campo           = substr( key( $_POST ), 11 );
        $ordem_ordenacao = '';

        if( isset( $_POST['ordem_lov_anterior'] ) ) {

          if( $_POST['ordem_lov_anterior'] == $_POST[key( $_POST )] ) {
            $ordem_ordenacao = 'desc';
          }
        }

        if( $_POST["codigo_pesquisa"] != '' ) {

          /**
           * Query para buscar o tipo do campo clicado no cabeçalho
           */
          $sValorPesquisa    = $_POST["codigo_pesquisa"];
          $sWhere            = "nomecam = '{$_POST['campo_filtrado']}'";
          $oDaoDbSysCampo    = new \classes\db_db_syscampo();
          $sSqlDaoDbSysCampo = $oDaoDbSysCampo->sql_query_file( null, 'conteudo', null, $sWhere );
          $rsDaoDbSysCampo   = self::db_query( $sSqlDaoDbSysCampo );

          if( $rsDaoDbSysCampo && pg_num_rows( $rsDaoDbSysCampo ) > 0 ) {

            $sConteudo = db_utils::fieldsMemory( $rsDaoDbSysCampo, 0 )->conteudo;

            /**
             * Caso seja um tipo 'date', trata para inverter o valor passado, transformando num valor válido para o banco
             */
            if( $sConteudo == 'date' ) {

              $aValorPesquisa = array_reverse( explode( "/", $sValorPesquisa ) );
              $sValorPesquisa = "%" . implode( "-", $aValorPesquisa );
            }
          }

          $query_anterior     = $query;
          $query_novo_filtro  = "select * from ({$query}) as x where {$campo}::text ILIKE '{$sValorPesquisa}%'";
          $query_novo_filtro .= " order by {$campo} {$ordem_ordenacao}";
          $query              = $query_novo_filtro;
        } else {

          if( $_POST["distinct_pesquisa"] == '1' ) {

            $query_anterior    = $query;
            $query             = "select distinct on (".$campo.") * from (".$query.") as x order by ".$campo." ".$ordem_ordenacao;
            $query_novo_filtro = $query;

          }else{
            $query = "select * from (".$query.") as x order by ".$campo." ".$ordem_ordenacao;
          }
        }

        $$offset = 0;
        break;
      }
      next($_POST);
    }
  }

  $filtroquery = $query;

  // executa a query e cria a tabela
  if( $query == "" ) {
    exit;
  }

  $query  .= " limit $numlinhas offset ".$$offset;
  $result  = self::db_query($query);
  $NumRows = $result->rowCount();

  if( $NumRows == 0 ) {

    if( isset( $query_anterior ) ) {

      echo "<script>alert('Não existem dados para este filtro');</script>";

      if( count( $totalizacao ) > 0 || isset( $totalizacao_rep ) ) {

        $total_campos     = "";
        $sep_total_campos = "";
        reset($totalizacao);

        for ( $j = 0; $j < count( $totalizacao ); $j ++) {

          if( key( $totalizacao ) == $totalizacao[key( $totalizacao )] ) {

            $total_campos    .= $sep_total_campos . "sum(" . $totalizacao[key($totalizacao)] . ") as ";
            $total_campos    .= $totalizacao[key($totalizacao)];
            $sep_total_campos = ",";
          }

          next($totalizacao);
        }

        reset($totalizacao);
        $tot = self::db_query( "SELECT count(*) as total,{$total_campos} FROM ({$query_anterior}) as temp" );
      } else {
        $tot = self::db_query("SELECT count(*) as total FROM ({$query_anterior}) as temp");
      }

      $$tot_registros = $tot->fetch()->total;

      $query       = $query_anterior . " limit $numlinhas offset " . $$offset;
      $result      = self::db_query($query);
      $NumRows     = $result->rowCount();
      $filtroquery = $query_anterior;
    }
  } else {

    if( isset( $query_anterior ) ) {

      $Dd1 = "disabled";

      if( count( $totalizacao ) > 0 || isset( $totalizacao_rep ) ) {

        $total_campos     = "";
        $sep_total_campos = "";
        reset($totalizacao);

        for ( $j = 0; $j < count( $totalizacao ); $j ++) {

          if( key( $totalizacao ) == $totalizacao[key( $totalizacao )] ) {

            $total_campos    .= $sep_total_campos . "sum(" . $totalizacao[key( $totalizacao )] . ") as ";
            $total_campos    .= $totalizacao[key($totalizacao)];
            $sep_total_campos = ",";
          }

          next($totalizacao);
        }

        reset($totalizacao);
        $tot = self::db_query("SELECT count(*) as total,{$total_campos} FROM ({$query_novo_filtro}) as temp");
      } else {
        $tot = self::db_query("SELECT count(*) FROM ({$query_novo_filtro}) as temp");
      }

      $$tot_registros = $tot->fetch()->total;

      if ( $$tot_registros == 0 ) {
        $Dd2 = "disabled";
      }
    }
  }

  $NumFields = $result->columnCount();

  if( ( $NumRows < $numlinhas ) && ( $numlinhas < ( $$tot_registros - $$offset - $numlinhas ) ) ) {
    $Dd1 = @ $Dd2 = "disabled";
  }

  $sScript = "<script>
                function js_mostra_text( liga, nomediv, evt ) {

                  evt = (evt) ? evt : (window.event) ? window.event : '';

                  if( liga == true ) {

                    document.getElementById(nomediv).style.top        = 0; //evt.clientY;
                    document.getElementById(nomediv).style.left       = 0; //(evt.clientX+20);
                    document.getElementById(nomediv).style.visibility = 'visible';
                  } else {
                    document.getElementById(nomediv).style.visibility = 'hidden';
                  }
                }

                function js_troca_ordem( nomeform, campo, valor ) {

                  document.navega_lov" . $NomeForm . ".campo_filtrado.value = valor;

                  obj = document.createElement( 'input' );
                  obj.setAttribute( 'name', campo );
                  obj.setAttribute( 'type', 'submit' );
                  obj.setAttribute( 'value', valor );
                  obj.setAttribute( 'style', 'color:#FCA; background-color:transparent; border-style:none' );
                  eval( 'document.' + nomeform + '.appendChild( obj )' );
                  eval( 'document.' + nomeform + '.' + campo + '.click()' );
                }

                function js_lanca_codigo_pesquisa( valor_recebido ) {
                  document.navega_lov" . $NomeForm . ".codigo_pesquisa.value = valor_recebido;
                }

                function js_lanca_distinct_pesquisa() {
                  document.navega_lov" . $NomeForm . ".distinct_pesquisa.value = 1;
                }

                function js_nova_quantidade_linhas( valor_recebido ) {

                  valor_recebe = Number( valor_recebido );

                  if( !valor_recebe ) {

                    alert( 'Valor Inválido!' );
                    document.navega_lov" . $NomeForm . ".nova_quantidade_linhas.value = '';
                    document.getElementById('quant_lista').value                      = '';
                  } else {

                    if( valor_recebe > 100 ) {

                      document.navega_lov" . $NomeForm . ".nova_quantidade_linhas.value = '100';
                      document.getElementById('quant_lista').value                      = 100;
                    } else {
                      document.navega_lov".$NomeForm.".nova_quantidade_linhas.value = valor_recebido;
                    }
                  }
                }
              </script>";

  echo $sScript;

  $sHtml  = "<table class='table table-bordered table-responsive-lg' style='font-size:14px;margin:0;'>";
  /**** botoes de navegacao ********/
  $sHtml .= "  <tr>";
  $sHtml .= "    <td colspan=\"". ($NumFields +1)."\">";
  $sHtml .= "      <form name=\"navega_lov".$NomeForm."\" method=\"post\">";
  $sHtml .= "        <input class=\"btn btn-sm btn-dark\" type=\"submit\" name=\"pri".$NomeForm."\"    value=\"Início\" ".@ $Dd1.">     ";
  $sHtml .= "        <input class=\"btn btn-sm btn-dark\" type=\"submit\" name=\"ant".$NomeForm."\"    value=\"Anterior\" ".@ $Dd1.">   ";
  $sHtml .= "        <input class=\"btn btn-sm btn-dark\" type=\"submit\" name=\"prox".$NomeForm."\"   value=\"Próximo\" ".@ $Dd2.">    ";
  $sHtml .= "        <input class=\"btn btn-sm btn-dark\" type=\"submit\" name=\"ult".$NomeForm."\"    value=\"Último\" ".@ $Dd2.">     ";
  $sHtml .= "        <input type=\"hidden\" name=\"offset".$NomeForm."\" value=\"".@ $$offset."\">        ";
  $sHtml .= "        <input type=\"hidden\" name=\"totreg".$NomeForm."\" value=\"".@ $$tot_registros."\"> ";
  $sHtml .= "        <input type=\"hidden\" name=\"codigo_pesquisa\"     value=\"\">                      ";
  $sHtml .= "        <input type=\"hidden\" name=\"distinct_pesquisa\"   value=\"\">                      ";
  $sHtml .= "        <input type=\"hidden\" name=\"filtro\"              value=\"$filtro\">               ";
  $sHtml .= "        <input type=\"hidden\" name=\"campo_filtrado\"      value=\"\">                      ";

  reset($variaveis_repassa);

  if( sizeof( $variaveis_repassa ) > 0 ) {

    for ( $varrep = 0; $varrep < sizeof( $variaveis_repassa ); $varrep ++ ) {

      $sHtml .= "<input type=\"hidden\" name=\"".key($variaveis_repassa)."\"";
      $sHtml .= "       value=\"".$variaveis_repassa[key($variaveis_repassa)]."\">\n";
      next($variaveis_repassa);
    }
  }

  if( isset( $ordem_lov ) && ( isset( $ordem_ordenacao ) && $ordem_ordenacao == '' ) ) {
    $sHtml .= "<input type=\"hidden\" name=\"ordem_lov_anterior\" value=\"".$_POST[key($_POST)]."\">\n";
  }

  if( isset( $_POST['nova_quantidade_linhas'] ) && $_POST['nova_quantidade_linhas'] == '' ) {
    $numlinhas = $_POST['nova_quantidade_linhas'];
  }

  $sHtml .= "<input type=\"hidden\" name=\"nova_quantidade_linhas\" value=\"$numlinhas\" >\n";

  if( isset( $totalizacao ) && isset( $tot ) ) {

    if( count( $totalizacao ) > 0 ) {

      $totNumfields = pg_numfields( $tot );
      for( $totrep = 1; $totrep < $totNumfields; $totrep++ ) {

        $sHtml .= "<input type=\"hidden\"";
        $sHtml .= "       name=\"totrep_" . pg_fieldname( $tot, $totrep ) . "\"";
        $sHtml .= "       value=\"" . db_formatar( pg_result( $tot , 0 , $totrep ), 'f' ) . "\">";
      }

      reset( $totalizacao );
      $totrepreg = "";
      $totregsep = "";

      for( $totrep = 0; $totrep < count( $totalizacao ); $totrep++ ) {

        $totrepreg .= $totregsep . key( $totalizacao ) . "=" . $totalizacao[key( $totalizacao )];
        $totregsep  = "|";
        next( $totalizacao );
      }

      reset( $totalizacao );
      $sHtml .= "<input type=\"hidden\" name=\"totalizacao_repas\" value=\"".$totrepreg."\">";
    }
  } else if( isset( $_POST["totalizacao_repas"] ) ) {

    $totalizacao_split = explode( "\|", $_POST["totalizacao_repas"] );

    for( $totrep = 0; $totrep < count( $totalizacao_split ); $totrep++ ) {

      $totalizacao_sep                  = explode( "\=", $totalizacao_split[$totrep] );
      $totalizacao[$totalizacao_sep[0]] = $totalizacao_sep[1];

      if( isset( $_POST["totrep_".$totalizacao_sep[0]] ) ) {

        $sHtml .= "<input type=\"hidden\"";
        $sHtml .= "       name=\"totrep_".$totalizacao_sep[0]."\"";
        $sHtml .= "       value=\"".$_POST["totrep_".$totalizacao_sep[0]]."\">";
      }
    }

    $sHtml .= "<input type=\"hidden\" name=\"totalizacao_repas\" value=\"".$_POST["totalizacao_repas"]."\">";
  }

  $sHtml .= "<input type=\"hidden\" name=\"filtroquery\" value=\"" . base64_encode( @$filtroquery ) . "\">";

  if( $NumRows > 0 ) {

    $sHtml .= "Foram retornados <label> ". $$tot_registros . "</label> registros.";
    $sHtml .= " Mostrando de <label>" . (@$$offset +1)." </label> até";
    $sHtml .= "<label> ";
    $sHtml .= ($$tot_registros < (@ $$offset + $numlinhas) ? ($NumRows <= $numlinhas ? $$tot_registros : $NumRows) : ($$offset + $numlinhas));
    $sHtml .= "</label>.";
  } else {
    $sHtml .= "Nenhum Registro Retornado";
  }

  $sHtml .= "    </form>";
  $sHtml .= "  </td>";
  $sHtml .= "</tr>";

  /*********************************/
  /***** Escreve o cabecalho *******/
  /*********************************/

  if( $NumRows > 0 ) {

    $sHtml .= "<tr>";

    // se foi passado funcao
    if ( $campos_layer != "" ) {

      $campo_layerexe = explode( "\|", $campos_layer );
      $sHtml .= "<td bgcolor=\"$db_corcabec\" title=\"Executa Procedimento Específico.\">";
      $sHtml .= "  Clique";
      $sHtml .= "</td>";
    }

    $clrotulocab = new rotulolov();

    for ( $i = 0; $i < $NumFields; $i ++ ) {


      if( strlen( strstr( $result->getColumnMeta($i)['name'], "db_") ) == 0 ) {

        $clrotulocab->label( $result->getColumnMeta($i)['name'] );
        $sHtml .= "<td bgcolor=\"$db_corcabec\" title=\"".$clrotulocab->title."\" style=\"padding-top:0;padding-bottom:0;\"> ";
        $sHtml .= "  <input name=\"" . $result->getColumnMeta($i)['name'] . "\" ";
        $sHtml .= "         value=\"" . ucfirst( $clrotulocab->titulo ) . "\" ";
        $sHtml .= "         type=\"button\" ";
        $sHtml .= "         onclick=\"js_troca_ordem( 'navega_lov" . $NomeForm . "', ";
        $sHtml .= "                                   'ordem_dblov" . $result->getColumnMeta($i)['name'] . "', ";
        $sHtml .= "                                   '" . $result->getColumnMeta($i)['name'] . "');\" ";
        $sHtml .= "        >";
        $sHtml .= "</td>";

      } else {

        if( strlen( strstr( $result->getColumnMeta($i)['name'], "db_m_" ) ) != 0 ) {
          $sHtml .= "<td bgcolor=\"$db_corcabec\" ";
          $sHtml .= "    title=\"" . substr( $result->getColumnMeta($i)['name'], 5 ) . "\" ";
          $sHtml .= "    > ";
          $sHtml .= "  <b><u>" . substr( $result->getColumnMeta($i)['name'], 5 ) . "</u></b> ";
          $sHtml .= "</td>";
        }
      }
    }

    $sHtml .= "</tr>";
  }

  //cria nome da funcao com parametros
  if( $arquivo == "()" ) {

    $arrayFuncao                = explode( "|", $aonde );
    $quantidadeItemsArrayFuncao = sizeof( $arrayFuncao );
  }

  $result2  = $result->fetchAll();


  for( $j = 0; $j < $NumFields; $j ++ ) {
    $campoCodigo  = $result->getColumnMeta($j)['name'];
    break;
  }

  /********************************/
  /****** escreve o corpo *********/
  /********************************/
  for( $i = 0; $i < $NumRows; $i ++ ) {

    $sHtml .= '<tr>';

    if( $arquivo == "()" ) {

      $loop     = "";
      $caracter = "";

      if( $quantidadeItemsArrayFuncao > 1 ) {

        for( $cont = 1; $cont < $quantidadeItemsArrayFuncao; $cont ++ ) {

          if( strlen( $arrayFuncao[$cont] ) > 3 ) {

            for( $luup = 0; $luup < $result->columnCount(); $luup ++ ) {

              if( $result->columnCount($luup) == "db_".$arrayFuncao[$cont] ) {
                $arrayFuncao[$cont] = "db_".$arrayFuncao[$cont];
              }
            }
          }

          $loop     .= $caracter . "'";
          $loop     .= $result2[$i]->$campoCodigo . "'";
          $caracter  = ",";
        }

        $resultadoRetorno = $arrayFuncao[0] . "(" . $loop . ")";
      } else {
        $resultadoRetorno = $arrayFuncao[0] . "()";
      }
    }

    if( isset( $cor ) ) {
      $cor = $cor == $cor1 ? $cor2 : $cor1;
    } else {
      $cor = $cor1;
    }

    if( $campos_layer != "" ) {

      $campo_layerexe = explode( "\|", $campos_layer );
      $sHtml .= "<td id=\"funcao_aux" . $i . "\" ";
      $sHtml .= "    class = 'DBLovrotTdFuncaoAuxiliar' ";
      $sHtml .= "    bgcolor=\"$cor\"> ";
      $sHtml .= "  <a href=\"\" onclick=\"" . $campo_layerexe[1] . "({$loop});return false\" > ";
      $sHtml .= "    <strong>" . $campo_layerexe[0] . "&nbsp;</strong> ";
      $sHtml .= "  </a> ";
      $sHtml .= "</td>";
    }


    for( $j = 0; $j < $NumFields; $j ++ ) {
      $nomeCampo        = $result->getColumnMeta($j)['name'];
      $sHtmlCampos      = "";
      $var_data         = "";
      $lCampoTipoTexto  = false;
      $lTipoEspecifico  = true;
      $lPrefixoDb       = false;
      
      //print_r($result2[$i]);

      if(    strlen( strstr( $result->getColumnMeta($j)['name'], "db_" ) ) == 0
        || strlen( strstr( $result->getColumnMeta($j)['name'], "db_m_" ) ) != 0 ) {

        if( $result->getColumnMeta($j)['native_type'] == "date") {
          //  if( $result2[$i]->getColumnMeta($j)['name'] != "" ) {
          if( $result->getColumnMeta($j)['name'] != "" ) {

            $matriz_data = explode( "-", $result2[$i]->$nomeCampo );
            $var_data    = $matriz_data[2] . "/" . $matriz_data[1] . "/" . $matriz_data[0];
          } else {
            $var_data = "//";
          }
       
        } else if( $result->getColumnMeta($j)['native_type'] == "float8" || $result->getColumnMeta($j)['native_type'] == "float4" || $result->getColumnMeta($j)['native_type'] == "numeric" ) {
          $var_data = self::db_formatar( $result2[$i]->$nomeCampo, 'f', ' ');
        
        } else if( $result->getColumnMeta($j)['native_type'] == "bool" ) {
          $var_data  = ( $result2[$i]->$nomeCampo == 'f' || $result->getColumnMeta($j)['native_type'] == '' ? 'Não' : 'Sim' );
        } else if( $result->getColumnMeta($j)['native_type'] == "text" ) {

          $lCampoTipoTexto = true;
          $var_data  = substr( $result2[$i]->$nomeCampo, 0, 10 );
        } else {
          $lEncontrouResultado = true;
          $sTitulo             = "";
          $sLabel              = "";
          $sCampo              = $result->getColumnMeta($j)['name'];
          $lTipoEspecifico     = false;

          switch( $sCampo ) {

            case 'j01_matric':

              $sTitulo = "Informações Imóvel";
              $sLabel  = "iptubase";
              break;

            case 'm80_codigo':

              $sTitulo = "Informações Lançamento";
              $sLabel  = "matestoqueini";
              break;

            case 'm40_codigo':

              $sTitulo = "Informações Requisição";
              $sLabel  = "matrequi";
              break;

            case 'm42_codigo':

              $sTitulo = "Informações Atendimento";
              $sLabel  = "atendrequi";
              break;

            case 'm45_codigo':

              $sTitulo = "Informações Devolução";
              $sLabel  = "matestoquedev";
              break;

            case 't52_bem':

              $sTitulo = "Informações Bem";
              $sLabel  = "bem";
              break;

            case 'q02_inscr':

              $sTitulo = "Informações Issqn";
              $sLabel  = "issbase";
              break;

            case 'z01_numcgm':

              $sTitulo = "Informações Contribuinte/Empresa";
              $sLabel  = "cgm";
              break;

            case 'e60_numemp':
            case 'e61_numemp':
            case 'e62_numemp':

              $sTitulo = "Informações do Empenho";
              $sLabel  = "empempenho";
              break;

            case 'e54_autori':
            case 'e55_autori':
            case 'e56_autori':

              $sTitulo = "Informações da Autorização de Empenho";
              $sLabel  = "empautoriza";
              break;

            case 'pc10_numero':

              $sTitulo = "Informações da Solicitação";
              $sLabel  = "empsolicita";
              break;

            default:

              $lEncontrouResultado = false;
              break;
          }

          if( $lEncontrouResultado ) {
            $sHtmlCampos = "<td id=\"I".$i.$j."\" style=\"padding-top:0;padding-bottom:0;\" bgcolor=\"$cor\"><a title='" . $sTitulo . "' onclick=\"js_JanelaAutomatica('" . $sLabel . "','". (trim(pg_result($result, $i, $j)))."');return false;\">&nbsp;Inf->&nbsp;</a>". ($arquivo != "" ? "<a title=\"$mensagem\" class='DBLovrotRegistrosRetornados' href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim($result2[$i]->$nomeCampo)."</a>" : (trim($result2[$i]->$nomeCampo)))."&nbsp;</td>\n";
          } else {
            $sHtmlCampos = "<td id=\"I".$i.$j."\" style=\"padding-top:0;padding-bottom:0;\" bgcolor=\"$cor\">". ($arquivo != "" ? "<a title=\"$mensagem\" class='DBLovrotRegistrosRetornados' href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim($result2[$i]->$nomeCampo)."</a>" : (trim($result2[$i]->$nomeCampo)))."&nbsp;</td>\n";
          }
        }
      } else {
        $lPrefixoDb = true;
      }

      if( $lPrefixoDb ) {
        continue;
      }

      if ( $lTipoEspecifico ) {

        if( $lCampoTipoTexto ) {

          $sHtmlCampos = "<td style=\"padding-top:0;padding-bottom:0;\" onMouseOver=\"js_mostra_text(true,'div_text_".$i."_".$j."',event);\"
                              onMouseOut=\"js_mostra_text(false,'div_text_".$i."_".$j."',event)\"
                              id=\"I" . $i . $j . "\"
                              bgcolor=\"$cor\">";
        } else {
          $sHtmlCampos = "<td style=\"padding-top:0;padding-bottom:0;\" id=\"I".$i.$j."\" bgcolor=\"$cor\">";
        }
      }

      $sHtml .= $sHtmlCampos;

      if( $arquivo != "" ) {
        $mensagem2 = str_replace( "\n", "<br>", $result2[$i]->$nomeCampo );
        $sHtml   .= "<a title=\"$mensagem2\" ";
        $sHtml   .= "  href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim($var_data);
        $sHtml   .= "</a>";
      } else {
        $sHtml .= trim($var_data);
      }

      $sHtml .= "</td>";
    }

    $sHtml .= "</tr>";
  }

  if( count( $totalizacao ) > 0 ) {

    $sHtml .= "<tr>";
    for( $j = 0; $j < $NumFields; $j ++ ) {

      $key_elemento = array_search( $result->getColumnMeta($j)['name'], $totalizacao );

      if( $key_elemento == true and $result->getColumnMeta($j)['name'] == $key_elemento and strlen( strstr( $result->getColumnMeta($j)['name'], "db_" ) ) == 0 ) {

        @$vertotrep = $_POST['totrep_'.$key_elemento];

        if( @$vertotrep != "" && !isset( $tot ) ) {

          $sHtml .= "<td>";
          $sHtml .= $vertotrep;
          $sHtml .= "</td>";
        } else if( isset( $tot ) ) {

          $sHtml .= "<td>";
          $sHtml .= self::db_formatar( $tot->fetch()->$key_elemento, 'f' ) . "&nbsp;";
          $sHtml .= "</td>";
        } else {
          $sHtml .= "<td></td>\n";
        }
      } else {

        if( $key_elemento == 'totalgeral' ) {
          $sHtml .=  "<td> Total Geral : </td>";
        } else if ( strlen( strstr( $result->getColumnMeta($j)['name'], "db_" ) ) == 0 ) {
          $sHtml .=  "<td></td>";
        }
      }
    }

    $sHtml .= "</tr>";
  }

  if( $NumRows > 0 ) {

    $sHtml .= "<tr>";
    $sHtml .= "  <td colspan=$NumFields>";
    $sHtml .= "    <input name='recomecar' ";
    $sHtml .= "           type='button' ";
    $sHtml .= "           value='Recomeçar' ";
    $sHtml .= "           onclick=\"js_troca_ordem( 'navega_lov" . $NomeForm . "', 'recomecar', '0' );\">";
    $sHtml .= "    <label for='indica_codigo' style='cursor:pointer;'>Indique o Conteúdo:</label> ";
    $sHtml .= "    <input title='Digite o valor a pesquisar e clique sobre o campo (cabeçalho) a pesquisar' ";
    $sHtml .= "           id=indica_codigo name=indica_codigo ";
    $sHtml .= "           type=text ";
    $sHtml .= "           onchange='js_lanca_codigo_pesquisa( this.value )'";
    $sHtml .= "           >";
    $sHtml .= "    <label for='quant_lista' style='cursor:pointer;'>Quantidade a Listar:</label>";
    $sHtml .= "    <input id=quant_lista ";
    $sHtml .= "           name=quant_lista ";
    $sHtml .= "           type=text ";
    $sHtml .= "           onchange='js_nova_quantidade_linhas( this.value )'";
    $sHtml .= "           ";
    $sHtml .= "           value='$numlinhas' ";
    $sHtml .= "           size='5'>";
    $sHtml .= "    <label for='mostra_diferentes' style='cursor:pointer;'>Mostra Diferentes:</label>";
    $sHtml .= "    <input title='Mostra os valores diferentes clicando no cabeçalho a pesquisar' ";
    $sHtml .= "           id='mostra_diferentes' name=mostra_diferentes ";
    $sHtml .= "           type=checkbox ";
    $sHtml .= "           onchange='js_lanca_distinct_pesquisa()' ";
    $sHtml .= "           >";
    $sHtml .= "  </td>";
    $sHtml .= "</tr>";
  }

  $sHtml .= "</table>";

  if( $automatico == true ) {

    if( $result->rowCount() == 1 && $$offset == 0 ) {
      $sHtml .= "<script>".@$resultadoRetorno."</script>";
    }
  }

  echo $sHtml;
  return $result;
}

function db_lov($query, $numlinhas, $arquivo = "", $filtro = "%", $aonde = "_self", $mensagem = "Clique Aqui", $NomeForm = "NoMe") {
  global $BrowSe;
  //cor do cabecalho
  global $db_corcabec;
  $db_corcabec = $db_corcabec == "" ? "#CDCDFF" : $db_corcabec;
  //cor de fundo de cada registro
  global $cor1;
  global $cor2;
  $cor1 = $cor1 == "" ? "#97B5E6" : $cor1;
  $cor2 = $cor2 == "" ? "#E796A4" : $cor2;
  global $HTTP_POST_VARS;
  $tot_registros = "tot_registros".$NomeForm;
  $offset = "offset".$NomeForm;
  //recebe os valores do campo hidden
  $$tot_registros = @ $HTTP_POST_VARS["totreg".$NomeForm];
  $$offset = @ $HTTP_POST_VARS["offset".$NomeForm];
  // se for a primeira vez que é rodado, pega o total de registros e guarda no campo hidden
  if (empty ($$tot_registros)) {
    $Dd1 = "disabled";
    $tot = self::db_query("select count(*) from ($query) as temp");
    $$tot_registros = pg_result($tot, 0, 0);
  }
  // testa qual botao foi pressionado
  if (isset ($HTTP_POST_VARS["pri".$NomeForm])) {
    $$offset = 0;
    $Dd1 = "disabled";
  } else
    if (isset ($HTTP_POST_VARS["ant".$NomeForm])) {
      if ($$offset <= $numlinhas) {
        $$offset = 0;
        $Dd1 = "disabled";
      } else
        $$offset = $$offset - $numlinhas;
    } else
      if (isset ($HTTP_POST_VARS["prox".$NomeForm])) {
        if ($numlinhas >= ($$tot_registros - $$offset - $numlinhas)) {
          $$offset = $$tot_registros - $numlinhas;
          $Dd2 = "disabled";
        } else
          $$offset = $$offset + $numlinhas;
      } else
        if (isset ($HTTP_POST_VARS["ult".$NomeForm])) {
          $$offset = $$tot_registros - $numlinhas;
          $Dd2 = "disabled";
        } else {
          $$offset = @ $HTTP_POST_VARS["offset".$NomeForm] == "" ? 0 : @ $HTTP_POST_VARS["offset".$NomeForm];
        }
  // executa a query e cria a tabela
  $query .= " limit $numlinhas offset ".$$offset;
  $result = self::db_query($query);
  $NumRows = pg_numrows($result);
  $NumFields = pg_numfields($result);
  if ($NumRows < $numlinhas)
    $Dd1 = $Dd2 = "disabled";
  echo "<table id=\"TabDbLov\" border=\"1\" cellspacing=\"1\" cellpadding=\"0\">\n";
  /**** botoes de navegacao ********/
  echo "<tr><td colspan=\"$NumFields\" nowrap>
          <form name=\"navega_lov".$NomeForm."\" method=\"post\">
            <input type=\"submit\" name=\"pri".$NomeForm."\" value=\"<<\" ".@ $Dd1.">
            <input type=\"submit\" name=\"ant".$NomeForm."\" value=\"<\" ".@ $Dd1.">
            <input type=\"submit\" name=\"prox".$NomeForm."\" value=\">\" ".@ $Dd2.">
            <input type=\"submit\" name=\"ult".$NomeForm."\" value=\">>\" ".@ $Dd2.">
                <input type=\"hidden\" name=\"offset".$NomeForm."\" value=\"".$$offset."\">
                <input type=\"hidden\" name=\"totreg".$NomeForm."\" value=\"".$$tot_registros."\">
                <input type=\"hidden\" name=\"filtro\" value=\"$filtro\">
          </form>". ($NumRows > 0 ? "
          Foram retornados <font color=\"red\"><strong>".$$tot_registros."</strong></font> registros.
          Mostrando de <font color=\"red\"><strong>". ($$offset +1)."</strong></font> até
          <font color=\"red\"><strong>". ($$tot_registros < ($$offset + $numlinhas) ? $NumRows : ($$offset + $numlinhas))."</strong></font>." : "Nenhum Registro
          Retornado")."
          </td></tr>\n";
  /*********************************/
  /***** Escreve o cabecalho *******/
  if ($NumRows > 0) {
    echo "<tr>\n";
    for ($i = 0; $i < $NumFields; $i ++) {
      if (strlen(strstr(pg_fieldname($result, $i), "db")) == 0)
        echo "<td nowrap bgcolor=\"$db_corcabec\"  style=\"font-size:13px\" align=\"center\"><b><u>".ucfirst(pg_fieldname($result, $i))."</u></b></td>\n";
    }
    echo "</tr>\n";
  }
  /********************************/
  /****** escreve o corpo *******/
  for ($i = 0; $i < $NumRows; $i ++) {
    echo "<tr>\n";
    $cor = @ $cor == $cor1 ? $cor2 : $cor1;
    for ($j = 0; $j < $NumFields; $j ++) {
      if (strlen(strstr(pg_fieldname($result, $j), "db")) == 0)
        echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;font-size:13px\" bgcolor=\"$cor\" nowrap>
                   ". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;font-size:13px\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"js_retornaValor('I".$i.$j."');return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">"). (trim(pg_result($result, $i, $j)) == "" ? "&nbsp;" : trim(pg_result($result, $i, $j)))."</a>" : (trim(pg_result($result, $i, $j)) == "" ? "&nbsp;" : trim(pg_result($result, $i, $j))))."</td>\n";
    }
    echo "</tr>\n";
  }
  /******************************/
  echo "</table>";

  return $result;
}

//  Insere um registro de log
function db_logs($string = '', $codcam = 0, $chave = 0) {
  $wheremod="";
  if(isset($_SESSION["DB_modulo"])){
    $wheremod =  "and modulo = ".self::db_getsession("DB_modulo");
  }
  $sql = "select db_itensmenu.id_item
                  from configuracoes.db_itensmenu
            inner join configuracoes.db_menu  on  db_menu.id_item_filho = db_itensmenu.id_item
                  where trim(funcao) = '".trim(addslashes( basename( $_SERVER["REQUEST_URI"] ) ))."'
            $wheremod limit 1 ";

  $result = self::db_query($sql);

  if ($result != false && $result->rowCount() > 0) {
    $item = $result->fetch()->id_item;

    $sql        = "select nextval('db_logsacessa_codsequen_seq')";
    $result     = self::db_query($sql);
    $codsequen  = self::lastInsertId();

    // grava codigo na sessao
    self::db_putsession("DB_itemmenu_acessado",$item);
    self::db_putsession("DB_acessado",$codsequen);


    $sql = "INSERT INTO db_logsacessa VALUES ($codsequen,
                                              '".self::db_getsession("DB_ip")."',
                                              '".date("Y-m-d")."',
                                              '".date("H:i:s")."',
                                              '".$_SERVER["REQUEST_URI"]."',
                                              '$string',
                                              ".self::db_getsession("DB_id_usuario").",
                                              ".self::db_getsession("DB_modulo").",
                                              ".$item.",
                                              ".self::db_getsession("DB_coddepto").",

                                              ".self::db_getsession("DB_instit").")";

    $rs = self::db_query($sql);
    if (!$rs) {
      die("Houve um problema ao realizar a auditoria do sistema.");
    }
  }
}

function db_logsmanual($string = '', $modulo = 0, $item = 0, $codcam = 0, $chave = 0) {
  $sql = "INSERT INTO db_logsacessa VALUES (nextval('db_logsacessa_codsequen_seq'),'".self::db_getsession("DB_ip")."','".date("Y-m-d")."','".date("H:i:s")."','".$_SERVER["REQUEST_URI"]."','$string',".self::db_getsession("DB_id_usuario").",".$modulo.",".$item.",".self::db_getsession("DB_coddepto").",".self::db_getsession("DB_instit").")";
  $rs = self::db_query($sql);
  if (!$rs) {
    die("Houve um problema ao realizar a auditoria do sistema.");
  }
}

static function db_logsmanual_demais($string = '', $id_usuario=0, $modulo = 0, $item = 0, $coddepto = 0, $instit = 0) {
  $db_ip = $_SERVER['REMOTE_ADDR'];

  $sql = "INSERT INTO db_logsacessa VALUES (nextval('db_logsacessa_codsequen_seq'),'$db_ip','".date("Y-m-d")."','".date("H:i:s")."','".$_SERVER["REQUEST_URI"]."','$string',$id_usuario,$modulo,$item,$coddepto,$instit)";
  $rs = self::db_query($sql);
  if (!$rs) {
    die("Houve um problema ao realizar a auditoria do sistema.");
  }
}

/**
 * Menu do sistema
 *
 * @example db_menu
 * @param integer $usuario - Id do usuário
 * @param integer $modulo  - Código do Módulo
 * @param integer $anousu  - Exercício de Acesso
 * @param integer $instit  - Número da instituição
 */
function db_menu($usuario = null, $modulo = null, $anousu = null, $instit = null) {
  
  $usuario = !empty($usuario) ? $usuario : self::db_getsession("DB_id_usuario");
  $modulo  = !empty($modulo)  ? $modulo  : self::db_getsession("DB_modulo");
  $anousu  = !empty($anousu)  ? $anousu  : self::db_getsession("DB_anousu");
  $instit  = !empty($instit)  ? $instit  : self::db_getsession("DB_instit");

  $idItem = self::db_getsession('DB_itemmenu_acessado');

  /**
   * Busca as preferênias do usuário
   */
  
  $oPreferencias = unserialize(base64_decode(self::db_getsession('DB_preferencias_usuario')));
  $sOrdenacao    = DBMenu::getCampoOrdenacao();
  $DBMenu        = new DBMenu($modulo, $usuario, $anousu, $instit);
  $DBMenu->setAdministrador( (self::db_getsession("DB_administrador") == 1) );
  $DBMenu->setDBSeller( isset($DB_SELLER) );
  $DBMenu->setExibeBuscaMenus( ($oPreferencias->getExibeBusca() == '1') );
  $DBMenu->setDataUsu( (isset($_SESSION["DB_datausu"]) ? date("Y", self::db_getsession("DB_datausu")) : date("Y")) );
  $DBMenu->setFuncao( strtolower(basename($_SERVER["PHP_SELF"])) );
  $DBMenu->setOrdenacao($sOrdenacao);

  $sMenu = $DBMenu->montaMenu($modulo);

  echo $sMenu;

  if (!empty($sMenu)) {

    $iCodigoDepartamento = '';

    if (isset ($_SESSION["DB_coddepto"])) {

      $iCodigoDepartamento = self::db_getsession("DB_coddepto");
      $result = @ self::db_query("select descrdepto from db_depart where coddepto = ".self::db_getsession("DB_coddepto"));

      if ($result != false && pg_numrows($result) > 0) {
        $descrdep = "[<strong>".self::db_getsession("DB_coddepto")."-".substr(pg_result($result, 0, 'descrdepto'), 0, 40)."</strong>]";
      } else {
        $descrdep = "";
      }

    } else {
      $descrdep = "";
    }

    $msg = ucfirst(self::db_getsession("DB_nome_modulo")) . $descrdep . "->" . $DBMenu->getDescricaoFuncao();

    echo "<script>
            (window.CurrentWindow || parent.CurrentWindow).bstatus.document.getElementById('st').innerHTML = '&nbsp;&nbsp;$msg' ;
            (window.CurrentWindow || parent.CurrentWindow).bstatus.document.getElementById('dtatual').innerHTML = '".date("d/m/Y", self::db_getsession("DB_datausu"))."' ;
            (window.CurrentWindow || parent.CurrentWindow).bstatus.document.getElementById('dtanousu').innerHTML = '".self::db_getsession("DB_anousu")."' ;

            if ((window.CurrentWindow || parent.CurrentWindow).bstatus.window.carregaDepartamentos) {
              (window.CurrentWindow || parent.CurrentWindow).bstatus.window.carregaDepartamentos($iCodigoDepartamento);
            }


            function js_db_menu_confirma () {

              if(typeof(js_db_menu_retorno) != 'undefined')
                var retorno  = js_db_menu_retorno();
              else
                var retorno = true;
              return retorno;
            }

            if(document.getElementById('autoCompleteMenus')){
               autoCompleteMenu();
            }
          </script>";

  } else {
    echo "Sem permissao de menu!";
  }
}

// acessa menu
function db_acessamenu($item_menu, $descr, $acao) {
  //#00#//db_acessamenu
  //#10#//Esta funcao acessa o menu de permissões do usuário
  //#15#//db_acessamenu($item_menu,$acao);
  //#20#//Item menu : Item de menu que será acessado
  //#20#//Ação      : Ação a executar quando clicado no menu
  //#20#//            1 - Abrir Janela de Iframe
  //#20#//            2 - Redirecionar para o ítem
  $sql = "select m.descricao
                  from db_permissao p
                       inner join db_itensmenu m on m.id_item = p .id_item
                  where p.anousu = ".self::db_getsession("DB_anousu")." and p.id_item = $item_menu and id_usuario = ".self::db_getsession("DB_id_usuario");
  $res = self::db_query($sql);
  if (pg_numrows($res) > 0) {
    $descri = pg_result($res, 0, 'descricao');
    echo " <input name='db_acessa_menu_".$item_menu."' value='".$descr."' type='button' onclick=\"document.getElementById('DBmenu_".$item_menu."').click();\" title='$descri'>  ";
  }
}

// emite o valor por extenso ( em moeda )
function db_extenso($valor = 0, $maiusculas = false) {
  //#00#//db_extenso
  //#10#//Esta funcao retorna um valor por extenso em maiusculo ou não
  //#15#//db_extenso($valor,$maiusculo);
  //#20#//Valor    : Valor a ser gerado
  //#20#//Maiusculo: Se retorna a string gerada em maiusculo ou não

  $rt = '';
  $singular = array ("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
  $plural = array ("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");

  $c = array ("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
  $d = array ("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
  $d10 = array ("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
  $u = array ("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");

  $z = 0;

  $valor = number_format($valor, 2, ".", ".");
  $inteiro = explode(".", $valor);
  for ($i = 0; $i < count($inteiro); $i ++)
    for ($ii = strlen($inteiro[$i]); $ii < 3; $ii ++)
      $inteiro[$i] = "0".$inteiro[$i];

  $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
  for ($i = 0; $i < count($inteiro); $i ++) {
    $valor = $inteiro[$i];
    $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
    $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
    $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

    $r = $rc. (($rc && ($rd || $ru)) ? " e " : "").$rd. (($rd && $ru) ? " e " : "").$ru;
    $t = count($inteiro) - 1 - $i;
    $r .= $r ? " ". ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
    if ($valor == "000")
      $z ++;
    elseif ($z > 0) $z --;
    if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
      $r .= (($z > 1) ? " de " : "").$plural[$t];
    //        $rt = '';
    if ($r)
      $rt = $rt. ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e ") : " ").$r;
  }

  if (!$maiusculas) {
    return ($rt ? $rt : "zero");
  } else { /*
                  Trocando o " E " por " e ", fica muito + apresentável!
                  Rodrigo Cerqueira, rodrigobc@fte.com.br
                  */
    if ($rt)
      $rt = ereg_replace(" E ", " e ", ucwords($rt));
    return (($rt) ? ($rt) : "Zero");
  }

}
function db_permissaomenu($ano, $modulo, $item) {
  //#00#//db_permissaomenu
  //#10#//Esta funcao verifica se o usuario tem permissao de menu do item no ano e modulo especificado
  //#15#//db_permissaomenu($ano,$modulo,$item);
  //#20#//Ano: ano que deseja utilizar para verificar o acesso
  //#20#//Modulo: codigo do modulo que deseja utilizar para verificar o acesso
  //#20#//Item: codigo do item que deseja utilizar para verificar o acesso
  //#20#//Sempre será utilizado o usuario atual para a verificacao
  if (self::db_getsession("DB_id_usuario") == 1 || self::db_getsession("DB_administrador") == 1) {
    return "true";
  } else {
    $sql = "select id_item
                                                                from db_permissao
                                where anousu = $ano and id_modulo = $modulo and id_item = $item and id_usuario = ".self::db_getsession("DB_id_usuario") . " and db_permissao.id_instit = " . self::db_getsession("DB_instit") . " union
                                select id_item
                                from db_permissao
                                inner join db_permherda on db_permherda.id_perfil = db_permissao.id_usuario
                                where db_permissao.anousu = $ano and db_permissao.id_modulo = $modulo and db_permissao.id_item = $item and db_permherda.id_usuario = ".self::db_getsession("DB_id_usuario") . " and db_permissao.id_instit = " . self::db_getsession("DB_instit");






    //echo $sql;exit;
    $res = self::db_query($sql);
    if (pg_numrows($res) == 0) {
      return "false";
    } else {
      return "true";
    }
  }
}

// ---------------------
function debug($classe, $func = false) {
  print_vars($classe);
  if ($func == true)
    print_methods($classe);

}
function print_vars($obj) {
  $arr = get_object_vars($obj);
  echo "<table border=0 bgcolor=#AAAAFF style='border:1px solid'>";
  echo "<tr><td colspan=3>Debug da classe ".get_class($obj)." </td></tr>";
  while (list ($prop, $val) = each($arr))
    echo "<tr><td>&nbsp; </td><td align=left>var $$prop </td><td> $val </td>";
}
function print_methods($obj) {
  echo "<tr><td colspan=3>Metodos encontrados </td></tr>";
  $arr = get_class_methods(get_class($obj));
  foreach ($arr as $method)
    echo "<tr><td>&nbsp; </td><td colspan=2>function $method() </td>";
  echo "<table>";
}

function db_base_ativa() {
  //#00#//db_base_ativa
  //#10#//Esta funcao verifica se a variavel DB_NBASE esta setada para quando troca de base pelo info
  //#15#//db_base_ativa();
  //#20#//dbnbase=Nome da Base de Dados
  if (isset ($GLOBALS["DB_NBASE"])) {
    return $GLOBALS["DB_NBASE"];
  } else {
    return $GLOBALS["DB_BASE"];
  }
}

function db_criatermometro($dbnametermo='termometro',$dbtexto='Concluído',$dbcor='blue',$dbborda=1,$dbacao='Aguarde Processando...'){

  //#00#//db_criatermometro
  //#10#//Cria uma barra de progresso no ponto do programa que for chamado
  //#15#//db_criatermometro('termometro','Concluído','blue',1);
  //#20#//dbnametermo = Nome do termometro e da funcao js que atualiza o termometro
  //#20#//dbtexto     = Texto mostrado no lado da porcentagem concluida
  //#20#//dbcor       = Cor do termometro
  //#20#//dbborda     = Borda, 1 com borda ou 2 sem borda
  //#20#//dbacao      = Texto para acao executada ex: Aguarde Processando...
  //#99#//Essa função apenas cria o termometro, para atualizar o valor do termometro deve usar a funcao db_atutermometro

  if($dbborda !=1 && $dbborda !=0){
    $dbborda=1;
  }
  /*
   bkp
   <table border=".$dbborda." cellspacing=0 cellpadding=0>
   */
  echo "<table align='center' marginwidth='0' width='790' border='0' cellspacing='0' cellpadding='0'>";
  echo "<tr>
                  <td align='center'>
                   <b> $dbacao </b>
                  </td>
                </tr>";
  echo "<tr>
                    <td align='center'>";
  echo "   </td>
                 </tr>";
  echo "<tr>
                    <td align='center'>
                      <table style='border-collapse: collapse; border:1px solid #525252;' cellspacing=0 cellpadding=0>
                      <tr><td>";
  echo "        <table border=0 cellspacing=0 cellpadding=0>";
  echo "           <tr>
                               <td>";
  echo "                   <input name='".$dbnametermo."' style='background: transparent;text-align:center' id='dbtermometro".$dbnametermo."' type='text' value='' size=100 readonly>
                               </td>
                           </tr>";
  echo "           <tr>
                               <td>";
  echo "                   <input name='barra".$dbnametermo."' style='background: ".$dbcor.";text-align:center;visibility:hidden' id='dbbarra".$dbnametermo."' type='text' value='' size=0 readonly> ";
  echo "              </td>
                            </tr>";
  echo "        </table>
                     </td></tr>
                     </table>";
  echo "   </td>
                 </tr>";
  echo "</table>";
  echo "<script>
                    function js_termo_".$dbnametermo."(atual,texto){
                            atual = new Number(atual);
                            dbtexto = (texto==null)?'{$dbtexto}':texto;
                            document.getElementById('dbtermometro".$dbnametermo."').value = ' '+atual.toFixed(0)+'%'+' '+dbtexto;
                            document.getElementById('dbbarra".$dbnametermo."').size = atual;
                            document.getElementById('dbbarra".$dbnametermo."').style.visibility = 'visible';
                      }
                </script>";
}

function db_atutermometro($dblinha,$dbrows,$dbnametermo,$dbquantperc=1,$dbtexto=null){

  //#00#//db_atutermometro
  //#10#//Atualiza o valor do termometro
  //#15#//db_atutermometro($i,$numrows,'termometro',1);
  //#20#//dblinha       = linha que esta atualmente
  //#20#//dbrows        = total de registros
  //#20#//dbnametermo   = nome do termometro q foi criado com o db_criatermometro
  //#20#//dbquantperc   = percentual que a barra sera atualizada
  global $percentualAuxiliar;
  $percatual = (int) (($dblinha * 100) / $dbrows);

  if ($percatual > $percentualAuxiliar) {

    $percentualAuxiliar = $percatual;
    if(is_null($dbtexto)) {
      echo "<script>js_termo_".$dbnametermo."($percatual);</script>";
    } else {
      echo "<script>js_termo_".$dbnametermo."($percatual,'$dbtexto');</script>";
    }
    echo str_repeat(' ', 1024 * 64);
    flush();
  }
}

function db_tracelog($descricao, $sql, $lErro){

  if( !TraceLog::getInstance()->isActive() ) {
    return;
  }
}

function db_tracelogfile($sStr){
  return TraceLog::getInstance()->write($sStr);
}

function db_tracelogsaida($tipo, $descricao, $sql) {
  switch($tipo) {
    case "file": break;
    case "db": break;
    default: break;
  }
}

function db_preparageratxt($lista, $k00_tipo =null) {

  global $k03_numpre, $fc_numbco, $aNumpres;
  $result = self::db_query("select nextval('numpref_k03_numpre_seq') as k03_numpre");
  db_fieldsmemory($result,0);

  $aNumpres = array();

  global $k00_codbco, $k00_codage, $k00_descr, $k00_hist1, $k00_hist2, $k00_hist3, $k00_hist4, $k00_hist5, $k00_hist6, $k00_hist7, $k00_hist8, $k03_tipo, $k00_tipoagrup;

  if ($k00_tipo == null) {

    $resultnumbco = self::db_query("select distinct k00_codbco,k00_codage,k00_descr,k00_hist1,k00_hist2,k00_hist3,k00_hist4,k00_hist5,k00_hist6,k00_hist7,k00_hist8,k03_tipo,k00_tipoagrup from arretipo
                        inner join listatipos on k62_tipodeb = k00_tipo where k62_lista = $lista");

    if(pg_numrows($resultnumbco)==0){
      echo "O código do banco não esta cadastrado no arquivo arretipo para este tipo!";
      exit;
    }
  } else {

    $sqlnumbco = "select distinct k00_codbco,k00_codage,k00_descr,k00_hist1,k00_hist2,k00_hist3,k00_hist4,k00_hist5,k00_hist6,k00_hist7,k00_hist8,k03_tipo,k00_tipoagrup
                                                                                from arretipo
                                                                                where k00_tipo = $k00_tipo";
    $resultnumbco = self::db_query($sqlnumbco) or die($sqlnumbco);

    if(pg_numrows($resultnumbco)==0){
      echo "O código do banco não esta cadastrado no arquivo arretipo para este tipo!";
      exit;
    }
  }
  db_fieldsmemory($resultnumbco, 0);

  $resultfc = self::db_query("select fc_numbco($k00_codbco,'$k00_codage')") or die("erro ao executar fc_numbco");
  db_fieldsmemory($resultfc, 0);

}

function db_separainstrucao($texto, $comeca=0, &$layout, $linha, $separador, $maximo=0, $quantidadegeral) {
  global $quantidadegeral;

  $texto = db_geratexto($texto);

  $textos = explode("\|", $texto);

  //        for ($xxx=0; $xxx < sizeof($textos); $xxx++) {
  //                echo "$xxx: " . $textos[$xxx] . "<br>";
  //        }

  if ($maximo == 0) {
    $maximo = sizeof($textos);
  }
  if (trim($texto) != "") {
    $totalprocessado=0;
    if ($separador == "05") {
      $mostrar=0;
    } else {
      $mostrar=0;
    }

    ////                echo "$texto<br>size: " . sizeof($textos) . "<br>";
    ////                flush();

    for ($contasepara=0; $contasepara < sizeof($textos); $quantsepara) {
      global $instrucao1, $instrucao2, $instrucao3, $instrucao4;
      $instrucao1="";
      $instrucao2="";
      $instrucao3="";
      $instrucao4="";

      if ($totalprocessado+$maximo > sizeof($textos)) {
        if ($mostrar == "1") {
          echo "111<br>";
        }
        $processar=sizeof($textos)-$totalprocessado;
      } else {
        if ($mostrar == "1") {
          echo "222<br>";
        }
        $processar = $maximo;
      }
      if ($mostrar == "1") {
        echo("contasepara: $contasepara / separador: $separador / processar: $processar - tot: $totalprocessado / maximo: $maximo - size: " . sizeof($textos) . " <br>");
      }

      for ($quantsepara=0+$totalprocessado; $quantsepara < $processar+$totalprocessado; $quantsepara++) {
        $nomevar = "instrucao".($quantsepara+$comeca+1-$totalprocessado);
        global $$nomevar;
        $$nomevar = trim($textos[$quantsepara]);
        $contasepara++;
        if ($mostrar == "1") {
          echo "quantsepara: $quantsepara / $nomevar: " . $$nomevar . "<br>";
        }
        if ($separador == "04" and 1==2) {
          echo "maximo: $maximo - contasepara: $contasepara - quantsepara: $quantsepara - processar: $processar - total: $totalprocessado<br>";
          flush();
        }
      }
      $totalprocessado+=$processar;
      $quantidadegeral++;
      db_setaPropriedadesLayoutTxt($layout, $linha, $separador);
      if ($separador == "04" and 1==2) {
        echo "pula<br>";
        flush();
      }
      if ($maximo >= sizeof($textos)) {
        break;
      }
    }

    if ($mostrar == "1" and 1==2) {
      exit;
    }
  }
}
function db_formatatexto($linhas, $largura, $texto, $tipo="t") {
  //$linhas = numero de linhas (altura)
  //$largura= largura do texto
  //$texto  = texto a ser formatado
  //$tipo   = tipo ... t para texto e h para html

  if ($tipo=="t"){
    $quebra = "\n";
  }else{
    $quebra = "<br>";
  }

  $linha       = explode("\n",$texto);
  $numlinhas   = count($linha);
  $obs         ="";

  if($numlinhas>0){
    $linhatotal=0;
    for($i = 0; $i < $numlinhas; $i++){
      $linhatotal = $linhatotal + 1;
      $linhastam = strlen($linha[$i]);
      if($linhastam>$largura){
        $nlinhas = ($linhastam / $largura);
        $nlinhas1 = (int)$nlinhas;
        $linhatotal = $linhatotal + $nlinhas1;
        // $obs .= $linha[$i]."\n";

      }

      if($linhatotal>=$linhas){
        if($linhastam>$largura){
          $obs .= substr($linha[$i], 1, $largura);
          $obs .= "...";
        }else{
          $obs .= $linha[$i]."...";
        }
        break;
      }else{
        $obs .= $linha[$i]."$quebra";
      }
    }

  }
  return $obs;
}


// Faz os devidos "Escapes" na string a ser utilizada em javascript
function db_jsspecialchars($s) {
  //$s = string a ser tratada
  return preg_replace('/([^ !#$%@()*+,-.\x30-\x5b\x5d-\x7e])/e',
    "'\\x'.(ord('\\1')<16? '0': '').dechex(ord('\\1'))",$s);
}


function monta_menu($item_modulo,$id_modulo,$espacos,$lista, $iUsuario = false){

  $sOrdenacao = DBMenu::getCampoOrdenacao();
  global $matriz_item , $matriz_item_seleciona ;

  $iAnoUsu      = self::db_getsession('DB_anousu', false);
  $iInstituicao = self::db_getsession('DB_instit', false);

  $sql  = "select id_item_filho,descricao ,funcao                         ";
  $sql .= "  from db_menu m                                               ";
  $sql .= "       inner join db_itensmenu i on i.id_item = id_item_filho  ";
  $sql .= "  where m.id_item = $item_modulo                               ";
  $sql .= "    and m.modulo  = $id_modulo                                 ";
  $sql .= "  order by {$sOrdenacao} asc                                     ";

  if ($iUsuario and ($iUsuario != 1 || self::db_getsession("DB_administrador") != 1)) {

    $sql  = "  select id_item_filho,descricao ,funcao                                                  ";
    $sql .= "    from db_menu m                                                                        ";
    $sql .= "         inner join db_permissao p on p.id_item = m.id_item_filho                         ";
    $sql .= "         inner join db_itensmenu i on i.id_item = m.id_item_filho                         ";
    $sql .= "                                  and p.permissaoativa = '1'                              ";
    $sql .= "                                  and p.anousu = $iAnoUsu                                 ";
    $sql .= "                                  and p.id_instit =$iInstituicao                          ";
    $sql .= "                                  and p.id_modulo = $id_modulo                            ";
    $sql .= "   where p.id_usuario = $iUsuario                                                         ";
    $sql .= "      and m.id_item   = $item_modulo                                                      ";
    $sql .= "      and m.modulo    = $id_modulo                                                        ";
    $sql .= "      and i.itemativo = '1'                                                               ";
    $sql .= " union                                                                                    ";
    $sql .= "  select id_item_filho,descricao ,funcao                                                  ";
    $sql .= "    from db_menu m                                                                        ";
    $sql .= "         inner join db_permherda h on h.id_usuario     = $iUsuario                        ";
    $sql .= "         inner join db_usuarios  u on u.id_usuario     = h.id_perfil                      ";
    $sql .= "                                  and u.usuarioativo   = '1'                              ";
    $sql .= "         inner join db_permissao p on p.id_item        = m.id_item_filho                  ";
    $sql .= "         inner join db_itensmenu i on i.id_item        = m.id_item_filho                  ";
    $sql .= "                                  and p.permissaoativa = '1'                              ";
    $sql .= "                                  and p.anousu         = $iAnoUsu                         ";
    $sql .= "                                  and p.id_instit      = $iInstituicao                    ";
    $sql .= "                                  and p.id_modulo      = $id_modulo                       ";
    $sql .= "  where p.id_usuario = h.id_perfil                                                        ";
    $sql .= "    and m.id_item    = $item_modulo                                                       ";
    $sql .= "    and m.modulo     = $id_modulo                                                         ";
    $sql .= "    and i.itemativo = '1'                                                                 ";
  }

  $res = self::db_query($sql);
  if(pg_numrows($res)>0){

    for($i=0;$i<pg_numrows($res);$i++){
      $item_filho= pg_result($res,$i,0);
      $descricao = pg_result($res,$i,1);
      $funcao    = trim(pg_result($res,$i,2));
      if( empty($lista) || isset( $lista[$item_filho]) ){
        $matriz_item_seleciona[count($matriz_item_seleciona)] = $espacos."-".$item_filho;
      }
      $matriz_item[count($matriz_item)] = $espacos;
      if($funcao == ""){
        monta_menu($item_filho,$id_modulo,$espacos."-".$item_filho,$lista, $iUsuario);
      }
    }

  }

}

function db_strtotime($strData){

  if(empty($strData)) {
    return $strData;
  }

  if (substr(phpversion(),0,1) == 4) {
    return strtotime($strData,date('h:i'));
  } else if (substr(phpversion(),0,1) >= 5) {
    return(strtotime($strData));
  }
}


function db_getnomelogo(){

  $rsLogo = self::db_query("select logo
                            from db_config
                                 left join db_tipoinstit on db21_codtipo = db21_tipoinstit
                                                         where codigo = ".self::db_getsession("DB_instit"));
  if($rsLogo == false || pg_num_rows($rsLogo) == 0 ){
    return false;
  }else{
    return pg_result($rsLogo,0,"logo");
  }

}


function db_dataextenso( $timestamp=null, $sMunic=null ){

  $aMeses = array("01" => "Janeiro",
                  "02" => "Fevereiro",
                  "03" => "Março",
                  "04" => "Abril",
                  "05" => "Maio",
                  "06" => "Junho",
                  "07" => "Julho",
                  "08" => "Agosto",
                  "09" => "Setembro",
                  "10" => "Outubro",
                  "11" => "Novembro",
                  "12" => "Dezembro" );

  if ( $timestamp == null ) {
    $timestamp = self::db_getsession('DB_datausu');
  }

  if ( $sMunic == null and  $sMunic <> "" ) {
    $sSqlMunic = "select munic from db_config where codigo = ".self::db_getsession('DB_instit')." limit 1";
    $sMunic    = pg_result( self::db_query( $sSqlMunic ),0,'munic' );
  }

  $sData = ($sMunic == ""?"":ucfirst(strtolower( $sMunic ) ).", ").date('d',$timestamp)." de ".$aMeses[date('m',$timestamp)]." de ".date('Y',$timestamp).".";
  return $sData;

}

function db_geraArquivoOid ($arquivo,$arquivoAlt=null,$opcao=1,$conn){
  /*
   * $arquivo    => o arquivo do type "file", o arquivo a ser gravado
   * $arquivoAlt => o arquivo ja existente no banco,
   *                a opção alterar a função altera do $arquivoAlt para o $arquivo
   *                na opção excluir a função exclui este arquivo
   * $opcao      => 1 = incluir, 2= alterar, 3 = excluir
   * $conn       => conexão com banco
   */

  if($opcao==2){
    pg_lo_unlink($conn, $arquivoAlt);
  }
  if($opcao==3){
    pg_lo_unlink($conn, $arquivoAlt);
    return "null";
  }

  $nomeArquivo        = $_FILES["$arquivo"]["name"];
  $localRecebeArquivo = $_FILES["$arquivo"]["tmp_name"];

  if ( trim($localRecebeArquivo) != "") {
    $arquivoGrava = fopen($localRecebeArquivo, "rb");
    if ($arquivoGrava == false) {
      throw new Exception("Erro arquivo a gravar ");
      //echo "erro aruivograva";
      //exit;
    }
    $dados = fread($arquivoGrava, filesize($localRecebeArquivo));
    if ($dados == false) {
      throw new Exception("Erro fread ");
      //echo "erro fread";
      //exit;
    }
    fclose($arquivoGrava);
    $oidgrava = pg_lo_create();
    if ($oidgrava == false) {
      throw new Exception("Erro pg_lo_create ");
      //echo "erro pg_lo_create";
      //exit;
    }


    $objeto = pg_lo_open($conn, $oidgrava, "w");
    if ($objeto != false) {
      $erro = pg_lo_write($objeto, $dados);
      if ($erro == false) {
        throw new Exception("Erro pg_lo_write ");
        //echo "erro pg_lo_write";
        //exit;
      }
      pg_lo_close($objeto);
    } else {
      throw new Exception("Operação Cancelada! ");
      //$erro_msg ("Operação Cancelada!!");
      //$sqlerro = true;
    }

    return $oidgrava;
  }

}

function db_buscaImagemBanco($cadban,$conn){
  /*
   * $cadban = codigo k15_codigo da cadban
   * $conn   =  conexão
   */

  $sqlcodban = "select k15_codbco from cadban where k15_codigo = $cadban";
  $resultcadban = self::db_query($sqlcodban);
  $linhascadban = pg_num_rows($resultcadban);
  if($linhascadban >0){
    //db_fieldsmemory($resultcadban,0);
    $k15_codbco = pg_result($resultcadban,0,"k15_codbco");
    $banco = str_pad($k15_codbco, 3, "0", STR_PAD_LEFT);
    // busca os dados do banco..logo etc
    $sqlBanco = "select  * from db_bancos where db90_codban = '".$banco."'";
    $resultBanco = self::db_query($sqlBanco);
    $linhasBanco = pg_num_rows($resultBanco);
    if($linhasBanco > 0 ){
      //db_fieldsmemory($resultBanco,0);
      $db90_digban = pg_result($resultBanco,0,"db90_digban");
      $db90_abrev  = pg_result($resultBanco,0,"db90_abrev");
      $db90_logo   = pg_result($resultBanco,0,"db90_logo");
      // se não tiver os dados do banco na db_bancos não deve emitir o recibo.
      if($db90_digban=="" || $db90_abrev=="" || $db90_logo==""){
        return false;
//      db_redireciona('db_erros.php?fechar=true&db_erro=Configure os dados(Digito verificador, Nome abreviado do banco e o Arquivo do logo) do Banco: '.$banco.'-'.$db90_descr.', no Cadastro de Bancos');
      }
      // seta os dados para o boleto passando as informações do logo
      self::db_query ($conn, "begin");
      $caminho = "tmp/".$banco.".jpg";
      pg_lo_export  ( "$db90_logo",$caminho ,$conn);
      self::db_query ($conn, "commit");

      $arr = array("numbanco"  =>$banco."-".$db90_digban,
                   "banco"     =>$db90_abrev ,
                   "imagemlogo"=>$caminho);

      return $arr;

    }else{
      // se não tiver o banco na db_bancos
      db_redireciona('db_erros.php?fechar=true&db_erro=Não existe Banco cadastrado para o código'.$banco.' no Cadastro de Bancos'.$sqlBanco);
    }
  }
}

function verifica_bissexto($ano){

  if ($ano%4 == 0) {
    if ($ano%100 != 0) {
      return true;
    } else {
      if ($ano%400 == 0) {
        return true;
      } else {
        return false;
      }
    }
  }
  return false;
}


function verifica_ultimo_dia_mes($data){

  $iDia = substr($data,0,2);
  $iMes = substr($data,3,2);
  $iAno = substr($data,6,4);

  $lBisexto = verifica_bissexto($data);

  if ($lBisexto) {
    $iFev = 29;
  } else {
    $iFev = 28;
  }

  $aUltimoDia = array("01"=>"31",
                      "02"=>$iFev,
                      "03"=>"31",
                      "04"=>"30",
                      "05"=>"31",
                      "06"=>"30",
                      "07"=>"31",
                      "08"=>"31",
                      "09"=>"30",
                      "10"=>"31",
                      "11"=>"30",
                      "12"=>"31");
  if ($aUltimoDia[$iMes] == $iDia) {
    return true;
  } else {
    return false;
  }

}
/**
 * retorna o numero de meses entre o intervalo dado
 *
 * @param string $dataini
 * @param string  $datafim
 * @return integer
 */
function conta_meses($dataini, $datafim) {

  $res_meses = self::db_query("select fc_conta_meses('$dataini','$datafim') as totalmeses");
  $oMeses    = db_utils::fieldsMemory($res_meses, 0);
  return $oMeses->totalmeses;

}
/**
 * Funcao para compactar arquivos apartir de um array com os nomes dos arquivos
 *
 * @param array()      $aArquivosCompactar   array com o nome dos arquivos a serem compactados
 * @param string       $sNomeAbsoluto        nome do arquivo a ser gerado
 * @param string       $sTipoCompactacao     string identificando o tipo de compactação ('zip') implementado apenas zip
 */
function compactaArquivos($aArquivosCompactar=array(),$sNomeAbsoluto="",$sTipoCompactacao="zip") {

  switch ($sTipoCompactacao) {
    case 'zip':
      $sArquivos = "";
      foreach($aArquivosCompactar as $sArquivo) {
        $sArquivos .= " $sArquivo";
      }
      system("rm -f tmp/{$sNomeAbsoluto}.zip");
      system("cd tmp/; ../bin/zip -q {$sNomeAbsoluto}.zip $sArquivos 2> /tmp/erro.txt ; cd ..");
      break;
    case 'tar':
      return false;
      break;
  }

}

// Funcao ROUND personalizada
function db_round($valor, $decimais=0) {

  if((int)$valor == $valor) {
    return $valor;
  }

  return round($valor, $decimais);
}

function db_buscaImagemInstituicao($instit,$tipo){
  /**
   * $instit código da instituição {self::db_getsession("DB_instit")}
   * $tipo 1 - para logo 2 - para figura
   * $conn conexao com o banco
   */
  global $conn;
  $sSqlConfigArquivos  = "select db38_arquivo ";
  $sSqlConfigArquivos .= "  from db_configarquivos ";
  $sSqlConfigArquivos .= " where db38_instit = $instit ";
  $sSqlConfigArquivos .= "   and db38_tipo   = $tipo ";
  $rsSqlConfigArquivos = self::db_query($sSqlConfigArquivos);
  $iNumRows            = pg_numrows($rsSqlConfigArquivos);
  if ($iNumRows > 0) {

    $arquivo = pg_result($rsSqlConfigArquivos,0,"db38_arquivo");
    $caminho = "tmp/".$arquivo.".jpg";
    self::db_query($conn,"begin");
    pg_lo_export($conn,$arquivo,$caminho);
    self::db_query($conn,"commit");
    return $caminho;
  }else{
    return null;
  }
}

/**
 * Removeção acentução da string informada como parâmetro .
 *
 * @param string $sRemover
 * @return string
 */
function db_removeAcentuacao($sRemover){

  $var = $sRemover;

  $var = ereg_replace("[ÁÀÂÃ]","A",$var);
  $var = ereg_replace("[áàâãª]","a",$var);
  $var = ereg_replace("[ÉÈÊ]","E",$var);
  $var = ereg_replace("[éèê]","e",$var);
  $var = ereg_replace("[íì]","i",$var);
  $var = ereg_replace("[ÍÌ]","I",$var);
  $var = ereg_replace("[ÓÒÔÕ]","O",$var);
  $var = ereg_replace("[óòôõº]","o",$var);
  $var = ereg_replace("[ÚÙÛ]","U",$var);
  $var = ereg_replace("[úùû]","u",$var);
  $var = str_replace("Ç","C",$var);
  $var = str_replace("ç","c",$var);

  return $var;
}

/**
 * @name função db_tempodecorrido
 * @desc retorna o tempo gasto formatado entre duas datas
 * @param timestamp do primeiro tempo
 * @param timestamp do segundo tempo
 */
function db_formatatempodecorrido($timestampAntes, $timestampDepois){

  //string de retorno
  $sRetorno = '';

  //diferença entre as datas em segundos
  $iRestaSegundos = $timestampDepois-$timestampAntes;

  //quantidade de anos
  $anos = $iRestaSegundos/31536000;

  //se houver anos
  $iAnos = floor($anos);
  if ($iAnos >= 1) {

    //mostra quantos anos passaram
    $sRetorno .= ($iAnos == 1) ? $iAnos.' ano, ' : $iAnos.' anos, ';

    //retira do total, o tempo em segundos dos anos passados
    $iRestaSegundos = $iRestaSegundos-($iAnos*31536000);
  }

  //quantidade de meses (anos/12 e não dias*30)
  $ises = $iRestaSegundos/2628000;

  //se houver meses
  $iMeses = floor($ises);
  if ($iMeses >= 1) {

    //mostra quantos meses passaram
    $sRetorno .= ($iMeses == 1) ? $iMeses.' mês, ' : $iMeses.' meses, ';

    //retira do total, o tempo em segundos dos meses passados
    $iRestaSegundos = $iRestaSegundos-($iMeses*2628000);
  }

  //quantidade de semanas
  $semanas = $iRestaSegundos/604800;

  //se houver semanas
  $iSemanas = floor($semanas);
  if ($iSemanas >= 1) {

    //mostra quantas semanas passaram
    $sRetorno .= ($iSemanas == 1) ? $iSemanas.' semana, ' : $iSemanas.' semanas, ';

    //retira do total, o tempo em segundos das semanas passados
    $iRestaSegundos = $iRestaSegundos-($iSemanas*604800);
  }

  //quantidade de dias
  $dias = $iRestaSegundos/86400;

  //se houver dias
  $iDias = floor($dias);
  if ($iDias >= 1) {

    //mostra quantos dias passaram
    $sRetorno .= ($iDias == 1) ? $iDias.' dia, ' : $iDias.' dias, ';

    //retira do total, o tempo em segundos dos dias passados
    $iRestaSegundos = $iRestaSegundos-($iDias*86400);
  }

  //quantidade de horas
  $horas = $iRestaSegundos/3600;

  //se houver horas
  $iHoras = floor($horas);
  if ($iHoras >= 1) {

    //mostra quantas horas passaram
    $sRetorno .= ($iHoras == 1) ? $iHoras.' hora, ' : $iHoras.' horas, ';

    //retira do total, o tempo em segundos das horas passados
    $iRestaSegundos = $iRestaSegundos-($iHoras*3600);
  }

  //quantidade de minutos
  $minutos = $iRestaSegundos/60;

  //se houver minutos
  $iMinutos = floor($minutos);
  if ($iMinutos >= 1) {

    //mostra quantos minutos passaram
    $sRetorno .= ($iMinutos == 1) ? $iMinutos.' minuto, ' : $iMinutos.' minutos, ';

    //retira do total, o tempo em segundos dos minutos passados
    $iRestaSegundos = $iRestaSegundos-($iMinutos*60);
  }

  //mostra quantos minutos passaram
  if($iRestaSegundos >= 0) {
    $sRetorno .= (ceil($iRestaSegundos) == 1) ? ceil($iRestaSegundos).' segundo, ' : ceil($iRestaSegundos).' segundos, ';
  }

  //retira a ultima virgula
  $sRetorno = rtrim($sRetorno, ', ');

  //coloca "e" no lugar da ultima virgula
  $arrExplode = explode(',', $sRetorno);
  $sRetornoFinal = '';
  $nPedacos = count($arrExplode);
  for ($i=0; $i<$nPedacos; $i++) {
    if ($i == ($nPedacos-1)) {
      $sRetornoFinal .= ($i <> 0) ? ' e '.$arrExplode[$i] : $arrExplode[$i];

    } else if ($i == ($nPedacos-2)) {
      $sRetornoFinal .= $arrExplode[$i];
    } else {
      $sRetornoFinal .= $arrExplode[$i].',';
    }
  }

  //retorna o tempo decorrido formatado
  return $sRetornoFinal;
}

/*
 * retorna o mes abreviado conforme numero do mes recebido por parametro no formato ex: '01' = JAN ...
 */
function db_mesAbreviado ($iMes){
  $aMesAbreviado = array( "01" => "Jan",
                          "02" => "Fev",
                          "03" => "Mar",
                          "04" => "Abr",
                          "05" => "Mai",
                          "06" => "Jun",
                          "07" => "Jul",
                          "08" => "Ago",
                          "09" => "Set",
                          "10" => "Out",
                          "11" => "Nov",
                          "12" => "Dez");
  return $aMesAbreviado[$iMes];
}

/**
 * Função que analisa se o plano de contas PCASP esta ativado e então altera o nome das tabelas envolvido
 * @param string $sQuery
 * @throws Exception
 */
function analiseQueryPlanoOrcamento($sQuery, $iAnoUsu = null)  {

  if (empty($iAnoUsu)) {
    $iAnoUsu = self::db_getsession("DB_anousu");
  }

  $aTablesFrom = array(" conplano ",
                       "conplano.",
                       "conplanoreduz",
                       "conplanoconta",
                       "conplanocontabancaria",
                       "orccenarioeconomicoconplano ",
                       "fc_conplano_grupo"
  );

  $aTablesTo  = array(" conplanoorcamento ",
                      "conplanoorcamento.",
                      "conplanoorcamentoanalitica",
                      "conplanoorcamentoconta",
                      "conplanoorcamentocontabancaria",
                      "orccenarioeconomicoconplanoorcamento ",
                      "fc_conplanoorcamento_grupo"
  );
  /**
   * incluido um or no if da constante use pecasp, para verificar tambem a variavel de seção
   * para fazer o parse das tabelas migradas
   * ajuste provisorio para que seja possivel a visualização dos relatorios e quadros de
   * estimativas de receitas para o ano de 2012.
   * ativamos para 't' no RPC que chama o metodo getQuadroEstimativa
   * @todo remover || $_SESSION["DB_use_pcasp"] == 't'  do if abaixo
   */
  if ( $_SESSION["DB_use_pcasp"] == "t" || (USE_PCASP && $iAnoUsu > 2012) ) {
    $sQuery = str_replace($aTablesFrom, $aTablesTo, $sQuery);
  }

  return $sQuery;
}

/**
 *  funcao para corrigir o erro do php 2, relacionado a arredondamento
 *  mais informações sobre o bug: https://bugs.php.net/bug.php?id=44223
 */
function dbround_php_52($nValor, $iCasas = 0) {

  return round($nValor, $iCasas);

  // return sprintf("%.{$iCasas}f", round($nValor, $iCasas));
}


/**
 * Retorna a mensagem solicitada
 * @param string sCaminhoMensagem caminho de mensagem
 * @param stdclass oVariaveis objeto literal com as variaveis que devem ser substituidas
 * @example _M('configuracao.mensagem.con4_mensagem001.mensagem_nao_informada');
 *          Aonde: DBPortal. <-area
 *                 configuracao <- modulo
 *                 con4_mensagem001<- Programa
 *                 mensagem_nao_informada <- mensagem que deve ser exibida
 * @returns {string} texto da mensagem
 */
static function _M($sCaminhoMensagem, $oOpcoes = null) {
  return DBMensagem::getMensagem($sCaminhoMensagem, $oOpcoes);
}


function findword($sText, $sWord) {

  if (empty($sWord)) {
    return false;
  }
  $sText = str_replace(";", " ", $sText);
  return in_array($sWord, explode(" ", $sText));
}

function utf8_encode_all($entrada) {
  return DBString::utf8_encode_all($entrada);
  return $entrada;
}

function urlencode_all($entrada) {
  return DBString::urlencode_all($entrada);
}

/**
 *
 * Funcao para pegar um último ID (PostgreSQL)
 *
 */
public static function lastInsertId() {
  return db_conecta::lastInsertId();
}


/**
 *
 * Funcao para registrar atividade na tabela db_usuariosonline.
 *
 */
public function log_db_usuariosonline($tipo, $msg) {
  
  $hora = time();
  $ip   = $_SERVER['REMOTE_ADDR'];


  if ( $tipo == 'insert' ) {
    self::db_query("INSERT INTO 
                                db_usuariosonline 
                          VALUES 
                              ( ".self::db_getsession("DB_id_usuario")."
                                ,".$hora."
                                ,'".$ip."'
                                ,'".self::db_getsession("DB_login")."'
                                ,'".$msg."'          
                                ,''
                                ,".$hora."
                                ,' ')");
  } else if ( $tipo == 'update' ) {
    self::db_query("UPDATE 
                            db_usuariosonline
                        SET 
                            uol_arquivo = ''
                            ,uol_modulo = '".$msg."'
                            ,uol_inativo = ".time()."
                        WHERE uol_id = ".self::db_getsession("DB_id_usuario")."
                            and uol_ip = '".$ip."'
                            and uol_hora = ".self::db_getsession("DB_uol_hora")) or die("Erro(26) atualizando db_usuariosonline");
  }
  
}


}