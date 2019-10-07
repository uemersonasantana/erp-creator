<?php
/**
 * cl_sysarqarq
 *
 * @package   configuracao
 */

namespace classes;

use libs\db_stdlib;

//MODULO: configuracoes
//CLASSE DA ENTIDADE db_sysarqarq
class cl_sysarqarq { 
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
   var $codarqpai = 0; 
   var $codarq = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 codarqpai = int4 = Código da Tabela Pai 
                 codarq = int4 = Codigo Arquivo 
                 ";
   //funcao construtor da classe 
   function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulo          =   new \std\rotulo("db_sysarqarq"); 
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
       $this->codarqpai = ($this->codarqpai == ""?@$GLOBALS["codarqpai"]:$this->codarqpai);
       $this->codarq = ($this->codarq == ""?@$GLOBALS["codarq"]:$this->codarq);
     }else{
       $this->codarqpai = ($this->codarqpai == ""?@$GLOBALS["codarqpai"]:$this->codarqpai);
       $this->codarq = ($this->codarq == ""?@$GLOBALS["codarq"]:$this->codarq);
     }
   }
   // funcao para inclusao
   function incluir ($codarqpai,$codarq){ 
      $this->atualizacampos();
       $this->codarqpai = $codarqpai; 
       $this->codarq = $codarq; 
     if(($this->codarqpai == null) || ($this->codarqpai == "") ){ 
       $this->erro_sql = " Campo codarqpai nao declarado.";
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
     $sql = "insert into db_sysarqarq(
                                       codarqpai 
                                      ,codarq 
                       )
                values (
                                $this->codarqpai 
                               ,$this->codarq 
                      )";
     $result = db_stdlib::db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Tabela Pai ($this->codarqpai."-".$this->codarq) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Tabela Pai já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Tabela Pai ($this->codarqpai."-".$this->codarq) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codarqpai."-".$this->codarq;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= $result->rowCount();
     $resaco = $this->sql_record($this->sql_query_file($this->codarqpai,$this->codarq));
     if(($resaco!=false)||($this->numrows!=0)){
      $linha = $resaco->fetch();
       $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = db_stdlib::lastInsertId();
       $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
       $resac = db_stdlib::db_query("insert into db_acountkey values($acount,2365,'$this->codarqpai','I')");
       $resac = db_stdlib::db_query("insert into db_acountkey values($acount,759,'$this->codarq','I')");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,387,2365,'','".addslashes($linha->codarqpai)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,387,759,'','".addslashes($linha->codarq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($codarqpai=null,$codarq=null) { 
      $this->atualizacampos();

      if( $codarqpai != null ) {
        $this->codarqpai = $codarqpai;
      } 
      if( $codarqpai != null) {
        $this->codarqpai = $codarqpai;
      }

     $sql = " update db_sysarqarq set ";
     $virgula = "";
     if(trim($this->codarqpai)!="" || isset($GLOBALS["codarqpai"])){ 
       $sql  .= $virgula." codarqpai = $this->codarqpai ";
       $virgula = ",";
       if(trim($this->codarqpai) == null ){ 
         $this->erro_sql = " Campo Código da Tabela Pai nao Informado.";
         $this->erro_campo = "codarqpai";
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
     
     if($this->codarq!=null){
       $sql .= " codarq = $this->codarq";
     }
     if($this->codarqpai!=null){
       //$sql .= " and codarqpai = $this->codarqpai";
     }

     $resaco = $this->sql_record($this->sql_query_file(null,$this->codarq));
     if($this->numrows>0){
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,2365,'$this->codarqpai','A')");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,759,'$this->codarq','A')");
         if(isset($GLOBALS["codarqpai"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,387,2365,'".addslashes($linha->codarqpai)."','$this->codarqpai',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["codarq"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,387,759,'".addslashes($linha->codarq)."','$this->codarq',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_stdlib::db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tabela Pai nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->codarqpai."-".$this->codarq;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Tabela Pai nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->codarqpai."-".$this->codarq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codarqpai."-".$this->codarq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = $result->rowCount();
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($codarqpai=null,$codarq=null,$dbwhere=null) { 
    $this->atualizacampos();

    if ( $codarqpai != null ) {
      $this->codarqpai = $codarqpai;
    }

    if ( $codarq != null ) {
      $this->codarq = $codarq;
    }

     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($this->codarqpai,$this->codarq));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,2365,'$this->codarqpai','E')");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,759,'$this->codarq','E')");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,387,2365,'','".addslashes($linha->codarqpai)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,387,759,'','".addslashes($linha->codarq)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from db_sysarqarq
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($this->codarqpai != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " codarqpai = $this->codarqpai ";
        }
        if($this->codarq != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " codarq = $this->codarq ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     
     $result = db_stdlib::db_query($sql.$sql2);
     
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tabela Pai nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$this->codarqpai."-".$this->codarq;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Tabela Pai nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$this->codarqpai."-".$this->codarq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codarqpai."-".$this->codarq;
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
        $this->erro_sql   = "Record Vazio na Tabela:db_sysarqarq";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   function sql_query ( $codarqpai=null,$codarq=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from db_sysarqarq ";
     $sql .= "      inner join db_sysarquivo  on  db_sysarquivo.codarq = db_sysarqarq.codarqpai";
     $sql2 = "";
     if($dbwhere==""){
       if($codarqpai!=null ){
         $sql2 .= " where db_sysarqarq.codarqpai = $codarqpai "; 
       } 
       if($codarq!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " db_sysarqarq.codarq = $codarq "; 
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
   function sql_query_file ( $codarqpai=null,$codarq=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from db_sysarqarq ";
     $sql2 = "";
     if($dbwhere==""){
       if($codarqpai!=null ){
         $sql2 .= " where db_sysarqarq.codarqpai = $codarqpai "; 
       } 
       if($codarq!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " db_sysarqarq.codarq = $codarq "; 
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
   function sql_query_filho ( $codarqpai=null,$codarq=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from db_sysarqarq ";
     $sql .= "      inner join db_sysarquivo  on  db_sysarquivo.codarq = db_sysarqarq.codarq";
     $sql2 = "";
     if($dbwhere==""){
       if($codarqpai!=null ){
         $sql2 .= " where db_sysarqarq.codarqpai = $codarqpai "; 
       } 
       if($codarq!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " db_sysarqarq.codarq = $codarq "; 
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