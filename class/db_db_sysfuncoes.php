<?php
/**
 * db_db_sysfuncoes
 *
 * @package   configuracao
 */

namespace classes;

use libs\db_stdlib;

//MODULO: configuracoes
//CLASSE DA ENTIDADE db_sysfuncoes
class db_db_sysfuncoes { 
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
   var $codfuncao = 0; 
   var $nomefuncao = null; 
   var $nomearquivo = null; 
   var $obsfuncao = null; 
   var $corpofuncao = null; 
   var $triggerfuncao = null; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 codfuncao = int4 = Código Função 
                 nomefuncao = varchar(100) = Nome 
                 nomearquivo = varchar(100) = Nome do arquivo 
                 obsfuncao = text = Observação 
                 corpofuncao = text = Corpo da Função 
                 triggerfuncao = char(1) = Tipo 
                 ";
   //funcao construtor da classe 
   function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulo          =  new \std\rotulo("db_sysfuncoes"); 
     $this->pagina_retorno  =  basename($_SERVER["PHP_SELF"]);
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
       $this->codfuncao = ($this->codfuncao == ""?@$GLOBALS["codfuncao"]:$this->codfuncao);
       $this->nomefuncao = ($this->nomefuncao == ""?@$GLOBALS["nomefuncao"]:$this->nomefuncao);
       $this->nomearquivo = ($this->nomearquivo == ""?@$GLOBALS["nomearquivo"]:$this->nomearquivo);
       $this->obsfuncao = ($this->obsfuncao == ""?@$GLOBALS["obsfuncao"]:$this->obsfuncao);
       $this->corpofuncao = ($this->corpofuncao == ""?@$GLOBALS["corpofuncao"]:$this->corpofuncao);
       $this->triggerfuncao = ($this->triggerfuncao == ""?@$GLOBALS["triggerfuncao"]:$this->triggerfuncao);

