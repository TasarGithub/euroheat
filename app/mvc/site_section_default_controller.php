<?php
class site_section_default_controller extends controller_base
{
	function index()
	{
		# print_r($this->route); # echo '<hr />'; exit;
        
		# 301 ���� URL ������������� �� �� "/", ������ 301 �������� �� URL �� "/"
		if ($_SERVER['REQUEST_URI'][strlen($_SERVER['REQUEST_URI']) - 1] != "/"
            && !stristr($_SERVER['REQUEST_URI'], '?')
            && !stristr($_SERVER['REQUEST_URI'], '#')) {
			header("HTTP/1.0 301 Moved Permanently");
			header("Location: http://".DOMAIN.$_SERVER['REQUEST_URI']."/");
			exit;
		}
		
		# ���������� ��������� ������
		# $news_controller = $this->load('news');
		# $faq_controller = $this->load('faq');
		# $feedback_controller = $this->load('feedback');
		
		# �������� ���������� �� �������
		$route = $this->route; # echo 'route: '.$this->route.'<br />';

        # ������������ URL ��� ������������� ������
        if (stristr($this->route, '/page')) $this->route = substr($this->route, 0, strpos($this->route, '/page'));
        # echo 'route: '.$this->route.'<br />';
        # exit;

        # �������� ���������� �� �������
		$siteSectionInfo = $this->model->getSiteSectionInfo($this->route); # echo '<pre>'.(print_r($siteSectionInfo, true)).'</pre>'; exit;

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

		# ��������� h1
		if (!empty($siteSectionInfo['h1'])) $GLOBALS['tpl_h1'] = $siteSectionInfo['h1'];
		# else $GLOBALS['tpl_h1'] = $siteSectionInfo['name'];

        # ������ ���������
        # ������ ��������� � ������ ������
        if (!empty($siteSectionInfo['full_navigation'])) {
            $GLOBALS['tpl_full_navigation'] = $siteSectionInfo['full_navigation'];
            # ���������� ����������, ����� ��� ������������ ������ ������ ��������� �� �����,
            # � �� ����� �� ���� �����
            $GLOBALS['tpl_show_navigation'] = 1;
        }
        # ������ ���������
        else {
            if (!empty($siteSectionInfo['navigation'])) {
                $GLOBALS['tpl_navigation'] = $this->buildNavigtaion($route, '');

                # ���������� ����������, ����� ��� ������������ ������ ������ ��������� �� �����,
                # � �� ����� �� ���� �����
                $GLOBALS['tpl_show_navigation'] = 1;
            }
        }

        # echo $this->route;

        ### ������������� ����������

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

            # �������� ������ ��������
            $newsResult = $news_controller->model->getItemsForIndex();

            if (!empty($newsResult['resultSet'])) $GLOBALS['tpl_news'] = $newsResult['resultSet'];
            if (!empty($newsResult['pagesSet'])) $GLOBALS['tpl_news_pages'] = $newsResult['pagesSet'];
            if (!empty($newsResult['allRowsCount'])) $GLOBALS['tpl_news_count'] = $newsResult['allRowsCount'];
        }

        elseif ($this->route == 'vopros') { # http://euroheater.ru/vopros/
            $faq_controller = $this->load('faq');

            # �������� ������ ��������
            $faqResult = $faq_controller->model->getItemsForIndex();

            if (!empty($faqResult['resultSet'])) $GLOBALS['tpl_faq'] = $faqResult['resultSet'];
            if (!empty($faqResult['pagesSet'])) $GLOBALS['tpl_faq_pages'] = $faqResult['pagesSet'];
            if (!empty($faqResult['allRowsCount'])) $GLOBALS['tpl_faq_count'] = $faqResult['allRowsCount'];
        }

        elseif ($this->route == 'otzyvy') { # http://euroheater.ru/otzyvy/
            $feedback_controller = $this->load('feedback');

            # �������� ������ �������
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

            # �������� ������ ����� �������� � ������� ��� ����������
            $GLOBALS['tpl_hide_feedback'] = 1;

            # �������� ��������������� ����� �������� � ������� ��� ����������
            $GLOBALS['tpl_hide_special_offer'] = 1;

            # �������� ���� ������ ��������
            $GLOBALS['tpl_top_menu_active_5'] = ' class="active"';
        }

        elseif ($this->route == 'sovet') { # http://euroheater.ru/sovet/
            $articles_controller = $this->load('articles');

            # �������� ������ ������
            $articlesResult = $articles_controller->model->getItemsForIndex();

            if (!empty($articlesResult['resultSet'])) $GLOBALS['tpl_articles'] = $articlesResult['resultSet'];
            if (!empty($articlesResult['pagesSet'])) $GLOBALS['tpl_articles_pages'] = $articlesResult['pagesSet'];
            if (!empty($articlesResult['allRowsCount'])) $GLOBALS['tpl_articles_count'] = $articlesResult['allRowsCount'];
        }

