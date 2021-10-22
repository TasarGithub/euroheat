<?php

class site_section_default_controller extends controller_base

{

	function index()

	{

		# print_r($this->route); # echo '<hr />'; exit;

        

		# 301 если URL заканчивается не на "/", делаем 301 редирект на URL со "/"

		if ($_SERVER['REQUEST_URI'][strlen($_SERVER['REQUEST_URI']) - 1] != "/"

            && !stristr($_SERVER['REQUEST_URI'], '?')

            && !stristr($_SERVER['REQUEST_URI'], '#')) {

			header("HTTP/1.0 301 Moved Permanently");

			header("Location: http://".DOMAIN.$_SERVER['REQUEST_URI']."/");

			exit;

		}

		

		# подгружаем сторонние модули

		# $news_controller = $this->load('news');

		# $faq_controller = $this->load('faq');

		# $feedback_controller = $this->load('feedback');

		

		# получаем инофрмацию по разделу

		$route = $this->route; # echo 'route: '.$this->route.'<br />';



        # модифицируем URL для постраничного вывода

        if (stristr($this->route, '/page')) $this->route = substr($this->route, 0, strpos($this->route, '/page'));

        # echo 'route: '.$this->route.'<br />';

        # exit;



        # получаем информацию по разделу

		$siteSectionInfo = $this->model->getSiteSectionInfo($this->route); 
        #echo '<pre>'.(print_r($siteSectionInfo, true)).'</pre>'; #exit;



		# 404

		if (empty($siteSectionInfo['is_showable'])) {

			header("HTTP/1.0 404 Not Found");

			header('Location: http://'.$_SERVER["HTTP_HOST"]);

			exit;

		}



        # meta keywords

        if (!empty($siteSectionInfo['keywords'])) $GLOBALS['tpl_meta_keywords'] = $siteSectionInfo['keywords'];



        # meta description

        if (!empty($siteSectionInfo['description'])) $GLOBALS['tpl_meta_description'] = $siteSectionInfo['description'];



		# page title

		if (!empty($siteSectionInfo['title'])) $GLOBALS['tpl_title'] = $siteSectionInfo['title'];

		else $GLOBALS['tpl_title'] = $siteSectionInfo['name'];



		# заголовок h1

		if (!empty($siteSectionInfo['h1'])) $GLOBALS['tpl_h1'] = $siteSectionInfo['h1'];

		# else $GLOBALS['tpl_h1'] = $siteSectionInfo['name'];



        # строка навигации

        # строка навигации в ручном режиме

        if (!empty($siteSectionInfo['full_navigation'])) {

            $GLOBALS['tpl_full_navigation'] = $siteSectionInfo['full_navigation'];

            # избыточная переменная, нужна для постепенного вывода строки навигации на сайте,

            # а не сразу на всем сайте

            $GLOBALS['tpl_show_navigation'] = 1;

        }

        # строка навигации

        else {

            if (!empty($siteSectionInfo['navigation'])) {

                $GLOBALS['tpl_navigation'] = $this->buildNavigtaion($route, '');



                # избыточная переменная, нужна для постепенного вывода строки навигации на сайте,

                # а не сразу на всем сайте

                $GLOBALS['tpl_show_navigation'] = 1;

            }

        }



        # echo $this->route;



        ### НЕСТАНДАРТНЫЙ ФУНКЦОИНАЛ



        if ($this->route == 'catalog'

            || $this->route == 'teploobmenniki'

            || $this->route == 'kalorifery'

            || $this->route == 'vodyanye-vozduhoohladiteli'

            || $this->route == 'freonovye-ispariteli'

            || $this->route == 'teploobmenniki/parovye'

            || $this->route == 'teploobmenniki/nerzhaveyuschie'

            || $this->route == 'teploobmenniki/vts_clima'

            || $this->route == 'teploobmenniki/kalorifery-cva'

            || $this->route == 'teploobmenniki/vts-nvs'

            || $this->route == 'teploobmenniki/kalorifery-kan'

            || $this->route == 'teploobmenniki/kanalnye-nagrevateli-i-ohladiteli'

            || $this->route == 'teploobmenniki/dlya-pritochnyh-ustanovok'

            || $this->route == 'filtry'

            || $this->route == 'filtry/panelnye-dlya-ventilyacii'

            || $this->route == 'filtry/kassetnye-dlya-ventilyacii'

            || $this->route == 'filtry/karmannye-dlya-ventilyacii'

            || $this->route == 'filtry/vts'

            || $this->route == 'smesitelnye-uzly'

            || $this->route == 'smesitelnye-uzly/obvyazka-kalorifera'

            || $this->route == 'smesitelnye-uzly/obvyazka-ohladitelya'

            || $this->route == 'smesitelnye-uzly/dlya-teplovoi-zavesy'

            || $this->route == 'smesitelnye-uzly/vts'

            ) {

            $GLOBALS['menu_1'] = ' class="active"';

        }



        elseif ($this->route == 'price') { # http://euroheater.ru/price/

            $GLOBALS['menu_2'] = ' class="active"';

        }



        elseif ($this->route == 'o-nas'

                || $this->route =="o-nas/klienty"

                ) { # http://euroheater.ru/80-kvt/

            $GLOBALS['menu_3'] = ' class="active"';

        }



        elseif ($this->route == 'online'

                || $this->route == 'calculator'

                || $this->route == 'raschet-nagrevatelya'

                || $this->route == 'raschet-ohladitelya'

                || $this->route == 'raschet-condensatora'

                || $this->route == 'raschet-parovogo-teploobmennika'

                || $this->route == 'raschet-isparitelya'

                ) {

            $GLOBALS['menu_4'] = ' class="active"';

        }



        elseif ($this->route == 'proekty') { # http://euroheater.ru/200-kvt/

            $GLOBALS['menu_5'] = ' class="active"';

        }



        elseif ($this->route == 'kontakty') { # http://euroheater.ru/320-kvt/

            $GLOBALS['menu_6'] = ' class="active"';

        }



        elseif ($this->route == 'novosti') { # http://euroheater.ru/novosti/

            $news_controller = $this->load('news');



            # получаем список новостей

            $newsResult = $news_controller->model->getItemsForIndex();



            if (!empty($newsResult['resultSet'])) $GLOBALS['tpl_news'] = $newsResult['resultSet'];

            if (!empty($newsResult['pagesSet'])) $GLOBALS['tpl_news_pages'] = $newsResult['pagesSet'];

            if (!empty($newsResult['allRowsCount'])) $GLOBALS['tpl_news_count'] = $newsResult['allRowsCount'];

        }



        elseif ($this->route == 'vopros') { # http://euroheater.ru/vopros/

            $faq_controller = $this->load('faq');



            # получаем список новостей

            $faqResult = $faq_controller->model->getItemsForIndex();



            if (!empty($faqResult['resultSet'])) $GLOBALS['tpl_faq'] = $faqResult['resultSet'];

            if (!empty($faqResult['pagesSet'])) $GLOBALS['tpl_faq_pages'] = $faqResult['pagesSet'];

            if (!empty($faqResult['allRowsCount'])) $GLOBALS['tpl_faq_count'] = $faqResult['allRowsCount'];

        }



        elseif ($this->route == 'otzyvy') { # http://euroheater.ru/otzyvy/

            $feedback_controller = $this->load('feedback');



            # получаем список отзывов

            $feedbackResult = $feedback_controller->model->getItemsForIndex();



            if (!empty($feedbackResult['resultSet'])) {

                foreach ($feedbackResult['resultSet'] as &$item) {

                    # echo '<pre>'.(print_r($item, true)).'</pre>'; # exit;

                    $item['feedback'] = cutText($item['feedback'], 200);

                } unset($item);

                $GLOBALS['tpl_feedback'] = $feedbackResult['resultSet'];

            }

            if (!empty($feedbackResult['pagesSet'])) $GLOBALS['tpl_feedback_pages'] = $feedbackResult['pagesSet'];

            if (!empty($feedbackResult['allRowsCount'])) $GLOBALS['tpl_feedback_count'] = $feedbackResult['allRowsCount'];



            # скрываем отзывы после контента в шаблоне для внутренних

            $GLOBALS['tpl_hide_feedback'] = 1;



            # скрываем спецпредложение после контента в шаблоне для внутренних

            $GLOBALS['tpl_hide_special_offer'] = 1;



            # выделяем меню вверху страницы

            $GLOBALS['tpl_top_menu_active_5'] = ' class="active"';

        }



        elseif ($this->route == 'sovet') { # http://euroheater.ru/sovet/

            $articles_controller = $this->load('articles');



            # получаем список статей

            $articlesResult = $articles_controller->model->getItemsForIndex();



            if (!empty($articlesResult['resultSet'])) $GLOBALS['tpl_articles'] = $articlesResult['resultSet'];

            if (!empty($articlesResult['pagesSet'])) $GLOBALS['tpl_articles_pages'] = $articlesResult['pagesSet'];

            if (!empty($articlesResult['allRowsCount'])) $GLOBALS['tpl_articles_count'] = $articlesResult['allRowsCount'];

        }



        elseif ($this->route == 'uslugi') { # http://euroheater.ru/uslugi/

            # выделяем меню вверху страницы

            $GLOBALS['tpl_top_menu_active_1'] = ' class="active"';

        }



        elseif ($this->route == 'oborudovanie') { # http://euroheater.ru/oborudovanie/

            # выделяем меню вверху страницы

            $GLOBALS['tpl_top_menu_active_2'] = ' class="active"';

        }



        elseif ($this->route == 'o-nas') { # http://euroheater.ru/o-nas/

            # выделяем меню вверху страницы

            $GLOBALS['tpl_top_menu_active_3'] = ' class="active"';

        }

        elseif ($this->route == 'o-nas/vakansii') { # http://euroheater.ru/o-nas/vakansii/

            # выделяем меню вверху страницы

            $GLOBALS['tpl_top_menu_active_3'] = ' class="active"';

        }

        elseif ($this->route == 'o-nas/missiya-i-cennosti') { # http://euroheater.ru/o-nas/missiya-i-cennosti/

            # выделяем меню вверху страницы

            $GLOBALS['tpl_top_menu_active_3'] = ' class="active"';

        }

        elseif ($this->route == 'o-nas/dokumenty-dogovora') { # http://euroheater.ru/o-nas/dokumenty-dogovora/

            # выделяем меню вверху страницы

            $GLOBALS['tpl_top_menu_active_3'] = ' class="active"';

        }

        elseif ($this->route == 'o-nas/nashi-klienty') { # http://euroheater.ru/o-nas/nashi-klienty/

            # выделяем меню вверху страницы

            $GLOBALS['tpl_top_menu_active_3'] = ' class="active"';

        }



        elseif ($this->route == 'tarify') { # http://euroheater.ru/tarify/

            # выделяем меню вверху страницы

            $GLOBALS['tpl_top_menu_active_4'] = ' class="active"';

        }



        elseif ($this->route == 'proekty') { # http://euroheater.ru/proekty/

            # выделяем меню вверху страницы

            $GLOBALS['tpl_top_menu_active_6'] = ' class="active"';

        }



        ### /НЕСТАНДАРТНЫЙ ФУНКЦОИНАЛ

		

        # текст раздела

        if (!empty($siteSectionInfo['file_name_1'])) {

            $fullPathToFileName1 = PATH_TO_SITE_SECTIONS.$siteSectionInfo['file_name_1']; # echo 'full path to file name 1: '.$fullPathToFileName1;

            # echo file_exists($fullPathToFileName1);

            if (file_exists($fullPathToFileName1)) {

                ob_start(); // start capturing output

                include($fullPathToFileName1); // execute the file

                $GLOBALS['tpl_content'] = ob_get_contents(); // get the contents from the buffer

                ob_end_clean(); // stop buffering and discard contents

            }

            # else echo 'file_name_1 is not exists: '.$fullPathToFileName1;

        }

        # else echo 'file_name_1 is empty.';



        # выводим блок "Советы, новости, вопрос-ответ" в подвале

        showBlockInFooter();



        # перелинковка в подвале

        if (!empty($siteSectionInfo['footeranchor'])) $GLOBALS['tpl_footeranchor'] = $siteSectionInfo['footeranchor'];

		# выводим шаблон для внутренних

		$this->tpl->setMainTemplate('template_for_inside_pages_v1.html');

		$this->tpl->echoMainTemplate();

	}

	

