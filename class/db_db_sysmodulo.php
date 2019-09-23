<?php
/**
 * db_db_sysmodulo
 *
 * @package   configuracao
 */

namespace classes;

use libs\db_stdlib;

//MODULO: configuracoes
//CLASSE DA ENTIDADE db_sysmodulo
class db_db_sysmodulo { 
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
   var $codmod = 0; 
   var $nomemod = null; 
   var $descricao = null; 
   var $dataincl_dia = null; 
   var $dataincl_mes = null; 
   var $dataincl_ano = null; 
   var $dataincl = null; 
   var $ativo = 'f'; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 codmod = int4 = Módulo 
                 nomemod = char(40) = Nome Módulo 
                 descricao = text = Descrição 
                 dataincl = date = Data Inclusão 
                 ativo = bool = Ativo 
                 ";
   // Funcao construtor da classe 
   function __construct() {  
     //classes dos rotulos dos campos
     $this->rotulo          =   new \std\rotulo("db_sysmodulo"); 
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
       $this->codmod    = ($this->codmod    == "" ? @$GLOBALS["codmod"]   : $this->codmod);
       $this->nomemod   = ($this->nomemod   == "" ? @$GLOBALS["nomemod"]  : $this->nomemod);
       $this->descricao = ($this->descricao == "" ? @$GLOBALS["descricao"]: $this->descricao);
       if($this->dataincl == ""){
         $this->dataincl_dia = ($this->dataincl_dia == ""?@$GLOBALS["dataincl_dia"]:$this->dataincl_dia);
         $this->dataincl_mes = ($this->dataincl_mes == ""?@$GLOBALS["dataincl_mes"]:$this->dataincl_mes);
         $this->dataincl_ano = ($this->dataincl_ano == ""?@$GLOBALS["dataincl_ano"]:$this->dataincl_ano);
         if($this->dataincl_dia != ""){
            $this->dataincl = $this->dataincl_ano."-".$this->dataincl_mes."-".$this->dataincl_dia;
         }
       }
       $this->ativo     = ($this->ativo   == "f"  ? @$GLOBALS["ativo"]    : $this->ativo);
     }else{
       $this->codmod    = ($this->codmod  == ""   ? @$GLOBALS["codmod"]   : $this->codmod);
     }
   }
   // funcao para inclusao
   function incluir (){ 
    $this->atualizacampos(); 
     if($this->nomemod == null ){  
       $this->erro_sql = " Campo Nome Módulo não Informado.";
       $this->erro_campo = "nomemod";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->descricao == null ){ 
       $this->erro_sql = " Campo Descrição não Informado.";
       $this->erro_campo = "descricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->dataincl == null ){ 
       $this->erro_sql = " Campo Data Inclusão não Informado.";
       $this->erro_campo = "dataincl_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ativo == null ){ 
       $this->erro_sql = " Campo Ativo nao Informado.";
       $this->erro_campo = "ativo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->codmod == "" or $this->codmod == null ){
       $result = db_stdlib::db_query("select nextval('db_sysmodulo_codmod_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: db_sysmodulo_codmod_seq do campo: codmod"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->codmod = db_stdlib::lastInsertId(); 
     }else{
       $result = db_stdlib::db_query("select last_value from db_sysmodulo_codmod_seq");
       if(($result != false) and (pg_result($result,0,0) < $this->codmod)){
         $this->erro_sql = " Campo codmod maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->codmod = $codmod; 
       }
     }
     if(($this->codmod == null) or ($this->codmod == "") ){ 
       $this->erro_sql = " Campo codmod nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = "insert into db_sysmodulo(
                                       codmod 
                                      ,nomemod 
                                      ,descricao 
                                      ,dataincl 
                                      ,ativo 
                       )
                values (
                                $this->codmod 
                               ,'$this->nomemod' 
                               ,'$this->descricao' 
                               ,'$this->dataincl' 
                               ,'$this->ativo' 
                      )";

     $result = db_stdlib::db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Modulos da documentacao do Sistema ($this->codmod) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Modulos da documentacao do Sistema já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Modulos da documentacao do Sistema ($this->codmod) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codmod;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= $result->rowCount();
     $resaco = $this->sql_record($this->sql_query_file($this->codmod));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = db_stdlib::lastInsertId();
       $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
       $resac = db_stdlib::db_query("insert into db_acountkey values($acount,748,'$this->codmod','I')");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,148,748,'','".AddSlashes(pg_result($resaco,0,'codmod'))."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,148,749,'','".AddSlashes(pg_result($resaco,0,'nomemod'))."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,148,750,'','".AddSlashes(pg_result($resaco,0,'descricao'))."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,148,751,'','".AddSlashes(pg_result($resaco,0,'dataincl'))."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,148,8975,'','".AddSlashes(pg_result($resaco,0,'ativo'))."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar () { 
     self::atualizacampos();

     $sql = " update db_sysmodulo set ";
     $virgula = "";

     if( trim($this->codmod) != "" ) {  
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
     if( trim($this->nomemod) != "" ) { 
       $sql  .= $virgula." nomemod = '$this->nomemod' ";
       $virgula = ",";
       if(trim($this->nomemod) == null ){ 
         $this->erro_sql = " Campo Nome Módulo nao Informado.";
         $this->erro_campo = "nomemod";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if( trim($this->descricao) != "" ) { 
       $sql  .= $virgula." descricao = '$this->descricao' ";
       $virgula = ",";
       if(trim($this->descricao) == null ){ 
         $this->erro_sql = " Campo Descrição nao Informado.";
         $this->erro_campo = "descricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if( trim($this->dataincl) != "" ) { 
       $sql  .= $virgula." dataincl = '$this->dataincl' ";
       $virgula = ",";
       if(trim($this->dataincl) == null ){ 
         $this->erro_sql = " Campo Data Inclusão nao Informado.";
         $this->erro_campo = "dataincl_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     } 
     if( trim($this->ativo) != "" ) { 
       $sql  .= $virgula." ativo = '$this->ativo' ";
       $virgula = ",";
       if(trim($this->ativo) == null ){ 
         $this->erro_sql = " Campo Ativo nao Informado.";
         $this->erro_campo = "ativo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

    $sql .= " where ";
    $sql .= " codmod = $this->codmod";
     
    $resaco = self::sql_record(self::sql_query_file($this->codmod));

    if( $this->numrows > 0 ) {
      foreach ( $resaco as $linha) {
        $resac  = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
        $acount = db_stdlib::lastInsertId();
        $resac  = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
        $resac = db_stdlib::db_query("insert into db_acountkey values($acount,748,'$this->codmod','A')");
        
        if(isset($GLOBALS["codmod"]))
         $resac = db_stdlib::db_query("insert into db_acount values($acount,148,748,'".addslashes($linha->codmod)."','$this->codmod',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
        if(isset($GLOBALS["nomemod"]))
         $resac = db_stdlib::db_query("insert into db_acount values($acount,148,749,'".addslashes($linha->nomemod)."','$this->nomemod',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
        if(isset($GLOBALS["descricao"]))
         $resac = db_stdlib::db_query("insert into db_acount values($acount,148,750,'".addslashes($linha->descricao)."','$this->descricao',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
        if(isset($GLOBALS["dataincl"]))
         $resac = db_stdlib::db_query("insert into db_acount values($acount,148,751,'".addslashes($linha->dataincl)."','$this->dataincl',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
        if(isset($GLOBALS["ativo"]))
         $resac = db_stdlib::db_query("insert into db_acount values($acount,148,8975,'".addslashes($linha->ativo)."','$this->ativo',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
      }
    }

     $result = db_stdlib::db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Modulos da documentacao do Sistema nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->codmod;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if( $result->rowCount() == 0 ){
         $this->erro_banco = "";
         $this->erro_sql = "Modulos da documentacao do Sistema nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->codmod;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codmod;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = $result->rowCount();
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($dbwhere=null) {
      $this->atualizacampos();
     if($dbwhere==null or $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($this->codmod));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if ( $resaco!=false || $this->numrows!=0 ) {
      foreach ( $resaco as $linha) {
        $resac  = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
        $acount = db_stdlib::lastInsertId();
        $resac  = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");

        $resac = db_stdlib::db_query("insert into db_acountkey values($acount,748,'$this->codmod','E')");
        $resac = db_stdlib::db_query("insert into db_acount values($acount,148,748,'','".addslashes($linha->codmod)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
        $resac = db_stdlib::db_query("insert into db_acount values($acount,148,749,'','".addslashes($linha->nomemod)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
        $resac = db_stdlib::db_query("insert into db_acount values($acount,148,750,'','".addslashes($linha->descricao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
        $resac = db_stdlib::db_query("insert into db_acount values($acount,148,751,'','".addslashes($linha->dataincl)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
        $resac = db_stdlib::db_query("insert into db_acount values($acount,148,8975,'','".addslashes($linha->ativo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
      }
     }
     $sql = " delete from db_sysmodulo
                    where ";
     $sql2 = "";

     if($dbwhere==null or $dbwhere ==""){
        if($this->codmod != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " codmod = $this->codmod ";
        }
     }else{
       $sql2 = $dbwhere;
     }

     $result = db_stdlib::db_query($sql.$sql2);

     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Modulos da documentacao do Sistema nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql  .= "Valores : ".$codmod;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Modulos da documentacao do Sistema nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$this->codmod;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codmod;
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
        $this->erro_sql   = "Record Vazio na Tabela:db_sysmodulo";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   function sql_query ( $codmod=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from db_sysmodulo ";
     $sql2 = "";
     if($dbwhere==""){
       if($codmod!=null ){
         $sql2 .= " where db_sysmodulo.codmod = $codmod "; 
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
   function sql_query_file ( $codmod=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from db_sysmodulo ";
     $sql2 = "";
     if($dbwhere==""){
       if($codmod!=null ){
         $sql2 .= " where db_sysmodulo.codmod = $codmod "; 
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