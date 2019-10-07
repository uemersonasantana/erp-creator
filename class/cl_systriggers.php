<?php
/**
 * cl_systriggers
 *
 * @package   configuracao
 */

namespace classes;

use libs\db_stdlib;

//MODULO: configuracoes
//CLASSE DA ENTIDADE db_systriggers
class cl_systriggers { 
   // cria variaveis de erro 
   var $rotulo     = null; 
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
   var $codtrigger = 0; 
   var $nometrigger = null; 
   var $quandotrigger = null; 
   var $erro = null; 
   var $codfuncao = 0; 
   var $codarq = 0; 
   var $eventotrigger = null; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 codtrigger = int4 = Código 
                 nometrigger = varchar(50) = Nome 
                 quandotrigger = varchar(6) = Quando 
                 erro = char(6) = Erro 
                 codfuncao = int4 = Código Função 
                 codarq = int4 = Codigo Arquivo 
                 eventotrigger = varchar(40) = Evento 
                 ";
   //funcao construtor da classe 
   function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulo          =   new \std\rotulo("db_systriggers"); 
     $this->pagina_retorno  =   basename($_SERVER["PHP_SELF"]);
   }
   //funcao erro 
   function erro($mostra,$retorna) { 
     if(($this->erro_status == "0") or ($mostra == true and $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }
   // funcao para atualizar campos
   function atualizacampos($exclusao=false) {
     if($exclusao==false){
       $this->codtrigger = ($this->codtrigger == ""?@$GLOBALS["codtrigger"]:$this->codtrigger);
       $this->nometrigger = ($this->nometrigger == ""?@$GLOBALS["nometrigger"]:$this->nometrigger);
       $this->quandotrigger = ($this->quandotrigger == ""?@$GLOBALS["quandotrigger"]:$this->quandotrigger);
       $this->erro = ($this->erro == ""?@$GLOBALS["trigger_erro"]:$this->erro);
       $this->codfuncao = ($this->codfuncao == ""?@$GLOBALS["codfuncao"]:$this->codfuncao);
       $this->codarq = ($this->codarq == ""?@$GLOBALS["codarq"]:$this->codarq);
       $this->eventotrigger = ($this->eventotrigger == ""?@$GLOBALS["eventotrigger"]:$this->eventotrigger);
     }else{
       $this->codtrigger = ($this->codtrigger == ""?@$GLOBALS["codtrigger"]:$this->codtrigger);
     }
   }
   // funcao para inclusao
   function incluir ($codtrigger){ 
      $this->atualizacampos();
     if($this->nometrigger == null ){ 
       $this->erro_sql = " Campo Nome nao Informado.";
       $this->erro_campo = "nometrigger";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->quandotrigger == null ){ 
       $this->erro_sql = " Campo Quando nao Informado.";
       $this->erro_campo = "quandotrigger";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->erro == null ){ 
       $this->erro_sql = " Campo Erro nao Informado.";
       $this->erro_campo = "erro";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->codfuncao == null ){ 
       $this->erro_sql = " Campo Código Função nao Informado.";
       $this->erro_campo = "codfuncao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->codarq == null ){ 
       $this->erro_sql = " Campo Codigo Arquivo nao Informado.";
       $this->erro_campo = "codarq";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->eventotrigger == null ){ 
       $this->erro_sql = " Campo Evento nao Informado.";
       $this->erro_campo = "eventotrigger";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($codtrigger == "" or $codtrigger == null ){
       $result = db_stdlib::db_query("select nextval('db_systriggers_codtrigger_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: db_systriggers_codtrigger_seq do campo: codtrigger"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->codtrigger = db_stdlib::lastInsertId(); 
     }else{
       $result = db_stdlib::db_query("select last_value from db_systriggers_codtrigger_seq");
       if(($result != false) and (db_stdlib::lastInsertId() < $codtrigger)){
         $this->erro_sql = " Campo codtrigger maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->codtrigger = $codtrigger; 
       }
     }
     if(($this->codtrigger == null) or ($this->codtrigger == "") ){ 
       $this->erro_sql = " Campo codtrigger nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into db_systriggers(
                                       codtrigger 
                                      ,nometrigger 
                                      ,quandotrigger 
                                      ,erro 
                                      ,codfuncao 
                                      ,codarq 
                                      ,eventotrigger 
                       )
                values (
                                $this->codtrigger 
                               ,'$this->nometrigger' 
                               ,'$this->quandotrigger' 
                               ,'$this->erro' 
                               ,$this->codfuncao 
                               ,$this->codarq 
                               ,'$this->eventotrigger' 
                      )";
     $result = db_stdlib::db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Triggers (Gatilhos) ($this->codtrigger) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Triggers (Gatilhos) já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Triggers (Gatilhos) ($this->codtrigger) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codtrigger;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= $result->rowCount();
     $resaco = $this->sql_record($this->sql_query_file($this->codtrigger));
     if(($resaco!=false)||($this->numrows!=0)){
        $linha = $resaco->fetch();
        
       $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = db_stdlib::lastInsertId();
       $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
       $resac = db_stdlib::db_query("insert into db_acountkey values($acount,779,'$this->codtrigger','I')");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,151,779,'','".addslashes($linha->codtrigger)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,151,780,'','".addslashes($linha->nometrigger)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,151,781,'','".addslashes($linha->quandotrigger)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,151,10738,'','".addslashes($linha->erro)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,151,774,'','".addslashes($linha->codfuncao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,151,759,'','".addslashes($linha->codarq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,151,782,'','".addslashes($linha->eventotrigger)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($codtrigger=null) { 
      $this->atualizacampos();
     $sql = " update db_systriggers set ";
     $virgula = "";
     if(trim($this->codtrigger)!="" or isset($GLOBALS["codtrigger"])){ 
       $sql  .= $virgula." codtrigger = $this->codtrigger ";
       $virgula = ",";
       if(trim($this->codtrigger) == null ){ 
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "codtrigger";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nometrigger)!="" or isset($GLOBALS["nometrigger"])){ 
       $sql  .= $virgula." nometrigger = '$this->nometrigger' ";
       $virgula = ",";
       if(trim($this->nometrigger) == null ){ 
         $this->erro_sql = " Campo Nome nao Informado.";
         $this->erro_campo = "nometrigger";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->quandotrigger)!="" or isset($GLOBALS["quandotrigger"])){ 
       $sql  .= $virgula." quandotrigger = '$this->quandotrigger' ";
       $virgula = ",";
       if(trim($this->quandotrigger) == null ){ 
         $this->erro_sql = " Campo Quando nao Informado.";
         $this->erro_campo = "quandotrigger";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->erro)!="" or isset($GLOBALS["erro"])){ 
       $sql  .= $virgula." erro = '$this->erro' ";
       $virgula = ",";
       if(trim($this->erro) == null ){ 
         $this->erro_sql = " Campo Erro nao Informado.";
         $this->erro_campo = "erro";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->codfuncao)!="" or isset($GLOBALS["codfuncao"])){ 
       $sql  .= $virgula." codfuncao = $this->codfuncao ";
       $virgula = ",";
       if(trim($this->codfuncao) == null ){ 
         $this->erro_sql = " Campo Código Função nao Informado.";
         $this->erro_campo = "codfuncao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->codarq)!="" or isset($GLOBALS["codarq"])){ 
       $sql  .= $virgula." codarq = $this->codarq ";
       $virgula = ",";
       if(trim($this->codarq) == null ){ 
         $this->erro_sql = " Campo Codigo Arquivo nao Informado.";
         $this->erro_campo = "codarq";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->eventotrigger)!="" or isset($GLOBALS["eventotrigger"])){ 
       $sql  .= $virgula." eventotrigger = '$this->eventotrigger' ";
       $virgula = ",";
       if(trim($this->eventotrigger) == null ){ 
         $this->erro_sql = " Campo Evento nao Informado.";
         $this->erro_campo = "eventotrigger";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($this->codtrigger!=null){
       $sql .= " codtrigger = $this->codtrigger";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->codtrigger));
     if($this->numrows>0){
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,779,'$this->codtrigger','A')");
         if(isset($GLOBALS["codtrigger"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,151,779,'".addslashes($linha->codtrigger)."','$this->codtrigger',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["nometrigger"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,151,780,'".addslashes($linha->nometrigger)."','$this->nometrigger',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["quandotrigger"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,151,781,'".addslashes($linha->quandotrigger)."','$this->quandotrigger',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["erro"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,151,10738,'".addslashes($linha->erro)."','$this->erro',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["codfuncao"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,151,774,'".addslashes($linha->codfuncao)."','$this->codfuncao',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["codarq"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,151,759,'".addslashes($linha->codarq)."','$this->codarq',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["eventotrigger"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,151,782,'".addslashes($linha->eventotrigger)."','$this->eventotrigger',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_stdlib::db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Triggers (Gatilhos) nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->codtrigger;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Triggers (Gatilhos) nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->codtrigger;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codtrigger;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = $result->rowCount();
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($codtrigger=null,$dbwhere=null) { 
     if($dbwhere==null or $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($codtrigger));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,779,'$codtrigger','E')");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,151,779,'','".addslashes($linha->codtrigger)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,151,780,'','".addslashes($linha->nometrigger)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,151,781,'','".addslashes($linha->quandotrigger)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,151,10738,'','".addslashes($linha->erro)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,151,774,'','".addslashes($linha->codfuncao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,151,759,'','".addslashes($linha->codarq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,151,782,'','".addslashes($linha->eventotrigger)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from db_systriggers
                    where ";
     $sql2 = "";
     if($dbwhere==null or $dbwhere ==""){
        if($codtrigger != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " codtrigger = $codtrigger ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_stdlib::db_query($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Triggers (Gatilhos) nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$codtrigger;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Triggers (Gatilhos) nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$codtrigger;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$codtrigger;
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
     if($result==false){
       $this->numrows    = 0;
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = $result->rowCount();
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:db_systriggers";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

   function sql_query ( $codtrigger=null,$campos="*",$ordem=null,$dbwhere=""){ 
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from db_systriggers ";
     $sql2 = "";
     if($dbwhere==""){
       if($codtrigger!=null ){
         $sql2 .= " where db_systriggers.codtrigger = $codtrigger "; 
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
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
   function sql_query_file ( $codtrigger=null,$campos="*",$ordem=null,$dbwhere=""){ 
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from db_systriggers ";
     $sql2 = "";
     if($dbwhere==""){
       if($codtrigger!=null ){
         $sql2 .= " where db_systriggers.codtrigger = $codtrigger "; 
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
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
}
?>