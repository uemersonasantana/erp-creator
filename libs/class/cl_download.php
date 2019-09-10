<?php
/**
 * Classe para download de arquivos
 *
 * @package     libs
 * @subpackage  class
 * @author      Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

class cl_download {
  var $diretorio = 'tmp/';
  var $arquivo = null;
  var $texto = "Clique Aqui";
  function cl_download() {
    $this->criaarquivo();
  }
  function criaarquivo() {
    $this->arquivo = "rp".rand(1, 10000)."_".time();
  }
  function download() {
    echo "<a href='db_download.php?arquivo=".$this->diretorio.$this->arquivo."'>".$this->texto."</a>";
  }
}