       $this->obsfuncao   = str_replace("'",'"',$this->obsfuncao);
       $this->corpofuncao = str_replace("'",'"',$this->corpofuncao);
     }else{
       $this->codfuncao = ($this->codfuncao == ""?@$GLOBALS["codfuncao"]:$this->codfuncao);
     }
   }
   // funcao para inclusao
   function incluir ($codfuncao){ 
      $this->atualizacampos();
     if($this->nomefuncao == null ){ 
       $this->erro_sql = " Campo Nome nao Informado.";
       $this->erro_campo = "nomefuncao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->nomearquivo == null ){ 
       $this->erro_sql = " Campo Nome do arquivo nao Informado.";
       $this->erro_campo = "nomearquivo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->obsfuncao == null ){ 
       $this->erro_sql = " Campo Observação nao Informado.";
       $this->erro_campo = "obsfuncao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->corpofuncao == null ){ 
       $this->erro_sql = " Campo Corpo da Função nao Informado.";
       $this->erro_campo = "corpofuncao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->triggerfuncao == null ){ 
       $this->erro_sql = " Campo Tipo nao Informado.";
       $this->erro_campo = "triggerfuncao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($codfuncao == "" || $codfuncao == null ){
       $result = db_stdlib::db_query("select nextval('db_sysfuncoes_codfuncao_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: db_sysfuncoes_codfuncao_seq do campo: codfuncao"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->codfuncao = db_stdlib::lastInsertId(); 
     }else{
       $result = db_stdlib::db_query("select last_value from db_sysfuncoes_codfuncao_seq");
       if(($result != false) and (db_stdlib::lastInsertId() < $this->codfuncao)){
         $this->erro_sql = " Campo codfuncao maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->codfuncao = $codfuncao; 
       }
     }
     if(($this->codfuncao == null) || ($this->codfuncao == "") ){ 
       $this->erro_sql = " Campo codfuncao nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into db_sysfuncoes(
                                       codfuncao 
                                      ,nomefuncao 
                                      ,nomearquivo 
                                      ,obsfuncao 
                                      ,corpofuncao 
                                      ,triggerfuncao 
                       )
                values (
                                $this->codfuncao 
                               ,'$this->nomefuncao' 
                               ,'$this->nomearquivo' 
                               ,'$this->obsfuncao' 
                               ,'$this->corpofuncao' 
                               ,'$this->triggerfuncao' 
                      )";
     $result = db_stdlib::db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Funcoes do sistema postgresql ($this->codfuncao) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Funcoes do sistema postgresql já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Funcoes do sistema postgresql ($this->codfuncao) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codfuncao;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= $result->rowCount();
     $resaco = $this->sql_record($this->sql_query_file($this->codfuncao));
     if(($resaco!=false)||($this->numrows!=0)){
      $linha = $resaco->fetch();
      
       $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = db_stdlib::lastInsertId();
       $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
       $resac = db_stdlib::db_query("insert into db_acountkey values($acount,774,'$this->codfuncao','I')");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,146,774,'','".addslashes($linha->codfuncao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,146,775,'','".addslashes($linha->nomefuncao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,146,9466,'','".addslashes($linha->nomearquivo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,146,776,'','".str_replace("'",'"',$linha->obsfuncao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,146,777,'','".str_replace("'",'"',$linha->corpofuncao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,146,778,'','".addslashes($linha->triggerfuncao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($codfuncao=null) { 
      $this->atualizacampos();

      if( $codfuncao != null ) {
        $this->codfuncao = $codfuncao;
      } 

     $sql = " update db_sysfuncoes set ";
     $virgula = "";
     if(trim($this->codfuncao)!="" || isset($GLOBALS["codfuncao"])){ 
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
     if(trim($this->nomefuncao)!="" || isset($GLOBALS["nomefuncao"])){ 
       $sql  .= $virgula." nomefuncao = '$this->nomefuncao' ";
       $virgula = ",";
       if(trim($this->nomefuncao) == null ){ 
         $this->erro_sql = " Campo Nome nao Informado.";
         $this->erro_campo = "nomefuncao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nomearquivo)!="" || isset($GLOBALS["nomearquivo"])){ 
       $sql  .= $virgula." nomearquivo = '$this->nomearquivo' ";
       $virgula = ",";
       if(trim($this->nomearquivo) == null ){ 
         $this->erro_sql = " Campo Nome do arquivo nao Informado.";
         $this->erro_campo = "nomearquivo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->obsfuncao)!="" || isset($GLOBALS["obsfuncao"])){ 
       $sql  .= $virgula." obsfuncao = '$this->obsfuncao' ";
       $virgula = ",";
       if(trim($this->obsfuncao) == null ){ 
         $this->erro_sql = " Campo Observação nao Informado.";
         $this->erro_campo = "obsfuncao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->corpofuncao)!="" || isset($GLOBALS["corpofuncao"])){ 
       $sql  .= $virgula." corpofuncao = '$this->corpofuncao' ";
       $virgula = ",";
       if(trim($this->corpofuncao) == null ){ 
         $this->erro_sql = " Campo Corpo da Função nao Informado.";
         $this->erro_campo = "corpofuncao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->triggerfuncao)!="" || isset($GLOBALS["triggerfuncao"])){ 
       $sql  .= $virgula." triggerfuncao = '$this->triggerfuncao' ";
       $virgula = ",";
       if(trim($this->triggerfuncao) == null ){ 
         $this->erro_sql = " Campo Tipo nao Informado.";
         $this->erro_campo = "triggerfuncao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if ( $this->codfuncao != null ) {
       $sql .= " codfuncao = $this->codfuncao";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->codfuncao));
     if($this->numrows>0){
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,774,'$this->codfuncao','A')");
         if(isset($GLOBALS["codfuncao"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,146,774,'".addslashes($linha->codfuncao)."','$this->codfuncao',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["nomefuncao"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,146,775,'".addslashes($linha->nomefuncao)."','$this->nomefuncao',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["nomearquivo"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,146,9466,'".addslashes($linha->nomearquivo)."','$this->nomearquivo',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["obsfuncao"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,146,776,'".str_replace("'",'"',$linha->obsfuncao)."','$this->obsfuncao',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["corpofuncao"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,146,777,'".str_replace("'",'"',$linha->corpofuncao)."','$this->corpofuncao',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["triggerfuncao"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,146,778,'".addslashes($linha->triggerfuncao)."','$this->triggerfuncao',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_stdlib::db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Funcoes do sistema postgresql nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->codfuncao;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Funcoes do sistema postgresql nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->codfuncao;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codfuncao;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = $result->rowCount();
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($codfuncao=null,$dbwhere=null) { 
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($codfuncao));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,774,'$codfuncao','E')");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,146,774,'','".addslashes($linha->codfuncao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,146,775,'','".addslashes($linha->nomefuncao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,146,9466,'','".addslashes($linha->nomearquivo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,146,776,'','".str_replace("'",'"',$linha->obsfuncao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,146,777,'','".str_replace("'",'"',$linha->corpofuncao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,146,778,'','".addslashes($linha->triggerfuncao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from db_sysfuncoes
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($codfuncao != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " codfuncao = $codfuncao ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_stdlib::db_query($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Funcoes do sistema postgresql nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$codfuncao;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Funcoes do sistema postgresql nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$codfuncao;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$codfuncao;
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
        $this->erro_sql   = "Record Vazio na Tabela:db_sysfuncoes";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   function sql_query ( $codfuncao=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from db_sysfuncoes ";
     $sql .= " left join db_sysfuncoescliente on db41_funcao = codfuncao ";
     $sql2 = "";
     if($dbwhere==""){
       if($codfuncao!=null ){
         $sql2 .= " where db_sysfuncoes.codfuncao = $codfuncao "; 
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
   function sql_query_file ( $codfuncao=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from db_sysfuncoes ";
     $sql2 = "";
     if($dbwhere==""){
       if($codfuncao!=null ){
         $sql2 .= " where db_sysfuncoes.codfuncao = $codfuncao "; 
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