	# формируем строку навигации

    # входные переменные:

    # $itemFullURL: полный URL к разделу (поле "full_url" в таблице "site_sections"), пример: "uslugi/mebel"

    # $isLastLink: 1 - последний раздел идет как ссылка, NULL - последний раздел идет без ссылки

    function buildNavigtaion($itemFullURL, $isLastLink = NULL)

	{

		# проверка переменных

		if (empty($itemFullURL)) return;

		

		$urls = explode('/', $itemFullURL); # echo '<pre>'.(print_r($urls, true)).'</pre>';



        # выводим строку навигации в разделах, начиная с 3 уровня

        # пример: http://www.zapashnyh.ru/biografiya/edgard/

        # для разделов 2 уровня строку навигации не выводим

        if (count($urls) == 1) return '';



		if (is_array($urls)) {

			# $result[] = '<a href="/">Главная</a>';

			unset($fullIterationUrl);

            $last_key = end(array_keys($urls));

			foreach ($urls as $k => $v) { # echo 'k: '.$k.', v: '.$v.'<br />';

                

                # пропускаем постраничный вывод

                if (stristr($v, 'page')) continue;



                # fullIterationUrl

				if (empty($fullIterationUrl)) $fullIterationUrl = $v;

				else $fullIterationUrl .= '/'.$v;

				# echo 'fullIterationUrl: '.$fullIterationUrl.'<hr />';

				

				$name = $this->model->getNavigationByFullURL($fullIterationUrl);

				

				# echo $v.'<br />';

				$url = substr($itemFullURL, 0, strpos($itemFullURL, $v));

				# последний раздел

                if ($k == $last_key) {

                    if (!empty($isLastLink)) $result[] = '<a href="/'.$fullIterationUrl.'/">'.$name.'</a>';

                    else $result[] = $name;

                }

                # не последний раздел

                else $result[] = '<a href="/'.$fullIterationUrl.'/">'.$name.'</a>';

			}

			$result = implode(' <span>&raquo;</span> ', $result);

			return $result;

		}

	} # /формируем строку навигации



    # URL SELECTOR: ЛИБО ЭТО СТАТИЧНЫЙ РАЗДЕЛ, ЛИБО ЭТО ЭЛЕКТРОСТАНЦИЯ ПОДРОБНО

    function urlSelectorGeneratora()

    {

        $params = array('url' => $this->routeVars['itemURL']);

        $urlSelector = $this->model->urlSelectorGeneratora($params);

        # echo '<pre>'.(print_r($urlSelector, true)).'</pre>';



        if (!empty($urlSelector['is_site_section'])) {

            $this->index();

        }

        elseif (!empty($urlSelector['catalog_detailed'])) {

            $catalog_controller = $this->load('catalog');

            $catalog_controller->showItem();

        }



        else {

            header("HTTP/1.0 404 Not Found");

            header("Location: http://".DOMAIN.'/generatora/');

            exit;

        }

    }

    # /URL SELECTOR: ЛИБО ЭТО СТАТИЧНЫЙ РАЗДЕЛ, ЛИБО ЭТО ЭЛЕКТРОСТАНЦИЯ ПОДРОБНО

}



# ФУНКЦИИ







# /ФУНКЦИИ