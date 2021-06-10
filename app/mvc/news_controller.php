<?php
class news_controller extends controller_base
{
	# ПОДРОБНЫЙ ВЫВОД НОВОСТИ
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

        # print_r($this->routeVars);

        # получаем информацию по позиции
        $itemInfo = $GLOBALS['tpl_item'] = $this->model->getItemInfo($this->routeVars['itemURL']); # print_r($itemInfo);

		# 404
		if (empty($itemInfo['id'])) {
			header("HTTP/1.0 404 Not Found");
			header("Location: http://".DOMAIN);
			exit;
		}
        
        # получаем информацию по родительской директории
        $parentSectionInfo = $site_section_default_controller->model->getSiteSectionInfo('novosti'); # print_r($parentSectionInfo);
        
        # заголовок страницы
        if (!empty($itemInfo['page_title'])) $GLOBALS['tpl_title'] = $itemInfo['page_title'];
        else $GLOBALS['tpl_title'] = $itemInfo['h1'];

        # строка навигации в ручном режиме
        if (!empty($itemInfo['full_navigation'])) {
            $GLOBALS['tpl_full_navigation'] = $itemInfo['full_navigation'];
            # избыточная переменная, нужна для постепенного вывода строки навигации на сайте,
            # а не сразу на всем сайте
            $GLOBALS['tpl_show_navigation'] = 1;
        }
        # строка навигации
        else {
            # строка навигации
            if (!empty($itemInfo['navigation'])) $navigation = $itemInfo['navigation'];
            else $navigation = $itemInfo['h1'];
            $GLOBALS['tpl_navigation'] = '
            <a href="/novosti/">'.$parentSectionInfo['navigation'].'</a> <span>&raquo;</span>
            '.$navigation;

            # избыточная переменная, нужна для постепенного вывода строки навигации на сайте,
            # а не сразу на всем сайте
            $GLOBALS['tpl_show_navigation'] = 1;
        }
        
        # заголовок h1
        if (!empty($itemInfo['h1'])) $GLOBALS['tpl_h1'] = $itemInfo['h1'];
        
        # получаем список позиций для блока "другие новости"
        $GLOBALS['tpl_another_news'] = $this->model->getNewsForBlockAnotherNews($itemInfo['id']); # print_r($GLOBALS['tpl_another_news']);
        foreach ($GLOBALS['tpl_another_news'] as &$item) {
            $item['text'] = cutText($item['text'], 213);
        } unset($item);

		# контент
		$GLOBALS['tpl_content'] = $this->tpl->getTemplate('news_detailed.html');

        # перелинковка в подвале
        if (!empty($itemInfo['footeranchor'])) $GLOBALS['tpl_footeranchor'] = $itemInfo['footeranchor'];

        # фиксируем id новости для блока "Советы, Новости, Вопрос-ответ" для /loader.php
        $GLOBALS['tpl_news_id_selected'] = $itemInfo['id'];

        # выводим блок "Советы, новости, вопрос-ответ" в подвале
        showBlockInFooter();

		# выводим шаблон для внутренних
		$this->tpl->setMainTemplate('template_for_inside_pages_v1.html');
		$this->tpl->echoMainTemplate();
	} # /ПОДРОБНЫЙ ВЫВОД НОВОСТИ
}