        elseif ($this->route == 'uslugi') { # http://euroheater.ru/uslugi/
            # �������� ���� ������ ��������
            $GLOBALS['tpl_top_menu_active_1'] = ' class="active"';
        }

        elseif ($this->route == 'oborudovanie') { # http://euroheater.ru/oborudovanie/
            # �������� ���� ������ ��������
            $GLOBALS['tpl_top_menu_active_2'] = ' class="active"';
        }

        elseif ($this->route == 'o-nas') { # http://euroheater.ru/o-nas/
            # �������� ���� ������ ��������
            $GLOBALS['tpl_top_menu_active_3'] = ' class="active"';
        }
        elseif ($this->route == 'o-nas/vakansii') { # http://euroheater.ru/o-nas/vakansii/
            # �������� ���� ������ ��������
            $GLOBALS['tpl_top_menu_active_3'] = ' class="active"';
        }
        elseif ($this->route == 'o-nas/missiya-i-cennosti') { # http://euroheater.ru/o-nas/missiya-i-cennosti/
            # �������� ���� ������ ��������
            $GLOBALS['tpl_top_menu_active_3'] = ' class="active"';
        }
        elseif ($this->route == 'o-nas/dokumenty-dogovora') { # http://euroheater.ru/o-nas/dokumenty-dogovora/
            # �������� ���� ������ ��������
            $GLOBALS['tpl_top_menu_active_3'] = ' class="active"';
        }
        elseif ($this->route == 'o-nas/nashi-klienty') { # http://euroheater.ru/o-nas/nashi-klienty/
            # �������� ���� ������ ��������
            $GLOBALS['tpl_top_menu_active_3'] = ' class="active"';
        }

        elseif ($this->route == 'tarify') { # http://euroheater.ru/tarify/
            # �������� ���� ������ ��������
            $GLOBALS['tpl_top_menu_active_4'] = ' class="active"';
        }

        elseif ($this->route == 'proekty') { # http://euroheater.ru/proekty/
            # �������� ���� ������ ��������
            $GLOBALS['tpl_top_menu_active_6'] = ' class="active"';
        }

        ### /������������� ����������
		
        # ����� �������
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

        # ������� ���� "������, �������, ������-�����" � �������
        showBlockInFooter();

        # ������������ � �������
        if (!empty($siteSectionInfo['footeranchor'])) $GLOBALS['tpl_footeranchor'] = $siteSectionInfo['footeranchor'];
		# ������� ������ ��� ����������
		$this->tpl->setMainTemplate('template_for_inside_pages_v1.html');
		$this->tpl->echoMainTemplate();
	}
	
	# ��������� ������ ���������
    # ������� ����������:
    # $itemFullURL: ������ URL � ������� (���� "full_url" � ������� "site_sections"), ������: "uslugi/mebel"
    # $isLastLink: 1 - ��������� ������ ���� ��� ������, NULL - ��������� ������ ���� ��� ������
    function buildNavigtaion($itemFullURL, $isLastLink = NULL)
	{
		# �������� ����������
		if (empty($itemFullURL)) return;
		
		$urls = explode('/', $itemFullURL); # echo '<pre>'.(print_r($urls, true)).'</pre>';

        # ������� ������ ��������� � ��������, ������� � 3 ������
        # ������: http://www.zapashnyh.ru/biografiya/edgard/
        # ��� �������� 2 ������ ������ ��������� �� �������
        if (count($urls) == 1) return '';

		if (is_array($urls)) {
			# $result[] = '<a href="/">�������</a>';
			unset($fullIterationUrl);
            $last_key = end(array_keys($urls));
			foreach ($urls as $k => $v) { # echo 'k: '.$k.', v: '.$v.'<br />';
                
                # ���������� ������������ �����
                if (stristr($v, 'page')) continue;

                # fullIterationUrl
				if (empty($fullIterationUrl)) $fullIterationUrl = $v;
				else $fullIterationUrl .= '/'.$v;
				# echo 'fullIterationUrl: '.$fullIterationUrl.'<hr />';
				
				$name = $this->model->getNavigationByFullURL($fullIterationUrl);
				
				# echo $v.'<br />';
				$url = substr($itemFullURL, 0, strpos($itemFullURL, $v));
				# ��������� ������
                if ($k == $last_key) {
                    if (!empty($isLastLink)) $result[] = '<a href="/'.$fullIterationUrl.'/">'.$name.'</a>';
                    else $result[] = $name;
                }
                # �� ��������� ������
                else $result[] = '<a href="/'.$fullIterationUrl.'/">'.$name.'</a>';
			}
			$result = implode(' <span>&raquo;</span> ', $result);
			return $result;
		}
	} # /��������� ������ ���������

    # URL SELECTOR: ���� ��� ��������� ������, ���� ��� �������������� ��������
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
    # /URL SELECTOR: ���� ��� ��������� ������, ���� ��� �������������� ��������
}

# �������



# /�������