<?php 
# ������ ������� ��� ������ � �������� (������� online_requests)
# romanov.egor@gmail.com; 2015.10.19

# ���������� ���� �������
include('../loader.control.php');

# ���������� ����� ������� ��� index.php � ajax.php
include('common.functions.php');

# ���������
$GLOBALS['tpl_title'] = '������-������';

# ������
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ������

if ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > ������� ������';
    $GLOBALS['tpl_h1'] = '������� ������';
    $GLOBALS['tpl_content'] = deleteItem();
}
else {
    $GLOBALS['tpl_title'] = '��� ������-������';
    $GLOBALS['tpl_h1'] = '��� ������-������ ('.$dbh->query('select count(1) from '.DB_PREFIX.'online_requests')->fetchColumn().')';
    $GLOBALS['tpl_content'] = showItems();
}

# /������

# ������� ������� ������
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ����������

# ��������� ������ ���� ������
function showItems($count = null)
{
    global $dbh;
    
    # �������� ������ ������
    $sql = '
    select t1.id,
           t1.date_add,
           date_format(t1.date_add, "%e") as date_add_day,
           elt(month(t1.date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
           date_format(t1.date_add, "%Y") as date_add_year,
           date_format(t1.date_add, "%H:%i") as date_add_time,
           t1.order_content,
           t2.name as type_name
    from '.DB_PREFIX.'online_requests as t1
    left outer join online_requests_types as t2
        on t2.id = t1.request_type_id
    order by t1.id desc
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sql_for_count = '
    select count(id)
    from '.DB_PREFIX.'online_requests
    '; # echo '<pre>'.$sql_for_count."</pre><hr />";
	$pages = new pages($_GET["page"], # ������� ��������
					   25, # ������� �� ��������
					   $dbh, # ������ ���� ������
                       '', # routeVars
					   $sql, # sql-������
					   $sql_for_count, # sql-������ ��� �������� ���������� �������
					   '/control/online_requests/', # ����� �� 1� ��������
					   '/control/online_requests/?page=%page%', # ����� �� ��������� ��������
						1500 # ������������ ���������� ������� �� ��������
						);
	$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>'; exit;
    $_ = $_result['resultSet'];
    if (!empty($_result['pagesSet'])) $pagesList = '<div class="pages_set">��������: '.$_result['pagesSet'].'</div>';
    $_c = count($_);
	$rows = array();
    for ($i=0;$i<$_c;$i++) {
        # type_name
        if (!empty($_[$i]['type_name'])) $typeName = '<b>'.$_[$i]['type_name'].'</b>';
        else unset($typeName);

        $rows[] = '
		<tr>
			<td>'.$typeName.'<pre class="no_border">'.$_[$i]['order_content'].'</pre></td>
			<td><pre class="no_border">'.$_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year'].' '.$_[$i]['date_add_time'].'</pre></td>
			<td class="center vertical_middle">
                <a class="block" title="������� ������" href="/control/online_requests/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'������ ����� ������� ������������. ������� ������?\')">
                    <i class="fa fa-trash-o size_18"></i>
                </a>
			</td>
		</tr>
		';
    } # /��������� ������ ������
	
	if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
	else unset($rows);
    
    $result = '
    <div style="width:50%;float:left">
        <b>URL:</b>&nbsp; <a href="/" target="_blank">http://'.$_SERVER['SERVER_NAME'].'</a>
    </div>

    <br style="clear:both" />
    <br />
    ';
    
    if (empty($rows)) $result .= '� ������� ��� ������.';
    else {
        $result .= '
        <div id="resultSet">
        <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
            <tr>
                <th class="center vertical_middle">������</th>
                <th class="center vertical_middle" style="width:200px">����</th>
                <th class="center vertical_middle" style="width:100px;white-space:nowrap">��������</th>
            </tr>
            '.$rows.'
        </table>
        '.$pagesList.'
        </div>';
    }
    
    return $result;
} # /��������� ������ ���� ������

# ������� ������
function deleteItem(){
	
	global $dbh;
	
	# �������� ����������
	if (empty($_GET['itemID']))	{
		# ������� ������
		$GLOBALS['tpl_failure'] = '������ �� �������. ����������, ���������� � ������������� �����.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
		# ������� ������ ������
        showItems();
	}
	else {
		# ������� ������ �� ��
        $sql = '
        delete from '.DB_PREFIX.'online_requests
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute()) {
			$GLOBALS['tpl_success'] = '������ ������� �������.';
            
			# ������� ������ ������
			return showItems();
		}
		else
		{
            if (empty($GLOBALS['tpl_failure'])) $GLOBALS['tpl_failure'] = '� ���������, ������ �� �������. ����������, ���������� � ������������� �����.';
			# ������� ������ ������
			return showItems();
		}
	}
} # /������� ������

# /����������