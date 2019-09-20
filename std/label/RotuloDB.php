<?php
/**
 * Esta classe gera as variáveis de controle do sistema de uma determinada tabela.
 */

namespace std;

use libs\db_stdlib;

//class rotulo_original {
class RotuloDB {
  //|00|//rotulo
  //|10|//Esta classe gera as variáveis de controle do sistema de uma determinada tabela
  //|15|//[variavel] = new \std\rotulo($tabela);
  //|20|//tabela  : Nome da tabela a ser pesquisada
  //|40|//Gera todas as variáveis de controle dos campos
  //|99|//
  var $tabela;

  function __construct($tabela) {
    $this->tabela = $tabela;
  }

  function rlabel($nome = "") {
    //#00#//rlabel
    //#10#//Este método gera o label do campo ou campos para relatório
    //#15#//rlabel($nome);
    //#20#//nome  : Nome do campo a ser gerado o label para relatório
    //#20#//        Se não for informado campo, será gerado de todos os campos
    //#40#//Gera a variável label do relatorio do campo rotulorel
    //#99#//A variável será o "RL" mais o nome do campo
    //#99#//Exemplo : campo z01_nome ficará RLz01_nome
    $sCampoTrim = trim($nome);
    $result     = db_stdlib::db_query("SELECT 
                                              c.rotulorel
                                          FROM db_syscampo c
                                              inner join db_sysarqcamp s on s.codcam = c.codcam
                                              inner join db_sysarquivo a on a.codarq = s.codarq
                                          WHERE 
                                              a.nomearq = '".$this->tabela."'
                                              ". ($sCampoTrim != "" ? "and c.nomecam = '${sCampoTrim}'" : ""));
    foreach ($result as $linha) {
      //  Variável para colocar como label de campo.
      $GLOBALS["RL".trim($linha->nomecam)]  = ucfirst(trim($linha->rotulorel));
    }
  }
  function label($nome = "") {
    //#00#//label
    //#10#//Este método gera o label do arquivo ou de um campo para os formulários
    //#15#//label($nome);
    //#20#//nome  : Nome do campo a ser gerado as variáveis de controle
    //#20#//        Se não informado o campo, será gerado de todos os campos
    //#99#//Nome das variáveis geradas:
    //#99#//"I" + nome do campo -> Tipo de consistencia javascript a ser gerada no formulário (|aceitatipo|)
    //#99#//"A" + nome do campo -> Variavel para determinar o autocomplete no objeto (!autocompl|)
    //#99#//"U" + nome do campo -> Variavel para preenchimento obrigatorio do campo (|nulo|)
    //#99#//"G" + nome do campo -> Variavel para colocar se letras do objeto devem ser maiusculo ou não (|maiusculo|)
    //#99#//"S" + nome do campo -> Variavel para colocar mensagem de erro do javascript de preenchimento de campo (|rotulo|)
    //#99#//"L" + nome do campo -> Variavel para colocar como label de campo (|rotulo|)
    //#99#//                       Coloca o campo com a primeira letra maiuscula e entre tags strong (negrito) (|rotulo|)
    //#99#//"T" + nome do campo -> Variavel para colocat na tag title dos campos (|descricao|)
    //#99#//"M" + nome do campo -> Variavel para incluir o tamanho da propriedade maxlength dos campos (|tamanho|)
    //#99#//"N" + nome do campo -> Variavel para controle da cor de fundo quando o  campo aceitar nulo (|nulo|)
    //#99#//                       style="background-color:#E6E4F1";
    //#99#//"RL"+ nome do campo -> Variavel para colocar como label de campo nos relatorios
    //#99#//"TC"+ nome do campo -> Variavel com o tipo de campo do banco de dados

    //        $result = pg_exec("select c.descricao,c.rotulo,c.nomecam,c.tamanho,c.nulo,c.maiusculo,c.autocompl,c.conteudo,c.aceitatipo,c.rotulorel
    //                                   from db_syscampo c
    //                                                   inner join db_sysarqcamp s
    //                                                   on s.codcam = c.codcam
    //                                                   inner join db_sysarquivo a
    //                                                   on a.codarq = s.codarq
    //                                                   where a.nomearq = '".$this->tabela."'
    //                                                   ". ($nome != "" ? "and trim(c.nomecam) = trim('$nome')" : ""));
    $sCampoTrim = trim($nome);
    $result     = db_stdlib::db_query("SELECT 
                                            c.descricao
                                            ,c.rotulo
                                            ,c.nomecam
                                            ,c.tamanho
                                            ,c.nulo
                                            ,c.maiusculo
                                            ,c.autocompl
                                            ,c.conteudo
                                            ,c.aceitatipo
                                            ,c.rotulorel
                                        FROM db_sysarquivo a
                                              INNER JOIN db_sysarqcamp s on s.codarq = a.codarq
                                              INNER JOIN db_syscampo c on c.codcam = s.codcam
                                        WHERE a.nomearq = '".$this->tabela."'
                                        ". ($sCampoTrim != "" ? "and c.nomecam = '${sCampoTrim}'" : ""));
    foreach ($result as $linha) {
      /// variavel com o tipo de campo
      $GLOBALS[trim("I".$linha->nomecam )]  = $linha->aceitatipo;

      /// variavel para determinar o autocomplete
      if ($linha->autocompl == 'f') {
        $GLOBALS[trim("A".$linha->nomecam)]  = "off";
      } else {
        $GLOBALS[trim("A".$linha->nomecam)]  = "on";
      }

      /// variavel para preenchimento obrigatorio
      $GLOBALS[trim("U".$linha->nomecam)]  = $linha->nulo;
      /// variavel para colocar maiusculo
      $GLOBALS[trim("G".$linha->nomecam)]  = $linha->maiusculo;
      /// variavel para colocar no erro do javascript de preenchimento de campo
      $GLOBALS[trim("S".$linha->nomecam)]  = $linha->rotulo;
      /// variavel para colocar como label de campo
      $GLOBALS[trim("L".$linha->nomecam)]  = "<strong>".ucfirst($linha->rotulo).":</strong>";
      /// variavel para colocar como label de campo
      $GLOBALS[trim("LS".$linha->nomecam)]  = ucfirst($linha->rotulo);
      /// vaariavel para colocat na tag title dos campos
      $GLOBALS[trim("T".$linha->nomecam)]  = ucfirst($linha->descricao)."\n\nCampo:".$linha->nomecam;
      /// variavel para incluir o tamanhoda tag maxlength dos campos
      $GLOBALS[trim("M".$linha->nomecam)]  = $linha->tamanho;
      /// variavel para controle de campos nulos
      $GLOBALS[trim("N".$linha->nomecam)]  = $linha->nulo;
      
      /*if ($$variavel == "t")
      $$variavel = "style=\"background-color:#E6E4F1\"";
      else
      $$variavel = "";*/
      
      /// variavel para colocar como label de campo nos relatorios
      $GLOBALS[trim("RL".$linha->nomecam)]  = ucfirst($linha->rotulorel);
      /// variavel para colocar o tipo de campo
      $GLOBALS["TC".trim($linha->nomecam)]  = $linha->conteudo;
    }
  }
  function tlabel($nome = "") {
    //#00#//tlabel
    //#10#//Este método gera o label do arquivo
    //#15#//tlabel($nome);
    //#20#//nome  : Nome do arquivo para ser gerado o label
    //#40#//Gera a variável label do arquivo "L" + nome do arquivo
    //#99#//Variáveis geradas:
    //#99#//"L" + nome do arquivo -> Label do arquivo
    //#99#//"T" + nome do arquivo -> Texto para a tag title

    $result = db_stdlib::db_query("select c.nomearq,c.descricao,c.nomearq,c.rotulo
                         from db_sysarquivo c
                        where c.nomearq = '".$this->tabela."'");
    if ( $result->rowCount() > 0 ) {
      $result   = $result->fetch();

      $GLOBALS[trim("L".$result->nomearq)]  = "<strong>".$result->rotulo.":</strong>";
      $GLOBALS[trim("T".$result->nomearq)]  = $result->descricao;
    }
  }
  //|XX|//
}
