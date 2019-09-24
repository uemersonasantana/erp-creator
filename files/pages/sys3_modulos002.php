<?php
header('Content-Type: text/html; charset=utf-8');

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$db_conecta         =   new libs\db_conecta; 
$db_stdlib          =   new libs\db_stdlib;
$db_usuariosonline  =   new libs\db_usuariosonline;

//  Pega um vetor e cria variáveis globais pelo índice do vetor.
$db_stdlib->db_postmemory($_REQUEST);

$exercicio  =   $db_stdlib->db_getsession("DB_anousu");
$borda      =   1; 
$bordat     =   1;
$preenc     =   0;
$TPagina    =   57;

///////////////////////////////////////////////////////////////////////

//$exercicio = 2003;
$xmod = '';
if (isset($xarquivo)) {
   $xmod = " where c.codarq = $xarquivo ";
}

if (isset($xmodulo)) {
   $xmod = " where a.codmod = $xmodulo ";
}

  $sql=" 
	select trim(nomemod) as modulo,
               c.codarq, 
               trim(c.nomearq) as arquivo,
               trim(c.descricao) as descricao,
               d.seqarq,
               trim(e.nomecam) as campo,
               trim(e.rotulo) as rotulo,
               trim(e.conteudo) as conteudo,
               e.tamanho,
               e.nulo,
               e.maiusculo,
               e.aceitatipo,
               f.sequen as seq_prikey,
               g.sequen as seq_forkey,
               trim(h.nomearq) as nome_arqreferen,
               trim(j.nomecam) as campopai
	from db_sysmodulo a 
	     inner join db_sysarqmod           b on a.codmod=b.codmod 
	     inner join db_sysarquivo          c on c.codarq=b.codarq
	     inner join db_sysarqcamp          d on d.codarq=c.codarq
	     inner join db_syscampo   	       e on e.codcam=d.codcam
	     left outer join db_sysprikey      f on f.codarq=c.codarq and f.codcam=e.codcam
	     left outer join db_sysforkey      g on g.codarq=c.codarq and g.codcam=e.codcam
	     left outer join db_sysarquivo     h on h.codarq=g.referen
	     left outer join db_syscampodep    i on i.codcam=e.codcam
	     left outer join db_syscampo       j on j.codcam=i.codcampai
$xmod	order by modulo,arquivo,d.seqarq
";

$result   = $db_stdlib->db_query($sql);
if ( $result->rowCount() == 0 ) {
  $db_stdlib->db_redireciona('db_erros.php?fechar=true&db_erro= Problema na estrutura, não retornou nenhum registro na seleção.');
}

$head4  = utf8_decode("RELATÓRIO DA ESTRUTURA DO SISTEMA");

$pdf    = new libs\pdf(); // abre a classe

//$pdf->Open('L'); // abre o relatorio
$pdf->AliasNbPages(); // gera alias para as paginas
$pdf->AddPage(); // adiciona uma pagina
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(235);
$pdf->SetFont('Arial','B',6);

$result_tmp   = $result->fetch();

$bordat = 1;
$preenc = 0;
$xxarq  = 0;
$xmod   = $result_tmp->modulo;
$xarq   = $result_tmp->arquivo;
$xdescr = $result_tmp->descricao;

$pdf->SetFont('Arial','B',8);
$pdf->multicell(0,4,utf8_decode("Módulo  : ".strtoupper($xmod)),0,"L",$preenc);
$pdf->ln(3);
$pdf->SetFont('Arial','B',8);
$pdf->multicell(0,4,utf8_decode("Arquivo  : ".strtoupper($xarq)." - ".$xdescr),0,"L",$preenc);
$pdf->ln(1);
$pdf->SetFont('Arial','B',6);
$pdf->Cell(6,4,"SEQ",$bordat,0,"C",1);
$pdf->Cell(25,4,"CAMPO",$bordat,0,"C",1);
$pdf->Cell(50,4,"ROTULO",$bordat,0,"C",1);
$pdf->Cell(20,4,"TIPO",$bordat,0,"C",1);
$pdf->Cell(6,4,"TAM",$bordat,0,"C",1);
$pdf->Cell(6,4,"NULO",$bordat,0,"C",1);
$pdf->Cell(6,4,"MAI",$bordat,0,"C",1);
$pdf->Cell(6,4,"SCR",$bordat,0,"C",1);
$pdf->Cell(6,4,"PK",$bordat,0,"C",1);
$pdf->Cell(6,4,"FK",$bordat,0,"C",1);
$pdf->Cell(25,4,"ARQ.REF",$bordat,0,"C",1);
$pdf->Cell(25,4,"CAMPO PAI",$bordat,1,"C",1);
$pdf->ln(3);

