<?php

$db_funcoes         =  new dbforms\db_funcoes;

$erro     = false;
$db_opcao = 1;

function xx($fd, $parq) {
  $fk = $db_stdlib->db_query("SELECT a.nomearq,c.nomecam,f.sequen,f.referen,c.codcam 
                    FROM db_sysforkey f 
                      INNER JOIN db_sysarquivo a on a.codarq = f.codarq 
                      INNER JOIN db_syscampo c   on c.codcam = f.codcam 
                    WHERE a.codarq = ".$parq." 
                      ORDER BY f.referen,f.sequen");
  $encerra = false;
  $qalias = "";
  if ( $fk->rowCount() > 0 ) {
    $Nfk = $fk->rowCount();
    $arq = 0;
    $virgula = "";
    foreach ( $fk as $linha ) {
      if( $arq != $linha->referen ) {
        if($virgula != "") {
          fputs($fd,'";'."\n");
        }
        $arq  = $linha->referen;
        $qarq = $db_stdlib->db_query("SELECT nomearq FROM db_sysarquivo WHERE codarq = ".$arq)->fetch();
        
        if ( strpos($GLOBALS["temalias"],trim($qarq->nomearq)) > 0 ) {
          if ( strpos($GLOBALS["qualalias"],"a") > 0 ) {
            if ( strpos($GLOBALS["qualalias"],"b") > 0 ) {
              if ( strpos($GLOBALS["qualalias"],"c") > 0 ) {
               $qalias="d";
              } else {
                $qalias="c";
                $GLOBALS["qualalias"] .=  "-c";
              }
            } else {
              $qalias="b";
              $GLOBALS["qualalias"] .=  "-b";
            }
          } else {
            $qalias="a";
            $GLOBALS["qualalias"] = "-a";
          }
        } else {
          $qalias ="";
        }
        $GLOBALS["temalias"] .= "-".trim($qarq->nomearq);
        fputs($fd,'     $sql .= "      inner join '.trim($qarq->nomearq)." ".($qalias==""?"":" as ".$qalias)." on ");
        $virgula = "";
      }
      $qk = $db_stdlib->db_query("SELECT 
                          q.nomecam 
                        FROM db_sysprikey p
                          INNER JOIN db_syscampo q on q.codcam = p.codcam 
                        WHERE codarq = ".$arq." and
                          sequen = ".$linha->sequen);

      fputs($fd,$virgula.' '.($qalias==""?trim($qarq->nomearq):" ".$qalias).'.'.trim(pg_result($qk,0,0))." = ".trim($linha->nomearq).".".trim($linha->nomecam));
      $encerra = true;
      $virgula = " and ";
    }
  }   
  if($encerra==true){
      fputs($fd,'";'."\n");
  }
}
?>
<div class="card-content collapse show">

<?php if ( $erro == true ) { ?>
<div class="alert alert-danger mb-2" role="alert"><strong>Atenção!</strong> <?php echo $cl_syscampo->erro_msg; ?></div>
<?php } 

include 'forms/frm_sys4_criaclasse001.php';

?>
</div>