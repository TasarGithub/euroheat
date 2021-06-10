<?php
# ������ "����� �����". ������ ����� ����� �� ������� "site_sections"
# romanov.egor@gmail.com
# 2013.9.14

class map_controller extends controller_base
{
	protected $allSiteSections; # ������������� ������ �� ����� ��������� �����
	protected $maxDepth; # ����������� ��������� ������� ���������� ����� �����
	protected $depth; # ������� �������� ������� ��� ���������� ����� �����
	# protected $prependSymbol; # ������ ����� ��������� �������
	protected $itemID; # ���� �������� �������� �������������� ������� $this->allSiteSections � �������� ��� ���������� ����� �����
	protected $result; # ��������� ���������� ����� �����
	# protected $indent; # �������� ������� (� px) ��� ��������� � ����� �����
	
	# ����� ����� ��� transpark.ru
    function index()
	{
        # 404
        if ($this->route == 'map') {
            header("HTTP/1.0 404 Not Found");
            header("Location: http://".$_SERVER['SERVER_NAME']);
            exit;
        }
        
		# 301 ���� URL ������������� �� �� "/", ������ 301 �������� �� URL �� "/"
		if ($_SERVER['REQUEST_URI'][strlen($_SERVER['REQUEST_URI']) - 1] != "/"
            && !stristr($_SERVER['REQUEST_URI'], '?')
            && !stristr($_SERVER['REQUEST_URI'], '#')) {
			header("HTTP/1.0 301 Moved Permanently");
			header("Location: http://".DOMAIN.$_SERVER['REQUEST_URI']."/");
			exit;
		}
        
		# ���������
		$this->maxDepth = 15;
		$this->depth = 1;
		# $this->prependSymbol = '&nbsp;';
		$this->itemID = 0;
		# $this->indent = 25;
        
		# ���������� ��������� �����������
		$site_section_default = $this->load('site_section_default');

		# �������� ���������� �� �������
		$siteSectionInfo = $site_section_default->model->getSiteSectionInfo('karta-sajta'); # echo '<pre>'.(print_r($siteSectionInfo, true)).'</pre>';

		# page title
		if (!empty($siteSectionInfo['page_title'])) $GLOBALS['tpl_title'] = $siteSectionInfo['page_title'];
		else $GLOBALS['tpl_title'] = $siteSectionInfo['name'];

        # ������ ��������� � ������ ������
        if (!empty($siteSectionInfo['full_navigation'])) $GLOBALS['tpl_full_navigation'] = $siteSectionInfo['full_navigation'];
        # ������ ���������
        else {
            # ������ ���������
            if (!empty($siteSectionInfo['navigation'])) $navigation = $siteSectionInfo['navigation'];
            else $navigation = $siteSectionInfo['h1'];
            $GLOBALS['tpl_navigation'] = '
            <a href="/sitemap/">'.$navigation.'</a>';
        }

		# ��������� h1
		if (!empty($siteSectionInfo['h1'])) $GLOBALS['tpl_h1'] = $siteSectionInfo['h1'];
		else $GLOBALS['tpl_h1'] = $siteSectionInfo['name'];
				
		# ������� ������ ���� �������� �� ������� site_sections
		# $this->tpl->text = '';
		$this->allSiteSections = $this->model->getSectionsList(); # echo '<pre>'.(print_r($this->allSiteSections, true)).'</pre>';
		
		# ����������� ������� ���������� ����� ����� �� ������� site_sections
		$this->buildSiteMap(); # echo '<pre>'.(print_r($this->result, true)).'</pre>';
		$GLOBALS['tpl_all_site_sections'] = $this->result; # echo '<pre>'.(print_r($GLOBALS['tpl_all_site_sections'], true)).'</pre>';
		
		# ������������� ����������
		# unset($additionalSections);
		# $this->buildAdditionalSections(); # echo $additionalSections.'<hr />';
		
        # ���������� ������
		# $GLOBALS['tpl_content'] = $this->result; # print_r($this->result);

        # ������������ � �������
        if (!empty($siteSectionInfo['footeranchor'])) $GLOBALS['tpl_footeranchor'] = $siteSectionInfo['footeranchor'];
        
        # ����� �������
        if (!empty($siteSectionInfo['file_name_1'])) $GLOBALS['tpl_content'] = getContent('/app/site_sections/'.$siteSectionInfo['file_name_1']);
		
		# ������� ������ ��� ����������
		$this->tpl->setMainTemplate('template_for_inside_pages_v1.html');
		$this->tpl->echoMainTemplate();
	}
    # /����� ����� ��� transpark.ru
    
