<?php
class feedback_controller extends controller_base {
	# ��������� ����� ������
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
        
        # �������� ���������� �� �������
        $itemInfo = $GLOBALS['tpl_item'] = $this->model->getItemInfo($this->routeVars['itemURL']); # print_r($itemInfo);
        
		# 404
		if (empty($itemInfo['id'])) {
			header("HTTP/1.0 404 Not Found");
			header("Location: http://".DOMAIN);
			exit;
		}
        
        # �������� ���������� �� ������������ ����������
        $parentSectionInfo = $site_section_default_controller->model->getSiteSectionInfo('otzyvy'); # print_r($parentSectionInfo);
        
        # ��������� ��������
        $GLOBALS['tpl_title'] = $itemInfo['name'].', '.$itemInfo['date_add_day'].' '.$itemInfo['date_add_month'].' '.$itemInfo['date_add_year'];
        
        # ������ ���������
        # ������ ��������� � ������ ������
        if (!empty($itemInfo['full_navigation'])) {
            $GLOBALS['tpl_full_navigation'] = $itemInfo['full_navigation'];
            # ���������� ����������, ����� ��� ������������ ������ ������ ��������� �� �����,
            # � �� ����� �� ���� �����
            $GLOBALS['tpl_show_navigation'] = 1;
        }
        # ������ ���������
        else {
            if (!empty($itemInfo['navigation'])) $navigation = $itemInfo['navigation'];
            else $navigation = $itemInfo['name'];
            if (strlen($navigation) > 60) $navigation = cutText($navigation, 60);
            $GLOBALS['tpl_navigation'] = '
            <a href="/otzyvy/">'.$parentSectionInfo['navigation'].'</a> <span>&raquo;</span>
            '.$navigation;

            # ���������� ����������, ����� ��� ������������ ������ ������ ��������� �� �����,
            # � �� ����� �� ���� �����
            $GLOBALS['tpl_show_navigation'] = 1;
        }

        # ��������� h1
        $GLOBALS['tpl_h1'] = $itemInfo['name'];

        # �������� ������ ������� ��� ����� "������ ������"
        $GLOBALS['tpl_another_feedback'] = $this->model->getFeedbackForBlockAnotherFeedback($itemInfo['id']); # echo '<pre>'.(print_r($GLOBALS['tpl_another_feedback'], true)).'</pre>'; # exit;
        foreach ($GLOBALS['tpl_another_feedback'] as &$item) {
            $item['feedback'] = cutText($item['feedback'], 233);
            # ���������� ��������� �������
            if(++$i == $_c) $item['is_last'] = 1;
            else unset($item['is_last']);
        } unset($item); # print_r($GLOBALS['tpl_faq']);

		# �������
		$GLOBALS['tpl_content'] = $this->tpl->getTemplate('feedback_detailed.html');
        
        # ������������ � �������
        if (!empty($itemInfo['footeranchor'])) $GLOBALS['tpl_footeranchor'] = $itemInfo['footeranchor'];

        # �������� ������ ����� �������� � ������� ��� ����������
        $GLOBALS['tpl_hide_feedback'] = 1;

        # �������� ��������������� ����� �������� � ������� ��� ����������
        $GLOBALS['tpl_hide_special_offer'] = 1;

        # ������� ���� "������, �������, ������-�����" � �������
        showBlockInFooter();

        # �������� ���� ������ ��������
        $GLOBALS['tpl_top_menu_active_5'] = ' class="active"';

		# ������� ������ ��� ����������
		$this->tpl->setMainTemplate('template_for_inside_pages_v1.html');
		$this->tpl->echoMainTemplate();
	} # /# ��������� ����� ������
}