<?php
class feedback_controller extends controller_base {
	# ПОДРОБНЫЙ ВЫВОД ОТЗЫВА
	function showItem()
    {
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
        
        # получаем информацию по позиции
        $itemInfo = $GLOBALS['tpl_item'] = $this->model->getItemInfo($this->routeVars['itemURL']); # print_r($itemInfo);
        
		# 404
		if (empty($itemInfo['id'])) {
			header("HTTP/1.0 404 Not Found");
			header("Location: http://".DOMAIN);
			exit;
		}
        
        # получаем информацию по родительской директории
        $parentSectionInfo = $site_section_default_controller->model->getSiteSectionInfo('otzyvy'); # print_r($parentSectionInfo);
        
        # заголовок страницы
        $GLOBALS['tpl_title'] = $itemInfo['name'].', '.$itemInfo['date_add_day'].' '.$itemInfo['date_add_month'].' '.$itemInfo['date_add_year'];
        
        # строка навигации
        # строка навигации в ручном режиме
        if (!empty($itemInfo['full_navigation'])) {
            $GLOBALS['tpl_full_navigation'] = $itemInfo['full_navigation'];
            # избыточная переменная, нужна для постепенного вывода строки навигации на сайте,
            # а не сразу на всем сайте
            $GLOBALS['tpl_show_navigation'] = 1;
        }
        # строка навигации
        else {
            if (!empty($itemInfo['navigation'])) $navigation = $itemInfo['navigation'];
            else $navigation = $itemInfo['name'];
            if (strlen($navigation) > 60) $navigation = cutText($navigation, 60);
            $GLOBALS['tpl_navigation'] = '
            <a href="/otzyvy/">'.$parentSectionInfo['navigation'].'</a> <span>&raquo;</span>
            '.$navigation;

            # избыточная переменная, нужна для постепенного вывода строки навигации на сайте,
            # а не сразу на всем сайте
            $GLOBALS['tpl_show_navigation'] = 1;
        }

        # заголовок h1
        $GLOBALS['tpl_h1'] = $itemInfo['name'];

        # получаем список позиций для блока "Другие отзывы"
        $GLOBALS['tpl_another_feedback'] = $this->model->getFeedbackForBlockAnotherFeedback($itemInfo['id']); # echo '<pre>'.(print_r($GLOBALS['tpl_another_feedback'], true)).'</pre>'; # exit;
        foreach ($GLOBALS['tpl_another_feedback'] as &$item) {
            $item['feedback'] = cutText($item['feedback'], 233);
            # определяем последний элемент
            if(++$i == $_c) $item['is_last'] = 1;
            else unset($item['is_last']);
        } unset($item); # print_r($GLOBALS['tpl_faq']);

		# контент
		$GLOBALS['tpl_content'] = $this->tpl->getTemplate('feedback_detailed.html');
        
        # перелинковка в подвале
        if (!empty($itemInfo['footeranchor'])) $GLOBALS['tpl_footeranchor'] = $itemInfo['footeranchor'];

        # скрываем отзывы после контента в шаблоне для внутренних
        $GLOBALS['tpl_hide_feedback'] = 1;

        # скрываем спецпредложение после контента в шаблоне для внутренних
        $GLOBALS['tpl_hide_special_offer'] = 1;

        # выводим блок "Советы, новости, вопрос-ответ" в подвале
        showBlockInFooter();

        # выделяем меню вверху страницы
        $GLOBALS['tpl_top_menu_active_5'] = ' class="active"';

		# выводим шаблон для внутренних
		$this->tpl->setMainTemplate('template_for_inside_pages_v1.html');
		$this->tpl->echoMainTemplate();
	} # /# ПОДРОБНЫЙ ВЫВОД ОТЗЫВА
}