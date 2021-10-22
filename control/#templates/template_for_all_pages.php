<!DOCTYPE html>
<html lang="ru">

<head>

    <meta charset="utf-8">
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo (!empty($GLOBALS['tpl_title']) ? $GLOBALS['tpl_title'] : 'админка euroheater.ru'); ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="/control/public/sb-admin-2-theme/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- MetisMenu CSS -->
    <link href="/control/public/sb-admin-2-theme/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet" />

    <!-- Timeline CSS -->
    <link href="/control/public/sb-admin-2-theme/dist/css/timeline.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="/control/public/sb-admin-2-theme/dist/css/sb-admin-2.css" rel="stylesheet" />

    <!-- Morris Charts CSS -->
    <link href="/control/public/sb-admin-2-theme/bower_components/morrisjs/morris.css" rel="stylesheet" />

    <!-- Custom Fonts -->
    <link href="/control/public/sb-admin-2-theme/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    
    <!-- jquery user interface -->
    <link href="/control/public/js/jquery-ui-1.10.3/css/start/jquery-ui-1.10.3.custom.css" rel="stylesheet" />
    
    <!-- Модификация стилей изначальной верстки -->
    <link href="/control/public/css/sb-admin-2-theme-modified.css?v=1.0" rel="stylesheet" type="text/css" />
    
    <!-- переопределенные стили для jquery UI -->
    <link href="/control/public/js/jquery-ui-1.10.3/styles.css?v=2.0" rel="stylesheet" />
    
    <!-- jQuery -->
    <script src="/control/public/sb-admin-2-theme/bower_components/jquery/dist/jquery.min.js"></script>

    <!-- jplayer 2.9.2 -->
    <script src="/control/public/js/jplayer/jquery.jplayer.min.js" type="text/javascript"></script>
    <!-- <link href="/control/public/js/jplayer/skin/pink.flag/css/jplayer.pink.flag.css" rel="stylesheet" type="text/css" /> -->
    <link href="/control/public/js/jplayer/skin/blue.monday/css/jplayer.blue.monday.min.css" rel="stylesheet" type="text/css" />

    <!-- mini skin for jplayer -->
    <script src="/control/public/js/jplayer-mini/jQuery.mb.miniAudioPlayer.js" type="text/javascript"></script>
    <script src="/control/public/js/jplayer-mini/jquery.metadata.js" type="text/javascript"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav id="navbar" class="navbar navbar-default navbar-static-top position_relative fixed" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-brand" href="/control/">админка euroheater.ru</a>
            </div>
            <span id="main_ajax_preloader"></span>
            <!-- /.navbar-header -->
            
            <ul id="top_menu_nav" class="nav navbar-top-links navbar-right">
                <!--<li><a id="top_menu_site_sections" href="/control/site_sections/"><i class="fa fa-sitemap fa-fw"></i>&nbsp; Разделы сайта</a></li>-->
                <li>
                    <a id="top_menu_templates" href="#" style="padding-top:12px" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-edit fa-fw"></i> html-шаблоны <i class="fa fa-caret-down" style="position:relative;left:3px;top:1px"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="/control/templates/?action=editItem&itemID=1" style="padding-top:14px"><i class="fa fa-edit fa-fw"></i> Шаблон для главной</a></li>
                        <li><a href="/control/templates/?action=editItem&itemID=2" style="padding-top:14px"><i class="fa fa-edit fa-fw"></i> Шаблон для внутренних</a></li>
                        <li><a href="/control/templates/?action=editItem&itemID=3" style="padding-top:14px"><i class="fa fa-edit fa-fw"></i> E-mail для уведомлений</a></li>
                        <li><a href="/control/templates/?action=editItem&itemID=4" style="padding-top:14px"><i class="fa fa-edit fa-fw"></i> Телефон</a></li>
                        <li><a href="/control/templates/" style="padding-top:14px"><i class="fa fa-edit fa-fw"></i> Все шаблоны</a></li>

                    </ul>
                </li>
                <li><a id="top_menu_css" href="/control/edit_css/" style="padding-top:12px"><i class="fa fa-edit fa-fw"></i> css-стили</a></li>
                <li><a id="top_menu_js" href="/control/edit_js/" style="padding-top:12px"><i class="fa fa-edit fa-fw"></i> js-скрипты</a></li>
                <li><a id="top_menu_js" href="/control/filegator/" target="_blank" style="padding-top:12px"><i class="fa fa-file-image-o fa-fw"></i> редактор файлов</a></li>
                <!--
                <li>
                    <a href="#" style="padding-top:12px" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-file-image-o fa-fw"></i> Редактор файлов <i class="fa fa-caret-down" style="position:relative;left:3px;top:1px"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="/control/public/files_editor_docs/image.php" style="padding-top:14px" target="_blank"><i class="fa fa-edit fa-fw"></i> /public/docs/</a></li>
                        <li><a href="/control/public/files_editor_images/image.php" style="padding-top:14px" target="_blank"><i class="fa fa-edit fa-fw"></i> /public/images/</a></li>
                    </ul>
                </li>
                -->
            </ul>
            
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu" style="margin-top:-5px">
                        <li>
                            <a id="left_menu_cars" href="/control/site_sections/"><i class="fa fa-sitemap fa-fw"></i>&nbsp; Разделы сайта</a>
                        </li>
                        <li>
                            <a id="left_menu_articles" href="/control/online_requests/"><i class="fa fa-list-alt fa-fw"></i>&nbsp; Онлайн-заявки</a>
                        </li>
                        <!--
                        <li>
                            <a id="left_menu_articles" href="/control/phone_calls/"><i class="fa fa-phone fa-fw"></i>&nbsp; Телефонные звонки</a>
                        </li>
                        -->
                        <li>
                            <a id="left_menu_news" href="/control/news/"><i class="fa fa-th-list fa-fw"></i>&nbsp; Новости</a>
                        </li>
                        <li>
                            <a id="left_menu_faq" href="/control/faq/"><i class="fa fa-question-circle fa-fw"></i>&nbsp; Вопросы-ответы</a>
                        </li>
                        <li>
                            <a id="left_menu_articles" href="/control/articles/"><i class="fa fa-book fa-fw"></i>&nbsp; Статьи</a>
                        </li>
                        <li>
                            <a id="left_menu_feedbacks" href="/control/feedbacks/"><i class="fa fa-comment fa-fw"></i>&nbsp; Отзывы</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row relative">
                <!-- сообщение об результате ajax-операции -->
                <div class="ajax_result top"></div>
                
                <div class="col-lg-12">
                    <h1 id="h1" class="page-header"><?php echo $GLOBALS['tpl_h1']; ?></h1>
                    <!-- сообщение об успешном действии -->
                    <?php if (!empty($GLOBALS['tpl_success'])): ?>
                    <div class="alert alert-success"><?php echo $GLOBALS['tpl_success']; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($GLOBALS['tpl_failure'])): ?>
                    <!-- сообщение о неудачном действии -->
                    <div class="alert alert-danger"><?php echo $GLOBALS['tpl_failure']; ?></div>
                    <?php endif; ?>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div id="main_content" class="row">
                <?php echo $GLOBALS['tpl_content']; ?>
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Bootstrap Core JavaScript -->
    <script src="/control/public/sb-admin-2-theme/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="/control/public/sb-admin-2-theme/bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="/control/public/sb-admin-2-theme/bower_components/raphael/raphael-min.js"></script>
    <!-- <script src="/control/public/sb-admin-2-theme/bower_components/morrisjs/morris.min.js"></script> -->
    <!-- <script src="/control/public/sb-admin-2-theme/js/morris-data.js"></script> -->

    <!-- Custom Theme JavaScript -->
    <script src="/control/public/sb-admin-2-theme/dist/js/sb-admin-2.js"></script>
    
    <!-- jquery user interface -->
    <script src="/control/public/js/jquery-ui-1.10.3/js/jquery-ui-1.10.3.custom.min.js"></script>
    
    <!-- настройки календаря (jquery UI datepicker) -->
    <script src="/control/public/js/jquery.datepicker.ru.js"></script>
    
    <!-- jquery plugin: https://github.com/jmosbech/StickyTableHeaders -->
    <script src="/control/public/js/jquery.stickytableheaders.min.js.txt"></script>
    
    <!-- jquery UI tabs -->
    <script src='/control/public/js/jquery.tabs.js' type='text/javascript'></script>
    
	<!-- jquery plugin: http://alan.blog-city.com/jquerylinedtextarea.htm -->
    <script src="/control/public/js/jquery-linedtextarea/jquery-linedtextarea.js"></script>
	<link href="/control/public/js/jquery-linedtextarea/jquery-linedtextarea.css" type="text/css" rel="stylesheet" />
    
    <!-- подключаем модуль для backup'а всех полей -->
    <script src='/control/public/js/jquery.backup/jquery.backup.js'></script>
    <link href='/control/public/js/jquery.backup/jquery.backup.css' rel='stylesheet' type='text/css' />

    <!-- mini skin for jplayer -->
    <link href="/control/public/js/jplayer-mini/css/jQuery.mb.miniAudioPlayer.min.css" rel='stylesheet' type='text/css' />
    
    <!-- подключаем скрипты jquery общего назначения -->
    <script src="/control/public/js/jquery.common.js?v=3.0"></script>

</body>

</html>