<?php
/**
 * db_db_sysarquivo
 *
 * @package   configuracao
 */

namespace classes;

use libs\db_stdlib;

//CLASSE DA ENTIDADE db_sysarquivo
class db_db_sysarquivo {
   // cria variaveis de erro
   var $rotulo     = null;
   var $rotulo2    = null;
   var $query_sql  = null;
   var $numrows    = 0;
   var $numrows_incluir = 0;
   var $numrows_alterar = 0;
   var $numrows_excluir = 0;
   var $erro_status= null;
   var $erro_sql   = null;
   var $erro_banco = null;
   var $erro_msg   = null;
   var $erro_campo = null;
   var $pagina_retorno = null;
   // cria variaveis do arquivo
   var $codarq = 0;
   var $nomearq = null;
   var $descricao = null;
   var $sigla = null;
   var $dataincl_dia = null;
   var $dataincl_mes = null;
   var $dataincl_ano = null;
   var $dataincl = null;
   var $tipotabela = 0;
   var $naolibclass = 'f';
   var $naolibfunc = 'f';
   var $naolibprog = 'f';
   var $naolibform = 'f';
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 codarq = int4 = Codigo Arquivo
                 nomearq = char(40) = Nome do Arquivo
                 descricao = text = Descrição
                 sigla = char(4) = Sigla
                 dataincl = date = Data Inclusão
                 rotulo = varchar(50) = Rótulo
                 tipotabela = int4 = Tipo Tabela
                 naolibclass = bool = Não Lib. Classe
                 naolibfunc = bool = Não Lib. Função
                 naolibprog = bool = Não Lib. Prog.
                 naolibform = bool = Não Lib.. Form
                 ";
   //funcao construtor da classe
   function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo          =   new \std\rotulo("db_sysarquivo");
     $this->pagina_retorno  =   basename($_SERVER["PHP_SELF"]);
   }
   //funcao erro
   function erro($mostra,$retorna) {
     if ( ($this->erro_status == "0") or ($mostra == true and $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if ($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }
   // funcao para atualizar campos
   function atualizacampos($exclusao=false) {
     if ($exclusao==false){
       $this->codarq = ($this->codarq == ""?@$GLOBALS["codarq"]:$this->codarq);
       $this->nomearq = ($this->nomearq == ""?@$GLOBALS["nomearq"]:$this->nomearq);
       $this->descricao = ($this->descricao == ""?@$GLOBALS["descricao"]:$this->descricao);
       $this->sigla = ($this->sigla == ""?@$GLOBALS["sigla"]:$this->sigla);
       if ($this->dataincl == ""){
         $this->dataincl_dia = ($this->dataincl_dia == ""?@$GLOBALS["dataincl_dia"]:$this->dataincl_dia);
         $this->dataincl_mes = ($this->dataincl_mes == ""?@$GLOBALS["dataincl_mes"]:$this->dataincl_mes);
         $this->dataincl_ano = ($this->dataincl_ano == ""?@$GLOBALS["dataincl_ano"]:$this->dataincl_ano);
         if ($this->dataincl_dia != ""){
            $this->dataincl = $this->dataincl_ano."-".$this->dataincl_mes."-".$this->dataincl_dia;
         }
       }
       // Mudei o nome para rotulo 2 afim de envitar o conflito com a varável que chama a classe rótulo.
       $this->rotulo2      = ($this->rotulo2 == ""?@$GLOBALS["rotulo"]:$this->rotulo2);
       $this->tipotabela  = ($this->tipotabela == ""?@$GLOBALS["tipotabela"]:$this->tipotabela);
       
       $this->naolibclass = ( isset($GLOBALS["naolibclass"])  ?  't':$this->naolibclass);
       $this->naolibfunc  = ( isset($GLOBALS["naolibfunc"])   ?  't':$this->naolibfunc);
       $this->naolibprog  = ( isset($GLOBALS["naolibprog"])   ?  't':$this->naolibprog);
       $this->naolibform  = ( isset($GLOBALS["naolibform"])   ?  't':$this->naolibform);
     }else{
       $this->codarq = ($this->codarq == ""?@$GLOBALS["codarq"]:$this->codarq);
     }
   }
   // funcao para inclusao
   function incluir () {
      $this->atualizacampos();
     if ($this->nomearq == null ){
       $this->erro_sql = " Campo Nome do Arquivo nao Informado.";
       $this->erro_campo = "nomearq";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->descricao == null ){
       $this->erro_sql = " Campo Descrição nao Informado.";
       $this->erro_campo = "descricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->dataincl == null ){
       $this->erro_sql = " Campo Data Inclusão nao Informado.";
       $this->erro_campo = "dataincl_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->rotulo2 == null ){
       $this->erro_sql = " Campo Rótulo nao Informado.";
       $this->erro_campo = "rotulo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->tipotabela == null ){
       $this->erro_sql = " Campo Tipo Tabela nao Informado.";
       $this->erro_campo = "tipotabela";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->naolibclass == null ){
       $this->erro_sql = " Campo Não Lib. Classe nao Informado.";
       $this->erro_campo = "naolibclass";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->naolibfunc == null ){
       $this->erro_sql = " Campo Não Lib. Função nao Informado.";
       $this->erro_campo = "naolibfunc";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->naolibprog == null ){
       $this->erro_sql = " Campo Não Lib. Prog. nao Informado.";
       $this->erro_campo = "naolibprog";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->naolibform == null ){
       $this->erro_sql = " Campo Não Lib.. Form nao Informado.";
       $this->erro_campo = "naolibform";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->codarq == "" or $this->codarq == null ){
       $result = db_stdlib::db_query("select nextval('db_sysarquivo_codarq_seq')");
       if ($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: db_sysarquivo_codarq_seq do campo: codarq";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->codarq = db_stdlib::lastInsertId();
     }else{
       $result = db_stdlib::db_query("select last_value from db_sysarquivo_codarq_seq");
       if ( $result != false and ($result->fetch()->last_value < $codarq) )  {
         $this->erro_sql = " Campo codarq maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->codarq = $codarq;
       }
     }
     if ( ($this->codarq == null) or ($this->codarq == "") ){
       $this->erro_sql = " Campo codarq nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = "insert into db_sysarquivo (
                                       codarq
                                      ,nomearq
                                      ,descricao
                                      ,sigla
                                      ,dataincl
                                      ,rotulo
                                      ,tipotabela
                                      ,naolibclass
                                      ,naolibfunc
                                      ,naolibprog
                                      ,naolibform
                       )
                values (
                                $this->codarq
                               ,'$this->nomearq'
                               ,'$this->descricao'
                               ,'$this->sigla'
                               ,'$this->dataincl'
                               ,'$this->rotulo2'
                               ,$this->tipotabela
                               ,'$this->naolibclass'
                               ,'$this->naolibfunc'
                               ,'$this->naolibprog'
                               ,'$this->naolibform'
                      )";
     $result = db_stdlib::db_query($sql);
     if ($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if ( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Tabela de Dados ($this->codarq) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Tabela de Dados já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Tabela de Dados ($this->codarq) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codarq;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= $result->rowCount();
     $resaco = $this->sql_record($this->sql_query_file($this->codarq));
     if ( ($resaco!=false) or ($this->numrows!=0)){
      $linha = $resaco->fetch();

       $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = db_stdlib::lastInsertId();
       $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
       $resac = db_stdlib::db_query("insert into db_acountkey values($acount,759,'$this->codarq','I')");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,140,759,'','".addslashes($linha->codarq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,140,760,'','".addslashes($linha->nomearq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,140,750,'','".addslashes($linha->descricao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,140,761,'','".addslashes($linha->sigla)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,140,751,'','".addslashes($linha->dataincl)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,140,756,'','".addslashes($linha->rotulo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8924,'','".addslashes($linha->tipotabela)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8925,'','".addslashes($linha->naolibclass)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8926,'','".addslashes($linha->naolibfunc)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8927,'','".addslashes($linha->naolibprog)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8928,'','".addslashes($linha->naolibform)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   function alterar ($codarq=null) {
      $this->atualizacampos();

     $sql = " update db_sysarquivo set ";
     $virgula = "";
     if (trim($this->codarq)!="" or isset($GLOBALS["codarq"])){
        if (trim($this->codarq)=="" and isset($GLOBALS["codarq"])){
           $this->codarq = "0" ;
        }
       $sql  .= $virgula." codarq = $this->codarq ";
       $virgula = ",";
       if (trim($this->codarq) == null ){
         $this->erro_sql = " Campo Codigo Arquivo nao Informado.";
         $this->erro_campo = "codarq";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if (trim($this->nomearq)!="" or isset($GLOBALS["nomearq"])){
       $sql  .= $virgula." nomearq = '$this->nomearq' ";
       $virgula = ",";
       if (trim($this->nomearq) == null ){
         $this->erro_sql = " Campo Nome do Arquivo nao Informado.";
         $this->erro_campo = "nomearq";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if (trim($this->descricao)!="" or isset($GLOBALS["descricao"])){
       $sql  .= $virgula." descricao = '$this->descricao' ";
       $virgula = ",";
       if (trim($this->descricao) == null ){
         $this->erro_sql = " Campo Descrição nao Informado.";
         $this->erro_campo = "descricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if (trim($this->sigla)!="" or isset($GLOBALS["sigla"])){
       $sql  .= $virgula." sigla = '$this->sigla' ";
       $virgula = ",";
     }
     if (trim($this->dataincl)!="" or isset($GLOBALS["dataincl_dia"]) and  ($GLOBALS["dataincl_dia"] !="") ){
       $sql  .= $virgula." dataincl = '$this->dataincl' ";
       $virgula = ",";
       if (trim($this->dataincl) == null ){
         $this->erro_sql = " Campo Data Inclusão nao Informado.";
         $this->erro_campo = "dataincl_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if (isset($GLOBALS["dataincl_dia"])){
         $sql  .= $virgula." dataincl = null ";
         $virgula = ",";
         if (trim($this->dataincl) == null ){
           $this->erro_sql = " Campo Data Inclusão nao Informado.";
           $this->erro_campo = "dataincl_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if (trim($this->rotulo2)!="" or isset($GLOBALS["rotulo"])){
       $sql  .= $virgula." rotulo = '$this->rotulo2' ";
       $virgula = ",";
       if (trim($this->rotulo2) == null ){
         $this->erro_sql = " Campo Rótulo nao Informado.";
         $this->erro_campo = "rotulo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if (trim($this->tipotabela)!="" or isset($GLOBALS["tipotabela"])){
       $sql  .= $virgula." tipotabela = $this->tipotabela ";
       $virgula = ",";
       if (trim($this->tipotabela) == null ){
         $this->erro_sql = " Campo Tipo Tabela nao Informado.";
         $this->erro_campo = "tipotabela";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if (trim($this->naolibclass)!="" or isset($GLOBALS["naolibclass"])){
       $sql  .= $virgula." naolibclass = '$this->naolibclass' ";
       $virgula = ",";
       if (trim($this->naolibclass) == null ){
         $this->erro_sql = " Campo Não Lib. Classe nao Informado.";
         $this->erro_campo = "naolibclass";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if (trim($this->naolibfunc)!="" or isset($GLOBALS["naolibfunc"])){
       $sql  .= $virgula." naolibfunc = '$this->naolibfunc' ";
       $virgula = ",";
       if (trim($this->naolibfunc) == null ){
         $this->erro_sql = " Campo Não Lib. Função nao Informado.";
         $this->erro_campo = "naolibfunc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if (trim($this->naolibprog)!="" or isset($GLOBALS["naolibprog"])){
       $sql  .= $virgula." naolibprog = '$this->naolibprog' ";
       $virgula = ",";
       if (trim($this->naolibprog) == null ){
         $this->erro_sql = " Campo Não Lib. Prog. nao Informado.";
         $this->erro_campo = "naolibprog";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if (trim($this->naolibform)!="" or isset($GLOBALS["naolibform"])){
       $sql  .= $virgula." naolibform = '$this->naolibform' ";
       $virgula = ",";
       if (trim($this->naolibform) == null ){
         $this->erro_sql = " Campo Não Lib.. Form nao Informado.";
         $this->erro_campo = "naolibform";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if ($this->codarq!=null){
       $sql .= " codarq = $this->codarq";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->codarq));
     if ( $this->numrows > 0 ) {
       foreach ( $resaco as $linha) {
         $resac   = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount  = db_stdlib::lastInsertId();
         $resac   = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,759,'$this->codarq','A')");
         if (isset($GLOBALS["codarq"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,140,759,'".addslashes($linha->codarq)."','$this->codarq',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if (isset($GLOBALS["nomearq"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,140,760,'".addslashes($linha->nomearq)."','$this->nomearq',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if (isset($GLOBALS["descricao"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,140,750,'".addslashes($linha->descricao)."','$this->descricao',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if (isset($GLOBALS["sigla"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,140,761,'".addslashes($linha->sigla)."','$this->sigla',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if (isset($GLOBALS["dataincl"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,140,751,'".addslashes($linha->dataincl)."','$this->dataincl',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if (isset($GLOBALS["rotulo"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,140,756,'".addslashes($linha->rotulo)."','$this->rotulo2',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if (isset($GLOBALS["tipotabela"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8924,'".addslashes($linha->tipotabela)."','$this->tipotabela',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if (isset($GLOBALS["naolibclass"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8925,'".addslashes($linha->naolibclass)."','$this->naolibclass',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if (isset($GLOBALS["naolibfunc"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8926,'".addslashes($linha->naolibfunc)."','$this->naolibfunc',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if (isset($GLOBALS["naolibprog"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8927,'".addslashes($linha->naolibprog)."','$this->naolibprog',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if (isset($GLOBALS["naolibform"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8928,'".addslashes($linha->naolibform)."','$this->naolibform',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_stdlib::db_query($sql);
     if ($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tabela de Dados nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->codarq;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if ($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Tabela de Dados nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->codarq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codarq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = $result->rowCount();
         return true;
       }
     }
   }
   // funcao para exclusao
   function excluir ($codarq=null,$dbwhere=null) {
    $this->atualizacampos();

    if ( $codarq != null ) {
      $this->codarq = $codarq;
    }

     if ($dbwhere==null or $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($this->codarq));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if ( ($resaco!=false) or ($this->numrows!=0 ) ) {
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,759,'$this->codarq','E')");

         $resac = db_stdlib::db_query("insert into db_acount values($acount,140,759,'".addslashes($linha->codarq)."','$this->codarq',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,140,760,'".addslashes($linha->nomearq)."','$this->nomearq',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,140,750,'".addslashes($linha->descricao)."','$this->descricao',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,140,761,'".addslashes($linha->sigla)."','$this->sigla',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,140,751,'".addslashes($linha->dataincl)."','$this->dataincl',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,140,756,'".addslashes($linha->rotulo)."','$this->rotulo2',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8924,'".addslashes($linha->tipotabela)."','$this->tipotabela',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8925,'".addslashes($linha->naolibclass)."','$this->naolibclass',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8926,'".addslashes($linha->naolibfunc)."','$this->naolibfunc',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8927,'".addslashes($linha->naolibprog)."','$this->naolibprog',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,140,8928,'".addslashes($linha->naolibform)."','$this->naolibform',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from db_sysarquivo
                    where ";
     $sql2 = "";
     if ($dbwhere==null or $dbwhere ==""){
        if ($this->codarq != ""){
          if ($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " codarq = $this->codarq ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_stdlib::db_query($sql.$sql2);
     if ($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tabela de Dados nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$this->codarq;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if ($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Tabela de Dados nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$this->codarq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codarq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = $result->rowCount();
         return true;
       }
     }
   }
   // funcao do recordset
   function sql_record($sql) {
     $result = db_stdlib::db_query($sql);
     if ($result==false){
       $this->numrows    = 0;
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = $result->rowCount();
      if ($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:db_sysarquivo";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   function sql_query ( $codarq=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if ($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from db_sysarquivo ";
     $sql2 = "";
     if ($dbwhere==""){
       if ($codarq!=null ){
         $sql2 .= " where db_sysarquivo.codarq = $codarq ";
       }
     }else if ($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if ($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_arqmod ( $codarq=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if ($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from db_sysarquivo ";
     $sql .= "      inner join db_sysarqmod    on db_sysarqmod.codarq = db_sysarquivo.codarq";
     $sql .= "      inner join db_sysmodulo    on db_sysmodulo.codmod = db_sysarqmod.codmod";
     $sql2 = "";
     if ($dbwhere==""){
       if ($codarq!=null ){
         $sql2 .= " where db_sysarquivo.codarq = $codarq ";
       }
     }else if ($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if ($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_file ( $codarq=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if ($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from db_sysarquivo ";
     $sql2 = "";
     if ($dbwhere==""){
       if ($codarq!=null ){
         $sql2 .= " where db_sysarquivo.codarq = $codarq ";
       }
     }else if ($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if ($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }

  /**
   * Busca todas tabela de um modulo e que tenha apenas uma PK
   *
   * @param int $iCodigoModulo
   * @access public
   * @return string
   */
  public function sql_query_buscaTabelaPorModulo($iCodigoModulo) {

    $sTabelasPermitidas = implode( ', ', InconsistenciaDados::getTabelasPermitidas() );
    $sSql  = "   select db_sysarquivo.codarq,                                                 ";
    $sSql .= "          db_sysarquivo.nomearq                                                 ";
    $sSql .= "     from db_sysarquivo                                                         ";
    $sSql .= "          inner join db_sysarqmod on db_sysarqmod.codarq = db_sysarquivo.codarq ";
    $sSql .= "          inner join db_sysmodulo on db_sysmodulo.codmod = db_sysarqmod.codmod  ";
    $sSql .= "          inner join db_sysprikey on db_sysprikey.codarq = db_sysarquivo.codarq ";
    $sSql .= "    where db_sysarqmod.codmod  = {$iCodigoModulo}                               ";
    $sSql .= "      and db_sysarquivo.codarq in ({$sTabelasPermitidas})                       ";
    $sSql .= " group by db_sysarquivo.codarq,                                                 ";
    $sSql .= "          db_sysarquivo.nomearq                                                 ";
    $sSql .= "   having count(db_sysprikey.codarq) = 1                                        ";
    $sSql .= " order by db_sysarquivo.nomearq asc;                                            ";

    return $sSql;
  }


  /**
   * Busca nome dos campos e se e pk pelo código da tabela
   *
   * @param int $iCodigoTabela
   * @access public
   * @return string
   */
  public function sql_query_buscaCamposPorTabela($iCodigoTabela) {

    $sSql  =" select db_syscampo.nomecam   as nomecampo,                              ";
    $sSql .="   	   db_sysarquivo.nomearq as nometabela,                             ";
    $sSql .="   	  case                                                                        ";
    $sSql .="   	  when db_sysprikey.codcam is not null                                        ";
    $sSql .="   	  then true                                                                   ";
    $sSql .="   	  else                                                                        ";
    $sSql .="   	    false                                                                     ";
    $sSql .="   	    end as campo_pk                                                           ";
    $sSql .="  from   db_syscampo                                                               ";
    $sSql .="  inner join db_sysarqcamp ON db_sysarqcamp.codcam = db_syscampo.codcam            ";
    $sSql .="  inner join db_sysarquivo on db_sysarquivo.codarq = db_sysarqcamp.codarq          ";
    $sSql .="   	    left  join db_sysprikey  on db_sysprikey.codcam  = db_syscampo.codcam     ";
    $sSql .="  where  db_sysarqcamp.codarq = {$iCodigoTabela}                                   ";

    return $sSql;
  }

  /**
   * Busca nome dos campos pk e sua referencia pelo código da tabela
   *
   * @param int $iCodigoTabela
   * @access public
   * @return string
   */
  public function sql_query_buscaCampoReferenciaPorTabela($iCodigoTabela) {

    $sSql  = " select nomecam,                                                       ";
    $sSql .= "       db_sysprikey.camiden as campo_referencia                        ";
    $sSql .= " from db_sysarquivo                                                    ";
    $sSql .= " inner join db_sysprikey ON db_sysprikey.codarq = db_sysarquivo.codarq ";
    $sSql .= " inner join db_syscampo ON db_syscampo.codcam   = db_sysprikey.codcam  ";
    $sSql .= " where db_sysarquivo.codarq = {$iCodigoTabela}                         ";

    return $sSql;
  }

}
?>