<?php
/**
 * Esta classe é uma extensão da classe |fpdf|.
 *
 * @package    libs
 * @author     Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

use libs\db_stdlib;
use libs\Services_Skins;

use Fpdf\Fpdf;

class pdf extends Fpdf { 
  protected $imprime_rodape;
//|00|//pdf
//|10|//Esta classe é uma extensão da classe |fpdf| e difere da mesma pelo fato de que nesta  classe
//|10|//foram alterados os métodos |header| (cabeçalho da página) de  |footer|  (rodapé)  para   que
//|10|//atendessem as nossas necessidades, da seguinte maneira:
//|10|//|header|     :    - O logotipo da prefeitura ficou alinhado a esquerda;
//|10|//                  - Os dados da prefeitura tais como: nome,  enderço,  município,  telefone,
//|10|//                    email, e site ficaram alinhados a  esquerda,  ao  lado  do  logotipo  da
//|10|//                    prefeitura;
//|10|//               Contem ainda variáveis livres para o desenvolvedor as quais  serão  impressas
//|10|//               na parte superior direita da tela, são elas:
//|10|//                  - head1, head2, head3, head4, head5, head6, head7, head8, head9
//|10|//
//|10|//|footer|     :    - contem dados como:
//|10|//                      - programa que gerou o relatório;
//|10|//                      - emissor;
//|10|//                      - exercício;
//|10|//                      - data e hora da emissão;
//|10|//		      - número da página.

public function __construct(
      $orientation = 'P',
      $unit = 'mm',
      $size = 'letter'
  ) {
      parent::__construct( $orientation, $unit, $size );
      $this->imprime_rodape = true;
  }

//Page header
function Header() { 
//#00#//header
//#10#//Este método é usado gerar o cabeçalho da página. É chamado automaticamente por |addPage| e não
//#10#//deve ser chamado diretamente pela aplicação. A implementação em FPDF está  vazia,  então  você
//#10#//precisa criar uma subclasse dele para  sobrepor o  método  se  você  quiser  um  processamento
//#10#//específico para o cabeçalho.
//#15#//header()
//#99#//Exemplo:
//#99#//class PDF extends FPDF
//#99#//{
//#99#//  function Header()
//#99#//  {
//#99#//    Seleciona fonte Arial bold 15
//#99#//      $this->SetFont('Arial','B',15);
//#99#//    Move para a direita
//#99#//      $this->Cell(80);
//#99#//    Titulo dentro de uma caixa
//#99#//      $this->Cell(30,10,'Title',1,0,'C');
//#99#//    Quebra de linha
//#99#//      $this->Ln(20);
//#99#//  }
//#99#//}

    //global $conn;
    //global $result;
    //global $url;
    //global $db21_compl;
	//Dados da instituição

//   echo ("select nomeinst,ender,munic,uf,telef,email,url,logo from db_stdlib::db_config where codigo = ".db_stdlib::db_getsession("db_stdlib::db_instit"));
//   $dados = db_stdlib::db_query("select nomeinst,ender,munic,uf,telef,email,url,logo from db_stdlib::db_config where codigo = ".db_stdlib::db_getsession("db_stdlib::db_instit"));
    $dados = db_stdlib::db_query("select nomeinst,
                                   db21_compl,
                                   trim(ender)||',
                                   '||trim(cast(numero as text)) as ender,
                                   trim(ender) as rua,
                                   munic,
                                   numero,
                                   uf,
                                   cgc,
                                   telef,
                                   email,
                                   url,
                                   logo
                            from db_config where codigo = ".db_stdlib::db_getsession("DB_instit"))->fetch();

    $url = @$dados->url; //echo Services_Skins::getSkinLink().$dados->logo;exit();
    $this->SetXY(1,1);
    $this->Image(Services_Skins::getSkinLink().'img/'.$dados->logo,10,7,20);

  //$this->Cell(100,32,"",1);
    $nome                 = utf8_decode($dados->nomeinst);
    $GLOBALS['nomeinst']  = utf8_decode($dados->nomeinst);


    if(strlen($nome) > 42)
      $TamFonteNome = 8;
    else
      $TamFonteNome = 9;

    $this->SetFont('Arial','BI',$TamFonteNome);
    $this->Text(33,9,$nome);
    $this->SetFont('Arial','I',8);
    $sComplento = substr(trim($dados->db21_compl),0,20 );
    if ($sComplento != '' || $sComplento != null ) {
    	$sComplento = ", ".substr(trim($dados->db21_compl),0,20 );
    }
    $this->Text(33,14,utf8_decode(trim($dados->rua).", ".trim($dados->numero).$sComplento));
    $this->Text(33,18,utf8_decode(trim($dados->munic)." - ".$dados->uf));
    $this->Text(33,22,trim($dados->telef)."   -    CNPJ : ".db_stdlib::db_formatar($dados->cgc,"cnpj"));
    $this->Text(33,26,trim($dados->email));
    $comprim = ($this->w - $this->rMargin - $this->lMargin);
    $this->Text(33,30,$url);
    $Espaco = $this->w - 80 ;
    $this->SetFont('Arial','',7);
    $margemesquerda = $this->lMargin;
    $this->setleftmargin($Espaco);
    $this->sety(6);
    $this->setfillcolor(235);
    //$this->roundedrect($Espaco - 3,5,75,28,2,'DF','123');
    $this->line(10,33,$comprim,33);
    $this->setfillcolor(255);
    $this->multicell(0,3,@$GLOBALS["head1"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head2"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head3"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head4"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head5"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head6"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head7"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head8"],0,"J",0);
    $this->multicell(0,3,@$GLOBALS["head9"],0,"J",0);
    $this->setleftmargin($margemesquerda);
    $this->SetY(35);
  }

//Page footer
function Footer() {
//#00#//footer
//#10#//Este método é usado para criar o rodapé da página. Ele é automaticamente chamado por |addPage|
//#10#//e |close| e não deve ser chamado diretamente pela aplicação. A  implementação  em  FPDF  está
//#10#//vazia, então você  deve  criar  uma  subclasse  e  sobrepor  o  método  se  você  quiser   um
//#10#//processamento específico.
//#15#//footer()
//#99#//Exemplo:
//#99#//class PDF extends FPDF
//#99#//{
//#99#//  function Footer()
//#99#//  {
//#99#//    Vai para 1.5 cm da borda inferior
//#99#//      $this->SetY(-15);
//#99#//    Seleciona Arial itálico 8
//#99#//      $this->SetFont('Arial','I',8);
//#99#//    Imprime o número da página centralizado
//#99#//      $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
//#99#//  }
//#99#//}
  //global $conn;
  //global $result;
  //global $url;

    if( $this->imprime_rodape == true) { 

		/*
		 * Modificação para exibir o caminho do menu
		 * na base do relatório
		 */
    	//$sSqlMenuAcess = "SELECT fc_montamenu(funcao) as menu from db_stdlib::db_itensmenu where id_item =".db_stdlib::db_getsession("db_stdlib::db_itemmenu_acessado");
      $sSqlMenuAcess = " select trim(modulo.descricao)||'>'||trim(menu.descricao)||'>'||trim(item.descricao) as menu
    	                     from db_menu
                      	  inner join db_itensmenu as modulo on modulo.id_item = db_menu.modulo
                      	  inner join db_itensmenu as menu on menu.id_item = db_menu.id_item
                      	  inner join db_itensmenu as item on item.id_item = db_menu.id_item_filho
                      	  where id_item_filho = ".db_stdlib::db_getsession("DB_itemmenu_acessado")."
                      	    and modulo = ".db_stdlib::db_getsession("DB_modulo");

    	$rsMenuAcess   = db_stdlib::db_query($sSqlMenuAcess)->fetch();
    	$sMenuAcess    = substr( $rsMenuAcess->menu , 0, 50);

	    //Position at 1.5 cm from bottom
	    $this->SetFont('Arial','',5);
	    $this->text(10,$this->h-8,'Base: '.@$GLOBALS["DB_NBASE"]);
	    $this->SetFont('Arial','I',6);
	    $this->SetY(-10);
	    
      $nome = @$_SERVER["PHP_SELF"];
	    $nome = substr($nome,strrpos($nome,"/")+1);

	    $result_nomeusu = db_stdlib::db_query("select nome as nomeusu from db_usuarios where id_usuario =".db_stdlib::db_getsession("DB_id_usuario"));
	    
      if ( $result_nomeusu->rowCount() > 0 ) {
	    	$nomeusu = $result_nomeusu->fetch()->nomeusu;
	    }
	    if ( isset($nomeusu) and $nomeusu != "" ) {
	    	$emissor = $nomeusu;
	    }else{
	    	$emissor = @$GLOBALS["DB_login"];
	    }
	    $this->Cell(0,10,$sMenuAcess. "  ". $nome.'   Emissor: '.substr(ucwords(strtolower($emissor)),0,30).'  Exerc: '.db_stdlib::db_getsession("DB_anousu").
	                                              '   Data: '.date("d-m-Y",db_stdlib::db_getsession("DB_datausu"))." - ".date("H:i:s"),"T",0,'L');

	    $this->Cell(0,10,utf8_decode('Pág '.$this->PageNo()).'/{nb}',0,1,'R');
    }
  }

// mudar o angulo do texto
function TextWithDirection($x,$y,$txt,$direction='R')
{
    $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
    if ($direction=='R')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$txt);
    elseif ($direction=='L')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$txt);
    elseif ($direction=='U')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
    elseif ($direction=='D')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
    else
        $s=sprintf('BT %.2f %.2f Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$txt);
    $this->_out($s);
}

// rotacionar o texto

function TextWithRotation($x,$y,$txt,$txt_angle,$font_angle=0)
{
    $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));

    $font_angle+=90+$txt_angle;
    $txt_angle*=M_PI/180;
    $font_angle*=M_PI/180;

    $txt_dx=cos($txt_angle);
    $txt_dy=sin($txt_angle);
    $font_dx=cos($font_angle);
    $font_dy=sin($font_angle);

    $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
             $txt_dx,$txt_dy,$font_dx,$font_dy,
             $x*$this->k,($this->h-$y)*$this->k,$txt);
    $this->_out($s);


}

}

//|XX|//
?>