<?php
class news_controller extends controller_base
{
	# ��������� ����� �������
	function showItem()
	{
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

        # print_r($this->routeVars);

        # �������� ���������� �� �������
        $itemInfo = $GLOBALS['tpl_item'] = $this->model->getItemInfo($this->routeVars['itemURL']); # print_r($itemInfo);

		# 404
		if (empty($itemInfo['id'])) {
			header("HTTP/1.0 404 Not Found");
			header("Location: http://".DOMAIN);
			exit;
		}
        
        # �������� ���������� �� ������������ ����������
        $parentSectionInfo = $site_section_default_controller->model->getSiteSectionInfo('novosti'); # print_r($parentSectionInfo);
        
        # ��������� ��������
        if (!empty($itemInfo['page_title'])) $GLOBALS['tpl_title'] = $itemInfo['page_title'];
        else $GLOBALS['tpl_title'] = $itemInfo['h1'];

        # ������ ��������� � ������ ������
        if (!empty($itemInfo['full_navigation'])) {
            $GLOBALS['tpl_full_navigation'] = $itemInfo['full_navigation'];
            # ���������� ����������, ����� ��� ������������ ������ ������ ��������� �� �����,
            # � �� ����� �� ���� �����
            $GLOBALS['tpl_show_navigation'] = 1;
        }
        # ������ ���������
        else {
            # ������ ���������
            if (!empty($itemInfo['navigation'])) $navigation = $itemInfo['navigation'];
            else $navigation = $itemInfo['h1'];
            $GLOBALS['tpl_navigation'] = '
            <a href="/novosti/">'.$parentSectionInfo['navigation'].'</a> <span>&raquo;</span>
            '.$navigation;

            # ���������� ����������, ����� ��� ������������ ������ ������ ��������� �� �����,
            # � �� ����� �� ���� �����
            $GLOBALS['tpl_show_navigation'] = 1;
        }
        
        # ��������� h1
        if (!empty($itemInfo['h1'])) $GLOBALS['tpl_h1'] = $itemInfo['h1'];
        
        # �������� ������ ������� ��� ����� "������ �������"
        $GLOBALS['tpl_another_news'] = $this->model->getNewsForBlockAnotherNews($itemInfo['id']); # print_r($GLOBALS['tpl_another_news']);
        foreach ($GLOBALS['tpl_another_news'] as &$item) {
            $item['text'] = cutText($item['text'], 213);
        } unset($item);

		# �������
		$GLOBALS['tpl_content'] = $this->tpl->getTemplate('news_detailed.html');

        # ������������ � �������
        if (!empty($itemInfo['footeranchor'])) $GLOBALS['tpl_footeranchor'] = $itemInfo['footeranchor'];

        # ��������� id ������� ��� ����� "������, �������, ������-�����" ��� /loader.php
        $GLOBALS['tpl_news_id_selected'] = $itemInfo['id'];

        # ������� ���� "������, �������, ������-�����" � �������
        showBlockInFooter();

		# ������� ������ ��� ����������
		$this->tpl->setMainTemplate('template_for_inside_pages_v1.html');
		$this->tpl->echoMainTemplate();
	} # /��������� ����� �������
}