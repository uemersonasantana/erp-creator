<?php
/**
 * Classe que verifica se o boletim já foi liberado
 *
 * @package     libs
 * @subpackage  class
 * @author      Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

class cl_verificaboletim {
  function cl_verificaboletim($clboletim) {
    global $k11_numbol;
    $data = date("Y-m-d", db_getsession("DB_datausu"));
    $instit = db_getsession("DB_instit");
    $result = $clboletim->sql_record($clboletim->sql_query_file(null, null, "k11_numbol", "", "k11_data='$data' and k11_instit=$instit and k11_libera = 't'"));
    $numrows = $clboletim->numrows;
    if ($numrows > 0) {
      db_fieldsmemory($result, 0);
      db_redireciona("db_erromenu.php?k11_numbol=$k11_numbol");
    }
  }
}