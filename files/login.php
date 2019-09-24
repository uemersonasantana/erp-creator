<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

//  A função autoload é utilizada no PHP para fazer o carregamento automático das classes.
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$db_conecta             =   new libs\db_conecta(1); 
$db_stdlib              =   new libs\db_stdlib;
$db_funcoes             =   new dbforms\db_funcoes; 
$Services_Skins             =   new libs\Services_Skins;
$Services_Funcoes       =   new libs\Services_Funcoes;
$db_valida_requisitos   =   new libs\db_valida_requisitos(DB_VALIDA_REQUISITOS);

//  BEGIN: HTML
include $Services_Skins->getPathFile('dashboard','html_start.php');
    //  BEGIN: Head
    include $Services_Skins->getPathFile('dashboard','head.php');
    //  END: Head

    //  BEGIN: Body
    include $Services_Skins->getPathFile('login','body_start.php');
        //  BEGIN: Content
        include $Services_Skins->getPathFile('login','body_content_start.php');

            //  ----Páginas que será carregada-----

            ?>
                <section class="flexbox-container">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="col-lg-4 col-md-6 col-10 box-shadow-2 p-0">
                            <div class="card border-grey border-lighten-3 px-1 py-1 m-0">
                                <div class="card-header border-0">
                                    <div class="text-center mb-1">
                                        <img src="<?php echo $Services_Skins->getSkinLink(); ?>app-assets/images/logo/logo.png" alt="branding logo">
                                    </div>

                                    
                                </div>
                                <div class="card-content">
                                    <div id="carregando" class="col-md-4 col-sm-6 col-12 fonticon-container" style="display:none;">
                                        <div class="fonticon-wrap icon-shadow icon-shadow-primary">
                                            <i class="ft-loader"></i>
                                        </div>
                                    </div>
                                    <div id="msg_erro" class="alert alert-danger mb-2" role="alert" style="cursor:pointer;display:none;" title="Clique para fechar AVISO" onclick="$(this).hide();">
                                        </div>
                                </div>
                                

                                <div class="card-content">

                                    <div class="card-body">
                                        <form class="form-horizontal" id="login" action="javascript: formularios_dinamicos('#login', '<?php echo $Services_Funcoes->url_acesso(); ?>files/rpc/login.RPC.php', '#carregando', '#msg_erro', 's', '');" novalidate>
                                            <fieldset class="form-group position-relative has-icon-left">
                                                <input type="text" class="form-control round" id="DB_login" name="DB_login" placeholder="Informe seu login" required>
                                                <div class="form-control-position">
                                                    <i class="ft-user"></i>
                                                </div>
                                            </fieldset>
                                            <fieldset class="form-group position-relative has-icon-left">
                                                <input type="password" class="form-control round" id="DB_login" name="DB_senha" placeholder="Informe sua senha" required>
                                                <div class="form-control-position">
                                                    <i class="ft-lock"></i>
                                                </div>
                                            </fieldset>
                                            <div class="form-group row">
                                                <div class="col-md-6 col-12 text-center text-sm-left">

                                                </div>
                                                <div class="col-md-6 col-12 float-sm-left text-center text-sm-right"><a href="recover-password.html" class="card-link">Esqueceu a senha?</a></div>
                                            </div>
                                            <div class="form-group text-center">
                                                <button type="submit" class="btn round btn-block btn-glow btn-bg-gradient-x-purple-blue col-12 mr-1 mb-1">Login</button>
                                            </div>

                                        </form>
                                    </div>
                                    <div class="text-center">
                                        <a href="#" class="btn btn-social-icon round mr-1 mb-1 btn-facebook"><span class="ft-facebook"></span></a>
                                        <a href="#" class="btn btn-social-icon round mr-1 mb-1 btn-twitter"><span class="ft-twitter"></span></a>
                                        <a href="#" class="btn btn-social-icon round mr-1 mb-1 btn-instagram"><span class="ft-instagram"></span></a>
                                    </div>

                                    <p class="card-subtitle text-muted text-right font-small-3 mx-2 my-1"><span>Don't have an account ? <a href="register.html" class="card-link">Sign Up</a></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
    <?php
        include $Services_Skins->getPathFile('login','body_content_end.php');

    //  END: Body
    include $Services_Skins->getPathFile('login','body_end.php'); 

//  END: HTML
include $Services_Skins->getPathFile('dashboard','html_end.php'); 