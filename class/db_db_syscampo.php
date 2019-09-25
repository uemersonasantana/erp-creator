<?php
/**
 * db_db_syscampo
 *
 * @package   configuracao
 */

namespace classes;

use libs\db_stdlib;

//MODULO: configuracoes
//CLASSE DA ENTIDADE db_syscampo
class db_db_syscampo { 
   // cria variaveis de erro 
   var $rotulocl     = null; 
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
   var $codcam = 0; 
   var $nomecam = null; 
   var $conteudo = null; 
   var $descricao = null; 
   var $valorinicial = null; 
   var $rotulo = null; 
   var $tamanho = 0; 
   var $nulo = 'f'; 
   var $maiusculo = 'f'; 
   var $autocompl = 'f'; 
   var $aceitatipo = 0; 
   var $tipoobj = null; 
   var $rotulorel = null; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 codcam = int4 = Código 
                 nomecam = char(40) = Nome 
                 conteudo = char(40) = Tipo Campo 
                 descricao = text = Descrição 
                 valorinicial = varchar(100) = Valor Inicial 
                 rotulo = varchar(50) = Rótulo 
                 tamanho = int4 = Tamanho 
                 nulo = bool = Aceita Nulo 
                 maiusculo = bool = Maiúsculo 
                 autocompl = bool = Auto-completar 
                 aceitatipo = int4 = Valida 
                 tipoobj = varchar(20) = Obj. Formulário 
                 rotulorel = varchar(40) = Rótulo relatório 
                 ";

