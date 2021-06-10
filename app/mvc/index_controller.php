<?php
class index_controller extends controller_base
{
	# ������� ������� ��������
	function index()
	{
        # 404
        if ($this->route == 'index') {
            header("HTTP/1.0 404 Not Found");
            header("Location: http://".$_SERVER['SERVER_NAME']);
            exit;
        }

		# ���������� ��������� �����������
        $feedback_controller = $this->load('feedback');
        $articles_controller = $this->load('articles');
        $faq_controller = $this->load('faq');
        $news_controller = $this->load('news');

        # �������� ������ �������
        $GLOBALS['tpl_feedback'] = $feedback_controller->model->getItemsForMainPage(); # echo '<pre>'.(print_r($GLOBALS['tpl_feedback'], true)).'</pre>'; # exit;
        foreach ($GLOBALS['tpl_feedback'] as &$item) {
            $item['feedback'] = cutText($item['feedback'], 190);
            # $item['name'] = cutText($item['name'], 13);
        } unset($item);

        # �������� ���������� ������
        $GLOBALS['tpl_articles_count'] = $articles_controller->model->getItemsCount();

        # �������� ������ �� ������
        $GLOBALS['tpl_articles'] = $articles_controller->model->getRandomItems(5);

        # �������� ���������� ��������-�������
        $GLOBALS['tpl_faq_count'] = $faq_controller->model->getItemsCount();

        # �������� �������-������ �� ������
        $GLOBALS['tpl_faq'] = $faq_controller->model->getRandomItems(5);
        foreach ($GLOBALS['tpl_faq'] as &$item) {
            $item['h1'] = cutText($item['h1'], 100);
        } unset($item);

        # �������� ������ �� ������
        $GLOBALS['tpl_news'] = $news_controller->model->getRandomItems(5);

		# ������� ������� ������
		$this->tpl->setMainTemplate('template_for_main_page_v1.html');
		# $this->tpl->setMainTemplate('template_for_main_page.min.html');
		$this->tpl->echoMainTemplate();
	}
	# /������� ������� ��������
	
	# �������� ��������� ������ �� �������� ��������
	function removeLastChar($string)
	{
		# �������� ����������
		if (empty($string)) return;
		
		$lastChar = substr($string, -1); # echo 'last char: '.$lastChar.'<br />';
		
		if (strpbrk($lastChar, '.,;\'"0123456789')) {
			# echo $string.'<br />';
			$string = substr($string, 0, strlen($string) - 1);
			# echo $string;
		}
		
		return trim($string);
	}
	# /�������� ��������� ������ �� �������� ��������
}