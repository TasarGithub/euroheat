<?php
class index_controller extends controller_base
{
	# выводим главную страницу
	function index()
	{
        # 404
        if ($this->route == 'index') {
            header("HTTP/1.0 404 Not Found");
            header("Location: http://".$_SERVER['SERVER_NAME']);
            exit;
        }

		# подгружаем сторонние контроллеры
        $feedback_controller = $this->load('feedback');
        $articles_controller = $this->load('articles');
        $faq_controller = $this->load('faq');
        $news_controller = $this->load('news');

        # получаем список отзывов
        $GLOBALS['tpl_feedback'] = $feedback_controller->model->getItemsForMainPage(); # echo '<pre>'.(print_r($GLOBALS['tpl_feedback'], true)).'</pre>'; # exit;
        foreach ($GLOBALS['tpl_feedback'] as &$item) {
            $item['feedback'] = cutText($item['feedback'], 190);
            # $item['name'] = cutText($item['name'], 13);
        } unset($item);

        # получаем количество статей
        $GLOBALS['tpl_articles_count'] = $articles_controller->model->getItemsCount();

        # получаем статьи на рандом
        $GLOBALS['tpl_articles'] = $articles_controller->model->getRandomItems(5);

        # получаем количество вопросов-ответов
        $GLOBALS['tpl_faq_count'] = $faq_controller->model->getItemsCount();

        # получаем вопросы-ответы на рандом
        $GLOBALS['tpl_faq'] = $faq_controller->model->getRandomItems(5);
        foreach ($GLOBALS['tpl_faq'] as &$item) {
            $item['h1'] = cutText($item['h1'], 100);
        } unset($item);

        # получаем статьи на рандом
        $GLOBALS['tpl_news'] = $news_controller->model->getRandomItems(5);

		# выводим главный шаблон
		$this->tpl->setMainTemplate('template_for_main_page_v1.html');
		# $this->tpl->setMainTemplate('template_for_main_page.min.html');
		$this->tpl->echoMainTemplate();
	}
	# /выводим главную страницу
	
	# очистить окончание строки от ненужных символов
	function removeLastChar($string)
	{
		# проверка переменных
		if (empty($string)) return;
		
		$lastChar = substr($string, -1); # echo 'last char: '.$lastChar.'<br />';
		
		if (strpbrk($lastChar, '.,;\'"0123456789')) {
			# echo $string.'<br />';
			$string = substr($string, 0, strlen($string) - 1);
			# echo $string;
		}
		
		return trim($string);
	}
	# /очистить окончание строки от ненужных символов
}