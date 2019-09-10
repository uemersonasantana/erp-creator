<?php
/**
 * janela
 *
 * @package     libs
 * @subpackage  class
 * @author      Uemerson A. Santana <uemerson@icloud.com>
 */

namespace libs;

class janela {
  //|00|//janela
  //|10|//Abre um determinado arquivo no diretório DOCUMENT_ROOT do servidor onde estiver rodando o PHP
  //|15|//[variavel] = new janela($nome,$arquivo);
  //|20|//nome    : Nome da janela a ser criada como objeto
  //|20|//arquivo : Arquivo a ser executado no iframe
  //|99|//(esta funcao não esta mais em uso, verifica a funcao java script |js_OpenJanelaIframe|)
  //|99|//Exemplo:
  //|99|//$func_iframe = new janela('db_iframe',''); // abre a classe janela
  //|99|//$func_iframe->posX=1;                      // seta a posicao que deverá abrir em relação a esquerda
  //|99|//$func_iframe->posY=20;                     // seta a posição que deverá abrir em relação ao topo
  //|99|//$func_iframe->largura=780;                 // seta a largura do formulário
  //|99|//$func_iframe->altura=430;                  // seta a altura do formulário
  //|99|//$func_iframe->titulo='Pesquisa';           // seta o titulo da janela
  //|99|//$func_iframe->iniciarVisivel = false;      // seta se mostrará ou não a janela (neste caso não)
  //|99|//$func_iframe->mostrar();                   // escreve o objeto iframe na tela

  var $nome;
  var $arquivo;
  var $iniciarVisivel = true;
  var $largura = "780";
  var $altura = "430";
  var $posX = "1";
  var $posY = "20";
  var $scrollbar = "auto"; // pode ser tb, 0 ou 1
  var $corFundoTitulo = "#2C7AFE";
  var $corTitulo = "white";
  var $fonteTitulo = "Arial, Helvetica, sans-serif";
  var $tamTitulo = "11";
  var $titulo = "DBSeller Informática Ltda";
  var $janBotoes = "111";

  function janela($nome, $arquivo) {
    $this->nome = $nome;
    $this->arquivo = $arquivo;
  }

  function mostrar() {
    $this->largura = "100%";
    $this->altura = "100%";

    if ($this->iniciarVisivel == true)
      $this->iniciarVisivel = "block";
    else
      $this->iniciarVisivel = "none";
    ?>
    <div id="Jan<? echo $this->nome ?>" style=" background-color: #c0c0c0;border: 0px outset #666666;position:absolute; left:<? echo $this->posX ?>px; top:<? echo $this->posY ?>px; width:<? echo $this->largura ?>; height:<? echo $this->altura ?>; z-index:1; display: <? echo $this->iniciarVisivel ?>;"><table width="100%" height="100%" style="border-color: #f0f0f0 #606060 #404040 #d0d0d0;border-style: solid;  border-width: 2px;"  border="0" cellspacing="0" cellpadding="2"><tr><td><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr id="CF<? echo $this->nome ?>" style="white-space: nowrap;background-color:<? echo $this->corFundoTitulo ?>"><td nowrap onmousedown="js_engage(document.getElementById('Jan<? echo $this->nome ?>'),event)" onmouseup="js_release(document.getElementById('Jan<? echo $this->nome ?>'),event)" onmousemove="js_dragIt(document.getElementById('Jan<? echo $this->nome ?>'),event)" onmouseout="js_release(document.getElementById('Jan<? echo $this->nome ?>'),event)" width="80%" style="cursor:hand;font-weight: bold;color: <? echo $this->corTitulo ?>;font-family: <? echo $this->fonteTitulo ?>;font-size: <? echo $this->tamTitulo ?>px">&nbsp;<? echo $this->titulo ?></td><td width="20%" align="right" valign="middle" nowrap><?$kp=0x4;$m = $kp & $this->janBotoes;$kp >>= 1;?><img <? echo $m?'style="cursor:hand"':"" ?> src=<? echo $m?"skins/img.php?file=Controles/jan_mini_on.png":"skins/img.php?file=Controles/jan_mini_off.png" ?> title="Minimizar" border="0" onClick="js_MinimizarJan(this,'<? echo $this->nome ?>')"><?$m = $kp & $this->janBotoes;$kp >>= 1;?><?$m = $kp & $this->janBotoes;$kp >>= 1;?><img <? echo $m?'style="cursor:hand"':"" ?> src=<? echo $m?"skins/img.php?file=Controles/jan_fechar_on.png":"skins/img.php?file=Controles/jan_fechar_off.png" ?> title="Fechar" border="0" onClick="js_FecharJan(this,'<? echo $this->nome ?>')"></td></tr></table></td></tr><tr><td width="100%" height="100%"><iframe frameborder="1" style="border-color:#C0C0F0" height="100%" width="100%" id="IF<? echo $this->nome ?>" name="IF<? echo $this->nome ?>" scrolling="<? echo $this->scrollbar ?>" src="<? echo $this->arquivo ?>"></iframe></td></tr></table></div><script><? echo $this->nome ?> = new janela(document.getElementById('Jan<? echo $this->nome ?>'),document.getElementById('CF<? echo $this->nome ?>'),IF<? echo $this->nome ?>);</script>
  <?php


  }
//|XX|//
}