for($i = 0;$i < $result->rowCount();$i++){
   $db_stdlib->db_fieldsmemory($result,$i);
   
   if ($xmod != $modulo and !empty($modulo) ) {
      $pdf->ln(3);
        $pdf->SetFont('Arial','B',8);
      $pdf->multicell(0,4,utf8_decode("Módulo  : ".strtoupper($modulo)),0,"L",$preenc);
        $pdf->ln(3);
   }
        
   if ( $xarq != $arquivo and !empty($modulo) ){
        $sqlind = "select a.*,b.*,c.nomecam
    from db_sysindices a
      inner join db_syscadind b on a.codind = b.codind
      inner join db_syscampo  c on c.codcam = b.codcam
    where codarq = $xxarq
                order by nomeind,sequen";
        $resindice = $db_stdlib->db_query($sqlind);
        $pdf->SetFont('Arial','B',8);
        if ( $resindice->rowCount() != 0 ) {
           $prinome = '';
           $espaco = '';
           $xnome = ''; 
           for ($iind = 0;$iind < $resindice->rowCount();$iind++){
               $db_stdlib->db_fieldsmemory($resindice,$iind);
               if ($prinome != $nomeind){
                   $xnome .= utf8_decode($espaco.$nomeind)."   -   Campos :  ";
                   $espaco = '#';
                   $virgula = '';
         }
               $xnome .= $virgula.$nomecam;
               $virgula = ', '; 
               $prinome = $nomeind;
           }
           $matrizind = explode('#',$xnome);
           $pdf->multicell(0,4,utf8_decode('Índices do arquivo : '),0,"L",$preenc);

           for( $xind = 0 ;$xind < sizeof($matrizind); $xind++ ){
                $pdf->multicell(0,4,$matrizind[$xind],0,"L",$preenc);
           }
        }else{
     $pdf->multicell(0,4,utf8_decode("Arquivo sem índice cadastrado"),0,"L",$preenc);
        }
  $pdf->ln(3);
        $pdf->SetFont('Arial','B',8);
  $pdf->multicell(0,4,"Arquivo  : ".utf8_decode(strtoupper($arquivo)." - ".$descricao),0,"L",$preenc);
        $pdf->ln(1);
        $pdf->SetFont('Arial','B',6);
  $pdf->Cell(6,4,"SEQ",$bordat,0,"C",1);
  $pdf->Cell(25,4,"CAMPO",$bordat,0,"C",1);
  $pdf->Cell(50,4,"ROTULO",$bordat,0,"C",1);
  $pdf->Cell(20,4,"TIPO",$bordat,0,"C",1);
  $pdf->Cell(6,4,"TAM",$bordat,0,"C",1);
  $pdf->Cell(6,4,"NULO",$bordat,0,"C",1);
  $pdf->Cell(6,4,"MAI",$bordat,0,"C",1);
  $pdf->Cell(6,4,"SCR",$bordat,0,"C",1);
  $pdf->Cell(6,4,"PK",$bordat,0,"C",1);
  $pdf->Cell(6,4,"FK",$bordat,0,"C",1);
  $pdf->Cell(25,4,"ARQ.REF",$bordat,0,"C",1);
  $pdf->Cell(25,4,"CAMPO PAI",$bordat,1,"C",1);
   }

   if ( $pdf->gety() > $pdf->h - 30 ){
        $pdf->addpage();
  $pdf->ln(3);
        $pdf->SetFont('Arial','B',8);
  $pdf->multicell(0,4,utf8_decode("Módulo  : ".strtoupper($modulo)),0,"L",$preenc);
        $pdf->ln(3);
  $pdf->ln(3);
        $pdf->SetFont('Arial','B',8);
  $pdf->multicell(0,4,"Arquivo  : ".utf8_decode(strtoupper($arquivo)." - ".$descricao),0,"L",$preenc);
        $pdf->ln(1);
        $pdf->SetFont('Arial','B',6);
  $pdf->Cell(6,4,"SEQ",$bordat,0,"C",1);
  $pdf->Cell(25,4,"CAMPO",$bordat,0,"C",1);
  $pdf->Cell(50,4,"ROTULO",$bordat,0,"C",1);
  $pdf->Cell(20,4,"TIPO",$bordat,0,"C",1);
  $pdf->Cell(6,4,"TAM",$bordat,0,"C",1);
  $pdf->Cell(6,4,"NULO",$bordat,0,"C",1);
  $pdf->Cell(6,4,"MAI",$bordat,0,"C",1);
  $pdf->Cell(6,4,"SCR",$bordat,0,"C",1);
  $pdf->Cell(6,4,"PK",$bordat,0,"C",1);
  $pdf->Cell(6,4,"FK",$bordat,0,"C",1);
  $pdf->Cell(25,4,"ARQ.REF",$bordat,0,"C",1);
  $pdf->Cell(25,4,"CAMPO PAI",$bordat,1,"C",1);
   }
   $pdf->SetFont('Arial','',6);
   $pdf->Cell(6,4,$seqarq,$borda,0,"C",0);
   $pdf->Cell(25,4,$campo,$borda,0,"L",0);
   $pdf->Cell(50,4,utf8_decode($rotulo),$borda,0,"L",0);
   $pdf->Cell(20,4,$conteudo,$borda,0,"L",0);
   $pdf->Cell(6,4,$tamanho,$borda,0,"C",0);
   $pdf->Cell(6,4,$nulo,$borda,0,"C",0);
   $pdf->Cell(6,4,$maiusculo,$borda,0,"C",0);
   $pdf->Cell(6,4,$aceitatipo,$borda,0,"C",0);
   $pdf->Cell(6,4,$seq_prikey,$borda,0,"C",0);
   $pdf->Cell(6,4,$seq_forkey,$borda,0,"C",0);
   $pdf->Cell(25,4,$nome_arqreferen,$borda,0,"L",0);
   $pdf->Cell(25,4,$campopai,$borda,1,"L",0);

   $xmod = $modulo;
   $xarq = $arquivo;
   $xxarq = $codarq;

}


$pdf->Output();
?>