   //funcao construtor da classe 
   function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulocl        =   new \std\rotulo("db_syscampo"); 
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
       $this->codcam = ($this->codcam == ""?@$GLOBALS["codcam"]:$this->codcam);
       $this->nomecam = ($this->nomecam == ""?@$GLOBALS["nomecam"]:$this->nomecam);
       $this->conteudo = ($this->conteudo == ""?@$GLOBALS["conteudo"]:$this->conteudo);
       $this->descricao = ($this->descricao == ""?@$GLOBALS["descricao"]:$this->descricao);
       $this->valorinicial = ($this->valorinicial == ""?@$GLOBALS["valorinicial"]:$this->valorinicial);
       $this->rotulo = ($this->rotulo == ""?@$GLOBALS["rotulo"]:$this->rotulo);
       $this->tamanho = ($this->tamanho == ""?@$GLOBALS["tamanho"]:$this->tamanho);
       $this->nulo = ($this->nulo == "f"?@$GLOBALS["nulo"]:$this->nulo);
       $this->maiusculo = ($this->maiusculo == "f"?@$GLOBALS["maiusculo"]:$this->maiusculo);
       $this->autocompl = ($this->autocompl == "f"?@$GLOBALS["autocompl"]:$this->autocompl);
       $this->aceitatipo = ($this->aceitatipo == ""?@$GLOBALS["aceitatipo"]:$this->aceitatipo);
       $this->tipoobj = ($this->tipoobj == ""?@$GLOBALS["tipoobj"]:$this->tipoobj);
       $this->rotulorel = ($this->rotulorel == ""?@$GLOBALS["rotulorel"]:$this->rotulorel);
     }else{
       $this->codcam = ($this->codcam == ""?@$GLOBALS["codcam"]:$this->codcam);
     }
   }
   // funcao para inclusao
   function incluir ($codcam=null){ 
      $this->atualizacampos();
     if($this->nomecam == null ){ 
       $this->erro_sql = " Campo Nome nao Informado.";
       $this->erro_campo = "nomecam";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->conteudo == null ){ 
       $this->erro_sql = " Campo Tipo Campo nao Informado.";
       $this->erro_campo = "conteudo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->descricao == null ){ 
       $this->erro_sql = " Campo Descrição nao Informado.";
       $this->erro_campo = "descricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rotulo == null ){ 
       $this->erro_sql = " Campo Rótulo nao Informado.";
       $this->erro_campo = "rotulo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if( $this->tamanho == null ){ 
       $this->tamanho = "0";
     }
     if( ( $this->conteudo == 'char' or $this->conteudo == 'varchar') and $this->tamanho == null ) { 
       $this->erro_sql = " Campo tamanho não pode ser vazio para o tipo: ".$this->conteudo;
       $this->erro_campo = "rotulo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->nulo == null ){ 
       $this->erro_sql = " Campo Aceita Nulo nao Informado.";
       $this->erro_campo = "nulo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->maiusculo == null ){ 
       $this->erro_sql = " Campo Maiúsculo nao Informado.";
       $this->erro_campo = "maiusculo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->autocompl == null ){ 
       $this->erro_sql = " Campo Auto-completar nao Informado.";
       $this->erro_campo = "autocompl";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->aceitatipo == null ){ 
       $this->erro_sql = " Campo Valida nao Informado.";
       $this->erro_campo = "aceitatipo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->tipoobj == null ){ 
       $this->erro_sql = " Campo Obj. Formulário nao Informado.";
       $this->erro_campo = "tipoobj";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rotulorel == null ){ 
       $this->erro_sql = " Campo Rótulo relatório nao Informado.";
       $this->erro_campo = "rotulorel";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($codcam == "" or $codcam == null ){
       $result = db_stdlib::db_query("select nextval('db_syscampo_codcam_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: db_syscampo_codcam_seq do campo: codcam"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->codcam = db_stdlib::lastInsertId(); 
     }else{
       $result = db_stdlib::db_query("select last_value from db_syscampo_codcam_seq");
       if(($result != false) and ( db_stdlib::lastInsertId() < $codcam)){
         $this->erro_sql = " Campo codcam maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->codcam = $codcam; 
       }
     }
     if(($this->codcam == null) or ($this->codcam == "") ){ 
       $this->erro_sql = " Campo codcam nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into db_syscampo(
                                       codcam 
                                      ,nomecam 
                                      ,conteudo 
                                      ,descricao 
                                      ,valorinicial 
                                      ,rotulo 
                                      ,tamanho 
                                      ,nulo 
                                      ,maiusculo 
                                      ,autocompl 
                                      ,aceitatipo 
                                      ,tipoobj 
                                      ,rotulorel 
                       )
                values (
                                $this->codcam 
                               ,'$this->nomecam' 
                               ,'$this->conteudo' 
                               ,'$this->descricao' 
                               ,'$this->valorinicial' 
                               ,'$this->rotulo' 
                               ,$this->tamanho 
                               ,'$this->nulo' 
                               ,'$this->maiusculo' 
                               ,'$this->autocompl' 
                               ,$this->aceitatipo 
                               ,'$this->tipoobj' 
                               ,'$this->rotulorel' 
                      )";
     $result = db_stdlib::db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Campos das tabelas ($this->codcam) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Campos das tabelas já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Campos das tabelas ($this->codcam) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codcam;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= $result->rowCount();
     $resaco = $this->sql_record($this->sql_query_file($this->codcam));
     if(($resaco!=false)or($this->numrows!=0)){
       $linha = $resaco->fetch();

       $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = db_stdlib::lastInsertId();
       $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
       $resac = db_stdlib::db_query("insert into db_acountkey values($acount,752,'$this->codcam','I')");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,752,'','".addslashes($linha->codcam)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,753,'','".addslashes($linha->nomecam)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,754,'','".addslashes($linha->conteudo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,750,'','".addslashes($linha->descricao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,755,'','".addslashes($linha->valorinicial)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,756,'','".addslashes($linha->rotulo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,757,'','".addslashes($linha->tamanho)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,758,'','".addslashes($linha->nulo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,2252,'','".addslashes($linha->maiusculo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,2253,'','".addslashes($linha->autocompl)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,2256,'','".addslashes($linha->aceitatipo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,2438,'','".addslashes($linha->tipoobj)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       $resac = db_stdlib::db_query("insert into db_acount values($acount,144,4792,'','".addslashes($linha->rotulorel)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($codcam=null) { 
      $this->atualizacampos();
     $sql = " update db_syscampo set ";
     $virgula = "";
     if(trim($this->codcam)!="" or isset($GLOBALS["codcam"])){ 
       $sql  .= $virgula." codcam = $this->codcam ";
       $virgula = ",";
       if(trim($this->codcam) == null ){ 
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "codcam";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nomecam)!="" or isset($GLOBALS["nomecam"])){ 
       $sql  .= $virgula." nomecam = '$this->nomecam' ";
       $virgula = ",";
       if(trim($this->nomecam) == null ){ 
         $this->erro_sql = " Campo Nome nao Informado.";
         $this->erro_campo = "nomecam";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->conteudo)!="" or isset($GLOBALS["conteudo"])){ 
       $sql  .= $virgula." conteudo = '$this->conteudo' ";
       $virgula = ",";
       if(trim($this->conteudo) == null ){ 
         $this->erro_sql = " Campo Tipo Campo nao Informado.";
         $this->erro_campo = "conteudo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->descricao)!="" or isset($GLOBALS["descricao"])){ 
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
     if(trim($this->valorinicial)!="" or isset($GLOBALS["valorinicial"])){ 
       $sql  .= $virgula." valorinicial = '$this->valorinicial' ";
       $virgula = ",";
     }
     if(trim($this->rotulo)!="" or isset($GLOBALS["rotulo"])){ 
       $sql  .= $virgula." rotulo = '$this->rotulo' ";
       $virgula = ",";
       if(trim($this->rotulo) == null ){ 
         $this->erro_sql = " Campo Rótulo nao Informado.";
         $this->erro_campo = "rotulo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->tamanho)!="" or isset($GLOBALS["tamanho"])){ 
        if(trim($this->tamanho)=="" and isset($GLOBALS["tamanho"])){ 
           $this->tamanho = "0" ; 
        } 
       $sql  .= $virgula." tamanho = $this->tamanho ";
       $virgula = ",";
     }
     if(trim($this->nulo)!="" or isset($GLOBALS["nulo"])){ 
       $sql  .= $virgula." nulo = '$this->nulo' ";
       $virgula = ",";
       if(trim($this->nulo) == null ){ 
         $this->erro_sql = " Campo Aceita Nulo nao Informado.";
         $this->erro_campo = "nulo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->maiusculo)!="" or isset($GLOBALS["maiusculo"])){ 
       $sql  .= $virgula." maiusculo = '$this->maiusculo' ";
       $virgula = ",";
       if(trim($this->maiusculo) == null ){ 
         $this->erro_sql = " Campo Maiúsculo nao Informado.";
         $this->erro_campo = "maiusculo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->autocompl)!="" or isset($GLOBALS["autocompl"])){ 
       $sql  .= $virgula." autocompl = '$this->autocompl' ";
       $virgula = ",";
       if(trim($this->autocompl) == null ){ 
         $this->erro_sql = " Campo Auto-completar nao Informado.";
         $this->erro_campo = "autocompl";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->aceitatipo)!="" or isset($GLOBALS["aceitatipo"])){ 
       $sql  .= $virgula." aceitatipo = $this->aceitatipo ";
       $virgula = ",";
       if(trim($this->aceitatipo) == null ){ 
         $this->erro_sql = " Campo Valida nao Informado.";
         $this->erro_campo = "aceitatipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->tipoobj)!="" or isset($GLOBALS["tipoobj"])){ 
       $sql  .= $virgula." tipoobj = '$this->tipoobj' ";
       $virgula = ",";
       if(trim($this->tipoobj) == null ){ 
         $this->erro_sql = " Campo Obj. Formulário nao Informado.";
         $this->erro_campo = "tipoobj";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rotulorel)!="" or isset($GLOBALS["rotulorel"])){ 
       $sql  .= $virgula." rotulorel = '$this->rotulorel' ";
       $virgula = ",";
       if(trim($this->rotulorel) == null ){ 
         $this->erro_sql = " Campo Rótulo relatório nao Informado.";
         $this->erro_campo = "rotulorel";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($codcam!=null){
       $sql .= " codcam = $this->codcam";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->codcam));
     if($this->numrows>0){
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,752,'$this->codcam','A')");
         if(isset($GLOBALS["codcam"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,752,'".addslashes($linha->codcam)."','$this->codcam',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["nomecam"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,753,'".addslashes($linha->nomecam)."','$this->nomecam',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["conteudo"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,754,'".addslashes($linha->conteudo)."','$this->conteudo',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["descricao"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,750,'".addslashes($linha->descricao)."','$this->descricao',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["valorinicial"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,755,'".addslashes($linha->valorinicial)."','$this->valorinicial',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["rotulo"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,756,'".addslashes($linha->rotulo)."','$this->rotulo',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["tamanho"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,757,'".addslashes($linha->tamanho)."','$this->tamanho',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["nulo"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,758,'".addslashes($linha->nulo)."','$this->nulo',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["maiusculo"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,2252,'".addslashes($linha->maiusculo)."','$this->maiusculo',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["autocompl"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,2253,'".addslashes($linha->autocompl)."','$this->autocompl',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["aceitatipo"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,2256,'".addslashes($linha->aceitatipo)."','$this->aceitatipo',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["tipoobj"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,2438,'".addslashes($linha->tipoobj)."','$this->tipoobj',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["rotulorel"]))
           $resac = db_stdlib::db_query("insert into db_acount values($acount,144,4792,'".addslashes($linha->rotulorel)."','$this->rotulorel',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_stdlib::db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Campos das tabelas nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->codcam;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Campos das tabelas nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->codcam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codcam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = $result->rowCount();
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($codcam=null,$dbwhere=null) { 
     if($dbwhere==null or $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($codcam));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)or($this->numrows!=0)){
       foreach ( $resaco as $linha) {
         $resac = db_stdlib::db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = db_stdlib::lastInsertId();
         $resac = db_stdlib::db_query("insert into db_acountacesso values($acount,".db_stdlib::db_getsession("DB_acessado").")");
         $resac = db_stdlib::db_query("insert into db_acountkey values($acount,752,'$codcam','E')");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,752,'','".addslashes($linha->codcam)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,753,'','".addslashes($linha->nomecam)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,754,'','".addslashes($linha->conteudo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,750,'','".addslashes($linha->descricao)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,755,'','".addslashes($linha->valorinicial)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,756,'','".addslashes($linha->rotulo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,757,'','".addslashes($linha->tamanho)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,758,'','".addslashes($linha->nulo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,2252,'','".addslashes($linha->maiusculo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,2253,'','".addslashes($linha->autocompl)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,2256,'','".addslashes($linha->aceitatipo)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,2438,'','".addslashes($linha->tipoobj)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
         $resac = db_stdlib::db_query("insert into db_acount values($acount,144,4792,'','".addslashes($linha->rotulorel)."',".db_stdlib::db_getsession('DB_datausu').",".db_stdlib::db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from db_syscampo
                    where ";
     $sql2 = "";
     if($dbwhere==null or $dbwhere ==""){
        if($codcam != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " codcam = $codcam ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_stdlib::db_query($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Campos das tabelas nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$codcam;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if($result->rowCount()==0){
         $this->erro_banco = "";
         $this->erro_sql = "Campos das tabelas nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$codcam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$codcam;
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
        $this->erro_sql   = "Record Vazio na Tabela:db_syscampo";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   function sql_query ( $codcam=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from db_syscampo ";
     $sql2 = "";
     if($dbwhere==""){
       if($codcam!=null ){
         $sql2 .= " where db_syscampo.codcam = $codcam "; 
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
   function sql_query_file ( $codcam=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from db_syscampo ";
     $sql2 = "";
     if($dbwhere==""){
       if($codcam!=null ){
         $sql2 .= " where db_syscampo.codcam = $codcam "; 
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