	# ����������� ������� ���������� ����� ����� ��� transprak.ru
	function buildSiteMap()
	{ 
		# �������
		# echo $this->itemID.'<hr />';
		
		# ������ �� ������������
		if ($this->depth > $this->maxDepth) return;
	
		# echo '<pre>'.(print_r($this->allSiteSections, true)).'</pre>';
		
		# ��������� �������� ������� ��� ������ �������
		# $indent = 0 + ($this->depth * $this->indent);
		# $indentForChilds = $indent + $this->indent;
		
		# ��������� ������ ����� ��������� �������
		# $prependSymbol = str_repeat($this->prependSymbol, $this->depth)."&gt; ";
		
		# ��������� � ��������� ���������� �� �������� ��������
		# if (!empty($this->allSiteSections[$this->itemID]['full_url'])) $this->allSiteSections[$this->itemID]['full_url'] .= '/';
		
		# $this->result[] = "<div style='padding-left:".$indent."px'><a href='/{$this->allSiteSections[$this->itemID]['full_url']}'>{$this->allSiteSections[$this->itemID]['name']}</a></div>".$_result;
		
        # print_r($this->allSiteSections);
        
		if (!empty($this->allSiteSections[$this->itemID]['is_showable']))
		{
			# echo $this->allSiteSections[$this->itemID]['name'].'<hr />';
		
			# ��������� � �����

			# faq: all items count
			if ($this->allSiteSections[$this->itemID]['full_url'] == 'vopros')
			{
				$_controller = $this->load('faq');
				$allItemsCount = $_controller->model->getItemsCount(); # echo $allItemsCount.'<hr />';
				$this->allSiteSections[$this->itemID]['name'] .= ' ('.$allItemsCount.')';
			}
            
			# ������ ��������: all items count
			if ($this->allSiteSections[$this->itemID]['full_url'] == 'otzyvy')
			{
				$_controller = $this->load('feedback');
				$allItemsCount = $_controller->model->getItemsCount(); # echo $allItemsCount.'<hr />';
				$this->allSiteSections[$this->itemID]['name'] .= ' ('.$allItemsCount.')';
			}
            
			# articles: all items count
			if ($this->allSiteSections[$this->itemID]['full_url'] == 'sovet')
			{
				$_controller = $this->load('articles');
				$allItemsCount = $_controller->model->getItemsCount(); # echo $allItemsCount.'<hr />';
				$this->allSiteSections[$this->itemID]['name'] .= ' ('.$allItemsCount.')';
			}

			# news: all items count
			if ($this->allSiteSections[$this->itemID]['full_url'] == 'novosti')
			{
				$_controller = $this->load('news');
				$allItemsCount = $_controller->model->getItemsCount(); # echo $allItemsCount.'<hr />';
				$this->allSiteSections[$this->itemID]['name'] .= ' ('.$allItemsCount.')';
			}
            
			# vacancies: all items count
			if ($this->allSiteSections[$this->itemID]['full_url'] == 'o-nas/vakansii')
			{
				# $_controller = $this->load('vacancies');
				# $allItemsCount = $_controller->model->getItemsCount(); # echo $allItemsCount.'<hr />';
				# $this->allSiteSections[$this->itemID]['name'] .= ' ('.$allItemsCount.')';
			}

            # catalog: all items count
            if ($this->allSiteSections[$this->itemID]['full_url'] == 'generatora') {
                $_controller = $this->load('catalog');
                $allItemsCount = $_controller->model->getItemsCount(); # echo $allItemsCount.'<hr />';
                $this->allSiteSections[$this->itemID]['name'] .= ' ('.$allItemsCount.')';
            }

			# url
			if ($this->allSiteSections[$this->itemID]['full_url'] != '/') $url = '/'.$this->allSiteSections[$this->itemID]['full_url'].'/';
			else $url = '/';
			# echo $url.'<hr />';

            if (!strpos($this->allSiteSections[$this->itemID]['full_url'], 'test')) {

                $this->result[] = array('level' => $this->depth,
                                        'url' => $url,
                                        'name' => $this->allSiteSections[$this->itemID]['name']
                                        );

            }

			# echo 'full_url: '.$this->allSiteSections[$this->itemID]['full_url'].', depth: '.$this->depth.'<hr />';

            # �������: http://www.euroheater.fboss.ru/generatora/
            if ($this->allSiteSections[$this->itemID]['full_url'] == 'generatora' && $this->depth == 2) {
                # ���������� ���������� ""
                $_controller = $this->load('catalog');
                # ���������� ������� �
                $_ = $_controller->model->getItemsForMap(); # echo '<pre>'.(print_r($_, true)).'</pre>';
                $_c = count($_); # echo $_c.'<hr />';
                if (!empty($_c)) {
                    for ($i=0;$i<$_c;$i++) {
                        $this->result[] = array('level' => 3,
                            'url' => '/generatora/'.$_[$i]['url'].'/',
                            'name' => $_[$i]['name'].' ('.$_[$i]['cost'].' ���.)'
                        );
                    } # print_r($array);
                }
            } # /�������

			# ������
			if ($this->allSiteSections[$this->itemID]['full_url'] == 'sovet' && $this->depth == 2)
			{
				# ���������� ���������� ""
				$_controller = $this->load('articles');
				# ���������� ������� � 
				$_ = $_controller->model->getItemsForMap(); # echo '<pre>'.(print_r($_, true)).'</pre>';
				$_c = count($_); # echo $_c.'<hr />';
				if (!empty($_c))
				{
					for ($i=0;$i<$_c;$i++)
					{
						$this->result[] = array('level' => 3,
												'url' => '/sovet/'.$_[$i]['url'].'/',
												'name' => $_[$i]['name']
												);
					} # print_r($array);
				}
			} # /������
			
			# �������-������
			if ($this->allSiteSections[$this->itemID]['full_url'] == 'vopros' && $this->depth == 2)
			{
				# ���������� ����������
				$_controller = $this->load('faq');
				# ���������� ������� � 
				$_ = $_controller->model->getItemsForMap(); # echo '<pre>'.(print_r($_, true)).'</pre>';
				$_c = count($_); # echo $_c.'<hr />';
				if (!empty($_c))
				{
					for ($i=0;$i<$_c;$i++)
					{
						$this->result[] = array('level' => 3,
												'url' => '/vopros/'.$_[$i]['url'].'/',
												'name' => $_[$i]['name']
												);
					} # print_r($array);
				}
			} # /�������-������
            
            # ������ ��������
			if ($this->allSiteSections[$this->itemID]['full_url'] == 'otzyvy' && $this->depth == 2)
			{
				# ���������� ����������
				$feedback_controller = $this->load('feedback');
				# ���������� ������� � 
				$_ = $feedback_controller->model->getItemsForMap(); # echo '<pre>'.(print_r($_, true)).'</pre>';
				$_c = count($_); # echo $_c.'<hr />';
				if (!empty($_c))
				{
					for ($i=0;$i<$_c;$i++)
					{
						$this->result[] = array('level' => 3,
												'url' => '/otzyvy/'.$_[$i]['id'].'/',
												'name' => $_[$i]['name'].', '.$_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year']
												);
					} # print_r($array);
				}
			} # /������ ��������
			
			# �������
			if ($this->allSiteSections[$this->itemID]['full_url'] == 'novosti'	&& $this->depth == 2)	{
				# ���������� ����������
				$_controller = $this->load('news');
				# ���������� �������
				$_ = $_controller->model->getItemsForMap(); # echo '<pre>'.(print_r($_, true)).'</pre>';
				$_c = count($_); # echo $_c.'<hr />';
				if (!empty($_c))
				{
					for ($i=0;$i<$_c;$i++)
					{
						$this->result[] = array('level' => 3,
												'url' => '/novosti/'.$_[$i]['date_add_formatted_2'].'/',
												'name' => $_[$i]['h1'],
												'date_add' => $_[$i]['date_add_formatted']
												);
					} # print_r($array);
				}
			} # /�������
            
			# ���
			if ($this->allSiteSections[$this->itemID]['full_url'] == 'shou' && $this->depth == 2) {
				# ���������� ����������
				$_controller = $this->load('shows');
				# ���������� �������
				$_ = $_controller->model->getItemsForMap(); # echo '<pre>'.(print_r($_, true)).'</pre>';
				$_c = count($_); # echo $_c.'<hr />';
				if (!empty($_c)) {
					for ($i=0;$i<$_c;$i++) {
						$this->result[] = array('level' => 3,
												'url' => '/shou/'.$_[$i]['url'].'/',
												'name' => $_[$i]['name'],
												'date_add' => ''
												);
					} # print_r($array);!!!
				}
			} # /���

            # �����������
            if ($this->allSiteSections[$this->itemID]['full_url'] == 'foto' && $this->depth == 2) {
                # ���������� ����������
                $_controller = $this->load('photos');
                # ���������� �������
                $_ = $_controller->model->getPhotoalbumsForMap(); # echo '<pre>'.(print_r($_, true)).'</pre>';
                $_c = count($_); # echo $_c.'<hr />';
                if (!empty($_c)) {
                    for ($i=0;$i<$_c;$i++) {
                        $this->result[] = array('level' => 3,
                            'url' => '/foto/'.$_[$i]['url'].'/',
                            'name' => $_[$i]['name'],
                            'date_add' => ''
                        );
                        # �������� ������ ���� ��� �����������
                        $_2 = $_controller->model->getPhotosForMap($_[$i]['id']); # echo '<pre>'.(print_r($_, true)).'</pre>';
                        if (!empty($_2)) {
                            foreach ($_2 as $item) {
                                $this->result[] = array('level' => 4,
                                    'url' => '/foto/'.$item['photoalbum_url'].'/'.$item['url'].'/',
                                    'name' => $item['name'],
                                    'date_add' => ''
                                );
                            }
                        }
                        # /�������� ������ ���� ��� �����������
                    } # echo '<pre>'.(print_r($array, true)).'</pre>'; # exit;
                }
            } # /�����������
            
		}
		
		# ������� ��������� ������ �����������, ���� �� ������
		if (!empty($_result))
		{
			# $_result = "<div style='padding-left:{$indentForChilds}px'>{$_result}</div>";
			if (!empty($_result) and is_array($_result))
			{
				$this->result = array_merge($this->result, $_result);
			}
		}
		
		# �������� ���������� �� ��������
		$_ = $this->multidimensionalSearch($this->allSiteSections, array('parent_id' => $this->allSiteSections[$this->itemID]['id'])); # print_r($_);
		$_c = count($_); # echo $_c.'<hr />';
		if (!empty($_c))
		{
			# ����������� ������� �� 1
			$this->depth++;
			for ($i=0;$i<$_c;$i++)
			{
				# �������
				# echo $_[$i].'<hr />';
				
				$this->itemID = $_[$i];
				
				# ��� ������� ������� �������� ����������� �������
				$functionResult = $this->buildSiteMap();
				
				# ���� ��� ��������� ������� � ������ �������� � � ���� ��� ��������, �������� �������
				$ii = $i + 1;
				if (empty($functionResult) && ($ii == $_c)){
					# ��������� ������� �� 1
					$this->depth--;
				}
			}
		}
		else return;
	} # /����������� ������� ���������� ����� ����� ��� transprak.ru
	
	/*
	������� ������ �� ������������ �������
	����� � http://ru2.php.net/manual/ru/function.array-search.php � ����������
	������ �������������:
	$parents = array();
	$parents[] = array('date' => 1320883200, 'uid' => 3);
	$parents[] = array('date' => 1320883200, 'uid' => 5);
	$parents[] = array('date' => 1318204800, 'uid' => 5);
	echo multidimensional_search($parents, array('date' => 1320883200, 'uid' => 5)); // 1
	*/
	function multidimensionalSearch($parents, $searched)
	{
		if (empty($searched) || empty($parents)) return;
	 
		$keysResult = array();
		foreach ($parents as $key => $value)
		{
			$exists = true;
			foreach ($searched as $skey => $svalue)
			{
				$exists = ($exists && isset($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
			}
			# if ($exists) return $key;
			if ($exists) $keysResult[] = $key;
		}
		if (!empty($keysResult)) return $keysResult;
		else return;
	}
}