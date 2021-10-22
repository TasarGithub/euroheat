<?php

class faq_controller extends controller_base {

	# ПОДРОБНЫЙ ВЫВОД ВОПРОСА-ОТВЕТА

	function showItem()

	{

        # подгружаем сторонние модули

        $feedback_controller = $this->load('feedback');

        

		# 301 если URL заканчивается не на "/", делаем 301 редирект на URL со "/"

		if ($_SERVER['REQUEST_URI'][strlen($_SERVER['REQUEST_URI']) - 1] != "/"

            && !stristr($_SERVER['REQUEST_URI'], '?')

            && !stristr($_SERVER['REQUEST_URI'], '#')) {

			header("HTTP/1.0 301 Moved Permanently");

			header("Location: http://".DOMAIN.$_SERVER['REQUEST_URI']."/");

			exit;

		}

        

		# подгружаем сторонние модули

		$site_section_default_controller = $this->load('site_section_default');

		# $news_controller = $this->load('news');

		$faq_controller = $this->load('faq');

		# $tags_controller = $this->load('tags');

		# $articles_controller = $this->load('articles');

        

        # получаем информацию по позиции

        $itemInfo = $GLOBALS['tpl_item'] = $this->model->getItemInfo($this->routeVars['itemURL']); # print_r($itemInfo);

        if (!empty($itemInfo['file_name'])) $GLOBALS['tpl_item']['text'] = getContent('/app/site_sections_faq/'.$itemInfo['file_name']);

        #echo "getContent('/app/site_sections_faq/'.itemInfo[file_name]);";
        #echo getContent('/app/site_sections_faq/'.$itemInfo['file_name']);
 
		# 404

		if (empty($itemInfo['id'])) {

			header("HTTP/1.0 404 Not Found");

			header("Location: http://".DOMAIN);

			exit;

		}

        

        # получаем информацию по родительской директории

        $parentSectionInfo = $site_section_default_controller->model->getSiteSectionInfo('vopros'); # print_r($parentSectionInfo);



        # заголовок страницы

        $GLOBALS['tpl_title'] = !empty($itemInfo['title']) ? $itemInfo['title'] : $itemInfo['h1'];



        # строка навигации

        # строка навигации в ручном режиме

        if (!empty($itemInfo['full_navigation'])) {
            $GLOBALS['tpl_navigation'] = $itemInfo['full_navigation'];

        # строка навигации
      #echo '<pre>'.(print_r( $itemInfo['full_navigation'], true)).'</pre>'; #exit;
        }
        else {

            if (!empty($itemInfo['navigation']))
            {
             $navigation = $itemInfo['navigation'];
              #  echo '<pre>'.(print_r( $itemInfo['navigation'], true)).'</pre>'; #exit;

            }
            else { $navigation = $itemInfo['h1'];
               # echo '<pre>'.(print_r( $navigation, true)).'</pre>'; #exit;
            }

            if (strlen($navigation) > 60) $navigation = cutText($navigation, 60);

            #echo '<pre>'.(print_r( $navigation, true)).'</pre>'; #exit;
            $GLOBALS['tpl_navigation'] = '<a href="/vopros/">'.$parentSectionInfo['navigation'].'</a> <span>&raquo;</span>'.$navigation;
            #echo '<pre>'.(print_r( $GLOBALS['tpl_navigation'], true)).'</pre>'; #exit;
        }

        

        # заголовок h1

        if (!empty($itemInfo['h1'])) $GLOBALS['tpl_h1'] = $itemInfo['h1'];

        

        # получаем вопросы-ответы для блока "Другие ответы на вопросы"

        $GLOBALS['tpl_another_faq'] = $this->model->getFaqForBlockAnotherFaq($itemInfo['id']); # print_r($GLOBALS['tpl_another_faq']);

        

		# контент

		$GLOBALS['tpl_content'] = $this->tpl->getTemplate('faq_detailed.html');

        

        # перелинковка в подвале

        if (!empty($itemInfo['footeranchor'])) $GLOBALS['tpl_footeranchor'] = $itemInfo['footeranchor'];



        # фиксируем id новости для блока "Советы, Новости, Вопрос-ответ" для /loader.php

        $GLOBALS['tpl_faq_id_selected'] = $itemInfo['id'];



        # выводим блок "Советы, новости, вопрос-ответ" в подвале

        showBlockInFooter();



		# выводим шаблон для внутренних

		$this->tpl->setMainTemplate('template_for_inside_pages_v1.html');

		$this->tpl->echoMainTemplate();

	} # /ПОДРОБНЫЙ ВЫВОД ВОПРОСА-ОТВЕТА

}