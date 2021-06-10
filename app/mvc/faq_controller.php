<?php
class faq_controller extends controller_base {
	# ��������� ����� �������-������
	function showItem()
	{
        # ���������� ��������� ������
        $feedback_controller = $this->load('feedback');
        
		# 301 ���� URL ������������� �� �� "/", ������ 301 �������� �� URL �� "/"
		if ($_SERVER['REQUEST_URI'][strlen($_SERVER['REQUEST_URI']) - 1] != "/"
            && !stristr($_SERVER['REQUEST_URI'], '?')
            && !stristr($_SERVER['REQUEST_URI'], '#')) {
			header("HTTP/1.0 301 Moved Permanently");
			header("Location: http://".DOMAIN.$_SERVER['REQUEST_URI']."/");
			exit;
		}
        
		# ���������� ��������� ������
		$site_section_default_controller = $this->load('site_section_default');
		# $news_controller = $this->load('news');
		$faq_controller = $this->load('faq');
		# $tags_controller = $this->load('tags');
		# $articles_controller = $this->load('articles');
        
        # �������� ���������� �� �������
        $itemInfo = $GLOBALS['tpl_item'] = $this->model->getItemInfo($this->routeVars['itemURL']); # print_r($itemInfo);
        if (!empty($itemInfo['file_name'])) $GLOBALS['tpl_item']['text'] = getContent('/app/site_sections_faq/'.$itemInfo['file_name']);
        
		# 404
		if (empty($itemInfo['id'])) {
			header("HTTP/1.0 404 Not Found");
			header("Location: http://".DOMAIN);
			exit;
		}
        
        # �������� ���������� �� ������������ ����������
        $parentSectionInfo = $site_section_default_controller->model->getSiteSectionInfo('vopros'); # print_r($parentSectionInfo);

        # ��������� ��������
        $GLOBALS['tpl_title'] = !empty($itemInfo['title']) ? $itemInfo['title'] : $itemInfo['h1'];

        # ������ ���������
        # ������ ��������� � ������ ������
        if (!empty($itemInfo['full_navigation'])) $GLOBALS['tpl_navigation'] = $itemInfo['full_navigation'];
        # ������ ���������
        else {
            if (!empty($itemInfo['navigation'])) $navigation = $itemInfo['navigation'];
            else $navigation = $itemInfo['h1'];
            if (strlen($navigation) > 60) $navigation = cutText($navigation, 60);
            $GLOBALS['tpl_navigation'] = '
            <a href="/vopros/">'.$parentSectionInfo['navigation'].'</a> <span>&raquo;</span>
            '.$navigation;
        }
        
        # ��������� h1
        if (!empty($itemInfo['h1'])) $GLOBALS['tpl_h1'] = $itemInfo['h1'];
        
        # �������� �������-������ ��� ����� "������ ������ �� �������"
        $GLOBALS['tpl_another_faq'] = $this->model->getFaqForBlockAnotherFaq($itemInfo['id']); # print_r($GLOBALS['tpl_another_faq']);
        
		# �������
		$GLOBALS['tpl_content'] = $this->tpl->getTemplate('faq_detailed.html');
        
        # ������������ � �������
        if (!empty($itemInfo['footeranchor'])) $GLOBALS['tpl_footeranchor'] = $itemInfo['footeranchor'];

        # ��������� id ������� ��� ����� "������, �������, ������-�����" ��� /loader.php
        $GLOBALS['tpl_faq_id_selected'] = $itemInfo['id'];

        # ������� ���� "������, �������, ������-�����" � �������
        showBlockInFooter();

		# ������� ������ ��� ����������
		$this->tpl->setMainTemplate('template_for_inside_pages_v1.html');
		$this->tpl->echoMainTemplate();
	} # /��������� ����� �������-������
}