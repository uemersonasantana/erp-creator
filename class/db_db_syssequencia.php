<?php
/**
 * db_db_syssequencia
 *
 * @package   configuracao
 */

namespace classes;

use libs\db_stdlib;

//CLASSE DA ENTIDADE db_syssequencia
class db_db_syssequencia { 
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
   var $codsequencia = 0; 
   var $nomesequencia = null; 
   var $incrseq = 0; 
   var $minvalueseq = 0; 
   var $maxvalueseq = 0; 
   var $startseq = 0; 
   var $cacheseq = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 codsequencia = int4 = Código 
                 nomesequencia = varchar(100) = Nome 
                 incrseq = int4 = Incrementa 
                 minvalueseq = int4 = Valor Mínimo 
                 maxvalueseq = int8 = Valor Máximo 
                 startseq = int4 = Numero para Inicial 
                 cacheseq = int4 = Cache 
                 ";
   //funcao construtor da classe 
   function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulo          =   new \std\rotulo("db_syssequencia"); 
     $this->pagina_retorno  =   basename($_SERVER["PHP_SELF"]);
   }
   //funcao erro 
   function erro($mostra,$retorna) { 
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }
   // funcao para atualizar campos
   function atualizacampos($exclusao=false) {
     if($exclusao==false){
       $this->codsequencia = ($this->codsequencia == ""?@$GLOBALS["codsequencia"]:$this->codsequencia);
       $this->nomesequencia = ($this->nomesequencia == ""?@$GLOBALS["nomesequencia"]:$this->nomesequencia);
       $this->incrseq = ($this->incrseq == ""?@$GLOBALS["incrseq"]:$this->incrseq);
       $this->minvalueseq = ($this->minvalueseq == ""?@$GLOBALS["minvalueseq"]:$this->minvalueseq);
       $this->maxvalueseq = ($this->maxvalueseq == ""?@$GLOBALS["maxvalueseq"]:$this->maxvalueseq);
       $this->startseq = ($this->startseq == ""?@$GLOBALS["startseq"]:$this->startseq);
       $this->cacheseq = ($this->cacheseq == ""?@$GLOBALS["cacheseq"]:$this->cacheseq);
     }else{
       $this->codsequencia = ($this->codsequencia == ""?@$GLOBALS["codsequencia"]:$this->codsequencia);
     }
   }
   // funcao para inclusao
   function incluir ($codsequencia=null){ 
      $this->atualizacampos();

    if ( $codsequencia!=null ) {
      $this->codsequencia = $codsequencia;
    }

     if($this->nomesequencia == null ){ 
       $this->erro_sql = " Campo Nome nao Informado.";
       $this->erro_campo = "nomesequencia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->incrseq == null ){ 
       $this->erro_sql = " Campo Incrementa nao Informado.";
       $this->erro_campo = "incrseq";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->minvalueseq == null ){ 
       $this->erro_sql = " Campo Valor Mínimo nao Informado.";
       $this->erro_campo = "minvalueseq";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->maxvalueseq == null ){ 
       $this->erro_sql = " Campo Valor Máximo nao Informado.";
       $this->erro_campo = "maxvalueseq";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->startseq == null ){ 
       $this->erro_sql = " Campo Numero para Inicial nao Informado.";
       $this->erro_campo = "startseq";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->cacheseq == null ){ 
       $this->erro_sql = " Campo Cache nao Informado.";
       $this->erro_campo = "cacheseq";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
      
     if(($this->codsequencia == null) || ($this->codsequencia == "") ){ 
       $this->erro_sql = " Campo codsequencia nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if ( (INT)$this->codsequencia == 0 ) {
        $this->codsequencia = db_stdlib::db_query("SELECT codsequencia FROM db_syssequencia ORDER BY codsequencia DESC LIMIT 1")->fetch()->codsequencia;
        $this->codsequencia = $this->codsequencia+1;
     }

     $sql = "insert into db_syssequencia(
                                       codsequencia 
                                      ,nomesequencia 
                                      ,incrseq 
                                      ,minvalueseq 
                                      ,maxvalueseq 
                                      ,startseq 
                                      ,cacheseq 
                       )
                values (
                                $this->codsequencia 
                               ,'$this->nomesequencia' 
                               ,$this->incrseq 
                               ,$this->minvalueseq 
                               ,$this->maxvalueseq 
                               ,$this->startseq 
                               ,$this->cacheseq 
                      )";
     $result = db_stdlib::db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Sequencias para campos ($this->codsequencia) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Sequencias para campos já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Sequencias para campos ($this->codsequencia) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codsequencia;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= $result->rowCount();
     $resaco = $this->sql_record($this->sql_query_file($this->codsequencia));
     if(($resaco!=false)||($this->numrows!=0)){
      $linha = $resaco->fetch();
      
       $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = db_stdlib::lastInsertId();
       $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
       $resac = db_stdlib::db_query("insert into db_acountkey values($acount,766,'$this->codsequencia','I')");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,150,766,'','".addslashes($linha->codsequencia)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,150,767,'','".addslashes($linha->nomesequencia)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,150,768,'','".addslashes($linha->incrseq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,150,769,'','".addslashes($linha->minvalueseq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,150,770,'','".addslashes($linha->maxvalueseq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,150,771,'','".addslashes($linha->startseq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,150,772,'','".addslashes($linha->cacheseq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($codsequencia=null) { 
      $this->atualizacampos();
     $sql = " update db_syssequencia set ";
     $virgula = "";
     if(trim($this->codsequencia)!="" || isset($GLOBALS["codsequencia"])){ 
       $sql  .= $virgula." codsequencia = $this->codsequencia ";
       $virgula = ",";
       if(trim($this->codsequencia) == null ){ 
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "codsequencia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nomesequencia)!="" || isset($GLOBALS["nomesequencia"])){ 
       $sql  .= $virgula." nomesequencia = '$this->nomesequencia' ";
       $virgula = ",";
       if(trim($this->nomesequencia) == null ){ 
         $this->erro_sql = " Campo Nome nao Informado.";
         $this->erro_campo = "nomesequencia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->incrseq)!="" || isset($GLOBALS["incrseq"])){ 
       $sql  .= $virgula." incrseq = $this->incrseq ";
       $virgula = ",";
       if(trim($this->incrseq) == null ){ 
         $this->erro_sql = " Campo Incrementa nao Informado.";
         $this->erro_campo = "incrseq";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->minvalueseq)!="" || isset($GLOBALS["minvalueseq"])){ 
       $sql  .= $virgula." minvalueseq = $this->minvalueseq ";
       $virgula = ",";
       if(trim($this->minvalueseq) == null ){ 
         $this->erro_sql = " Campo Valor Mínimo nao Informado.";
         $this->erro_campo = "minvalueseq";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->maxvalueseq)!="" || isset($GLOBALS["maxvalueseq"])){ 
       $sql  .= $virgula." maxvalueseq = $this->maxvalueseq ";
       $virgula = ",";
       if(trim($this->maxvalueseq) == null ){ 
         $this->erro_sql = " Campo Valor Máximo nao Informado.";
         $this->erro_campo = "maxvalueseq";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->startseq)!="" || isset($GLOBALS["startseq"])){ 
       $sql  .= $virgula." startseq = $this->startseq ";
       $virgula = ",";
       if(trim($this->startseq) == null ){ 
         $this->erro_sql = " Campo Numero para Inicial nao Informado.";
         $this->erro_campo = "startseq";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->cacheseq)!="" || isset($GLOBALS["cacheseq"])){ 
       $sql  .= $virgula." cacheseq = $this->cacheseq ";
       $virgula = ",";
       if(trim($this->cacheseq) == null ){ 
         $this->erro_sql = " Campo Cache nao Informado.";
         $this->erro_campo = "cacheseq";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($codsequencia!=null){
       $sql .= " codsequencia = $this->codsequencia";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->codsequencia));
     if($this->numrows>0){
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,766,'$this->codsequencia','A')");
         if(isset($GLOBALS["codsequencia"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,150,766,'".addslashes($linha->codsequencia)."','$this->codsequencia',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["nomesequencia"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,150,767,'".addslashes($linha->nomesequencia)."','$this->nomesequencia',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["incrseq"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,150,768,'".addslashes($linha->incrseq)."','$this->incrseq',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["minvalueseq"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,150,769,'".addslashes($linha->minvalueseq)."','$this->minvalueseq',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["maxvalueseq"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,150,770,'".addslashes($linha->maxvalueseq)."','$this->maxvalueseq',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["startseq"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,150,771,'".addslashes($linha->startseq)."','$this->startseq',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["cacheseq"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,150,772,'".addslashes($linha->cacheseq)."','$this->cacheseq',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_stdlib::db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Sequencias para campos nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->codsequencia;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Sequencias para campos nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->codsequencia;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codsequencia;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = $result->rowCount();
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($codsequencia=null,$dbwhere=null) { 
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($codsequencia));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,766,'$codsequencia','E')");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,150,766,'','".addslashes($linha->codsequencia)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,150,767,'','".addslashes($linha->nomesequencia)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,150,768,'','".addslashes($linha->incrseq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,150,769,'','".addslashes($linha->minvalueseq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,150,770,'','".addslashes($linha->maxvalueseq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,150,771,'','".addslashes($linha->startseq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,150,772,'','".addslashes($linha->cacheseq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from db_syssequencia
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($codsequencia != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " codsequencia = $codsequencia ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_stdlib::db_query($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Sequencias para campos nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$codsequencia;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Sequencias para campos nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$codsequencia;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$codsequencia;
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
        $this->erro_sql   = "Record Vazio na Tabela:db_syssequencia";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    function sql_query_file ( $codsequencia=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from db_syssequencia ";
     $sql2 = "";
     if($dbwhere==""){
       if($codsequencia!=null ){
         $sql2 .= " where db_syssequencia.codsequencia = $codsequencia "; 
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