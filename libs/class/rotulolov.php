<?php
/**
 * rotulolov
 *
 * @package     libs
 * @subpackage  class
 * @author      Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

/**
 * @todo - Implementar
 */
class rotulolov {
  //|00|//rotullov
  //|10|//Esta classe gera as variáveis para titulo, descricao e tamanho para rotina |db_lovrot|
  //|15|//[variavel] = new rotulolov;
  //|30|//titulo    : Propriedade que recebe o conteudo do campo |rotulo| da documentação
  //|30|//descricao : Propriedade que recebe o conteudo do campo |descricao| da documentação
  //|30|//tamanho   : Propriedade que recebe o conteudo do campo |tamanho| da documentação
  //|99|//Exemplo:
  //|99|//[variavel] = new rotulolov;
  //|99|//[variavel]->label("z01_nome");
  //|99|//[variavel]->titulo    // ja com o valor atulizado pelo método label
  //|99|//[variavel]->descricao // ja com o valor atulizado pelo método label
  //|99|//[variavel]->tamanho   // ja com o valor atulizado pelo método label
  var $titulo = null;
  var $title = null;
  var $tamanho = null;
  function label($nome = "") {
    //#00#//label
    //#10#//Este método gera o label do campo para a funcao |db_lovrot|
    //#15#//label($campo);
    //#20#//nome  : Nome do campo a ser gerado as variáveis de controle para função
    //#99#//Caso os campos comecem com dl_ não será pesquisada o label da documentação e sim o próprio
    //#99#//nome da variável sem o "dl_"
    if (substr($nome, 0, 3) == "dl_") {
      $this->titulo = substr($nome, 3);
      $this->title = substr($nome, 3);
      $this->tamanho = 0;
    } else {
      $sCampoTrim = trim($nome);
      $result = db_query("select c.descricao,c.rotulo,c.tamanho
                           from db_syscampo c
                          where c.nomecam = '${sCampoTrim}'");
      $numrows = pg_numrows($result);
      if ($numrows != 0) {
        $this->titulo = ucfirst(pg_result($result, 0, "rotulo"));
        $this->title = ucfirst(pg_result($result, 0, "descricao"));
        $this->tamanho = pg_result($result, 0, "tamanho");
      } else {
        $this->titulo = "";
        $this->title = "";
        $this->tamanho = "";
      }
    }
  }
  //|XX|//
}