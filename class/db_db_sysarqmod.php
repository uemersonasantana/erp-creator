<?php
/**
 * db_db_sysarqmod
 *
 * @package   configuracao
 */

namespace classes;

use libs\db_stdlib;

//MODULO: configuracoes
//CLASSE DA ENTIDADE db_sysarqmod
class db_db_sysarqmod { 
  // cria variaveis de erro 
  var $rotulo          = null; 
  var $query_sql       = null; 
  var $numrows         = 0; 
  var $numrows_incluir = 0; 
  var $numrows_alterar = 0; 
  var $numrows_excluir = 0; 
  var $erro_status     = null; 
  var $erro_sql        = null; 
  var $erro_banco      = null;  
  var $erro_msg        = null;  
  var $erro_campo      = null;  
  var $pagina_retorno  = null; 
  // cria variáveis do arquivo 
  var $codmod          = 0; 
  var $codarq          = 0; 
  // cria propriedade com as variaveis do arquivo 
  var $campos          = "
                       codmod = int4 = Módulo 
                       codarq = int4 = Codigo Arquivo 
                       ";
   //funcao construtor da classe 
   function db_db_sysarqmod() { 
     //classes dos rotulos dos campos
     $this->rotulo          =   new \std\rotulo("db_sysarqmod"); 
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
       $this->codmod = ($this->codmod == ""?@$GLOBALS["codmod"]:$this->codmod);
       $this->codarq = ($this->codarq == ""?@$GLOBALS["codarq"]:$this->codarq);
     }else{
       $this->codmod = ($this->codmod == ""?@$GLOBALS["codmod"]:$this->codmod);
       $this->codarq = ($this->codarq == ""?@$GLOBALS["codarq"]:$this->codarq);
     }
   }
   // funcao para inclusao
   function incluir ($codmod,$codarq){ 
      $this->atualizacampos();
       $this->codmod = $codmod; 
       $this->codarq = $codarq; 
     if(($this->codmod == null) || ($this->codmod == "") ){ 
       $this->erro_sql = " Campo codmod nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->codarq == null) || ($this->codarq == "") ){ 
       $this->erro_sql = " Campo codarq nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into db_sysarqmod(
                                       codmod 
                                      ,codarq 
                       )
                values (
                                $this->codmod 
                               ,$this->codarq 
                      )";
     $result = db_stdlib::db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Modulo e Arquivos ($this->codmod."-".$this->codarq) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Modulo e Arquivos já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Modulo e Arquivos ($this->codmod."-".$this->codarq) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codmod."-".$this->codarq;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= $result->rowCount();
     $resaco = $this->sql_record($this->sql_query_file($this->codmod,$this->codarq));
     if(($resaco!=false)||($this->numrows!=0)){
        $linha = $resaco->fetch();
       $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = db_stdlib::lastInsertId();
       $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
       $resac = db_stdlib::db_query("insert into db_acountkey values($acount,748,'$this->codmod','I')");
       $resac = db_stdlib::db_query("insert into db_acountkey values($acount,759,'$this->codarq','I')");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,142,748,'','".addslashes($linha->codmod)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,142,759,'','".addslashes($linha->codarq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($codmod=null,$codarq=null) { 
      $this->atualizacampos();

      if( $codmod != null ) {
        $this->codmod = $codmod;
      } 
      if( $codarq != null) {
        $this->codarq = $codarq;
      }

     $sql = " update db_sysarqmod set ";
     $virgula = "";
     if(trim($this->codmod)!="" || isset($GLOBALS["codmod"])){ 
       $sql  .= $virgula." codmod = $this->codmod ";
       $virgula = ",";
       if(trim($this->codmod) == null ){ 
         $this->erro_sql = " Campo Módulo nao Informado.";
         $this->erro_campo = "codmod";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->codarq)!="" || isset($GLOBALS["codarq"])){ 
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
     $sql .= " where ";
     
     if($codarq!=null){
       $sql .= " codarq = $this->codarq";
     }
      
     if($codmod!=null){
       //$sql .= " and codmod = $this->codmod";
     }

     $resaco = $this->sql_record($this->sql_query_file(null,$this->codarq));
     if($this->numrows>0){
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,748,'$this->codmod','A')");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,759,'$this->codarq','A')");
         if(isset($GLOBALS["codmod"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,142,748,'".addslashes($linha->codmod)."','$this->codmod',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["codarq"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,142,759,'".addslashes($linha->codarq)."','$this->codarq',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     
     $result = db_stdlib::db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Modulo e Arquivos nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->codmod."-".$this->codarq;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Modulo e Arquivos nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->codmod."-".$this->codarq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codmod."-".$this->codarq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = $result->rowCount();
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($codmod=null,$codarq=null,$dbwhere=null) { 
    $this->atualizacampos();

    if ( $codmod != null ) {
      $this->codmod = $codmod;
    }

    if ( $codarq != null ) {
      $this->codarq = $codarq;
    }
    
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($codmod,$codarq));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,748,'$codmod','E')");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,759,'$codarq','E')");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,142,748,'','".addslashes($linha->codmod)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,142,759,'','".addslashes($linha->codarq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from db_sysarqmod
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($codmod != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " codmod = $codmod ";
        }
        if($codarq != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " codarq = $codarq ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_stdlib::db_query($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Modulo e Arquivos nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$codmod."-".$codarq;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Modulo e Arquivos nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$codmod."-".$codarq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$codmod."-".$codarq;
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
        $this->erro_sql   = "Record Vazio na Tabela:db_sysarqmod";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   function sql_query ( $codmod=null,$codarq=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from db_sysarqmod ";
     $sql .= "      inner join db_sysarquivo  on  db_sysarquivo.codarq = db_sysarqmod.codarq";
     $sql .= "      inner join db_sysmodulo  on  db_sysmodulo.codmod = db_sysarqmod.codmod";
     $sql2 = "";
     if($dbwhere==""){
       if($codmod!=null ){
         $sql2 .= " where db_sysarqmod.codmod = $codmod "; 
       } 
       if($codarq!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " db_sysarqmod.codarq = $codarq "; 
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
   function sql_query_file ( $codmod=null,$codarq=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from db_sysarqmod ";
     $sql2 = "";
     if($dbwhere==""){
       if($codmod!=null ){
         $sql2 .= " where db_sysarqmod.codmod = $codmod "; 
       } 
       if($codarq!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " db_sysarqmod.codarq = $codarq "; 
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