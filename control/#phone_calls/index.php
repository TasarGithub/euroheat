<?php 
# ������ ������� ��� ������ � ����������� ��������
# * ������ ������� �� mango-office.ru
# * ������� "mango_calls"
# romanov.egor@gmail.com; 2015.12.25

# ���������� ���� �������
include('../loader.control.php');

# ���������� ����� ������� ��� index.php � ajax.php
include('common.functions.php');

# ���������
$GLOBALS['tpl_title'] = '���������� ������';

# ������
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ������

if ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > ������� ������';
    $GLOBALS['tpl_h1'] = '������� ������';
    $GLOBALS['tpl_content'] = deleteItem();
}
else {
    $GLOBALS['tpl_title'] = '��� ������';

    # $GLOBALS['tpl_h1'] = '��� ������ ('.$dbh->query('select count(1) from '.DB_PREFIX.'mango_calls')->fetchColumn().')';
    $GLOBALS['tpl_h1'] = '��� ������';
    $GLOBALS['tpl_content'] = showItems();
}

# /������

# ������� ������� ������
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ����������

# ��������� ������ ���� �������
function showItems($count = null)
{
    global $dbh;

    # �������� ������ �������
    $sql = '
    select id,
           date_format(date_add, "%e") as date_add_day,
           elt(month(date_add), "���.", "���.", "���.", "���.", "���", "����", "����", "���.", "���.", "���.", "���.", "���.") as date_add_month,
           date_format(date_add, "%Y") as date_add_year,
           date_format(date_add, "%H:%i") as date_add_time,
           date_format(date_add, "%Y-%m-%d") as date_add_modified,
           date_format(date_add, "%Y.%m.%d_%H.%i") as date_add_for_file_name_for_download,
           caller,
           talk_duration,
           is_missed_call,
           reason_code,
           reason,
           call_record
    from '.DB_PREFIX.'mango_calls
    order by date_add desc
    '; # echo '<pre>'.$sql."</pre><hr />";
    # date_format(date_add, "%c") as date_add_month,
    $sql_for_count = '
    select count(id)
    from '.DB_PREFIX.'mango_calls
    '; # echo '<pre>'.$sql_for_count."</pre><hr />";
	$pages = new pages($_GET["page"], # ������� ��������
					   25, # ������� �� ��������
					   $dbh, # ������ ���� ������
                       '', # routeVars
					   $sql, # sql-������
					   $sql_for_count, # sql-������ ��� �������� ���������� �������
					   '/control/phone_calls/'.str_replace('&', '?', $pageVariable), # ����� �� 1� ��������
					   '/control/phone_calls/?page=%page%'.$pageVariable, # ����� �� ��������� ��������
						1500 # ������������ ���������� ������� �� ��������
						);
	$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>'; exit;
    $_ = $_result['resultSet'];
    if (!empty($_result['pagesSet'])) $pagesList = '<div class="pages_set" style="margin-bottom:15px;text-align:left">��������: '.$_result['pagesSet'].'</div>';
    $_c = count($_);
	$rows = array();
    for ($i=0;$i<$_c;$i++) {
        # talk_duration: ��������� ������� � ������ � �������
        if ($_[$i]['talk_duration'] > 60) {
            $duration = floor($_[$i]['talk_duration'] / 60);
            $remainder = $_[$i]['talk_duration'] % 60;
            $talkDurationModified = $duration.' �. '.$remainder.' �.';
        }
        else $talkDurationModified = $_[$i]['talk_duration'] . ' �.';

        # talk_duration
        if (empty($_[$i]['is_missed_call'])) $talk_duration = $talkDurationModified;
        else {
            if (empty($_[$i]['reason'])) $_[$i]['reason'] = '������� �� �������';

            $talk_duration = '<span class="red">'.$_[$i]['reason'].'</span> (' . $talkDurationModified . ')';
        }

        # date_add_day
        if ($_[$i]['date_add_modified'] == date('Y-m-d')) $dateAdd = '<span class="bold">������� � '.$_[$i]['date_add_time'].'</span>';
        else $dateAdd = $_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year'].', '.$_[$i]['date_add_time'];

        # ������������� ������ ���������
        if (!empty($_[$i]['call_record'])) $listen = '
        <a class="audio {skin:\'blue\', showTime:true, downloadable:true }" href="/control/phone_calls/get_call_record.php?call_record='.$_[$i]['call_record'].'"></a>';
        else unset($listen);

        # ��� ����� ��� ����������
        $callRecord = HTTP_HOST.'_'.$_[$i]['date_add_for_file_name_for_download'].'_s_nomera_'.$_[$i]['caller'].'.mp3';

        # caller
        unset($caller);
        # if ($_[$i]['caller'] == '74996775485') $caller = '74996775485<sup>������</sup>';
        if ($_[$i]['caller'] == '74996775485') $caller = '<span class="chaser">74996775485</span><sup><img src="/control/public/images/chaser_logo_mini.png" /></sup>';
        else $caller = $_[$i]['caller'];

        $rows[] = '
		<tr>
		    <td class="vertical_middle nowrap for_player" data-call-record="'.$callRecord.'">'.$listen.'&nbsp;</td>
			<td class="center vertical_middle nowrap">'.$dateAdd.'</td>
            <td class="center vertical_middle nowrap">'.$caller.'</td>
            <td class="center vertical_middle nowrap">'.$talk_duration.'</td>
		</tr>
		';
    } # /��������� ������ �������
	
	if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
	else unset($rows);

    $result = '
    <script src="/control/phone_calls/index.js?v=4.0"></script>

    <b>URL:</b>&nbsp; <a href="/" target="_blank">http://'.$_SERVER['SERVER_NAME'].'</a>

    <!-- ���������� -->
    <div class="sorting" style="display:inline;margin-left:35px"><b>����������:</b> &nbsp;
    <a href="#" data-sorting-type="accepted">��������</a> <span style="color:#ccc">|</span>
    <a href="#" data-sorting-type="missed">�����������</a> <span style="color:#ccc">|</span>
    <a href="#" data-sorting-type="chaser">������</a> <span style="color:#ccc">|</span>
    <a href="#" data-sorting-type="all">���</a>
    </div>
    <!-- /���������� -->

    <!-- ����� -->
    <div class="searching" style="margin-top:15px">
    <b>����� �� ���� �:</b> &nbsp;
    <input id="search_by_date_from" class="form-control form_required" type="text" value="'.date('d.m.Y', strtotime('-7 days', strtotime(date('d.m.Y')))).'" style="display:inline-block;width:110px;height:24px" placeholder="'.date('d.m.Y', strtotime('-7 days', strtotime(date('d.m.Y')))).'" />
    &nbsp;
    <b>��</b> &nbsp; <input id="search_by_date_to" class="form-control form_required" type="text" value="'.date('d.m.Y').'" style="display:inline-block;width:110px;height:24px" placeholder="'.date('d.m.Y').'" />
     &nbsp;
    <button id="search_by_date_button" class="btn btn-success" type="button" style="height:24px;padding-top:0;padding-bottom:0;margin-top:-3px;padding-left: 3px;padding-right:6px">
        <i class="fa" style="margin-right:3px"></i>������</button>
     &nbsp;
    <button id="export_to_excel" class="btn btn-success" type="button" style="height:24px;padding-top:0;padding-bottom:0;margin-top:-3px;padding-left: 3px;padding-right:6pxk">
        <i class="fa" style="margin-right:3px"></i>��������� � Excel</button>
    </div>
    <!-- /����� -->

    <!-- ���������� -->
    <div id="statistics" style="margin-top:15px">
    '.getCallsStatistics().'
    </div>
    <!-- /���������� -->

    <br />
    ';
    
    if (empty($rows)) $result .= '� ������� ��� �������.';
    else {
        $result .= '

        <div id="resultSet">
        <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list" style="width:1px;overflow:hidden">
            <tr>
                <th class="center vertical_middle nowrap" style="min-width:100px">����������</th>
                <th class="center vertical_middle nowrap" style="min-width:100px">���� � �����</th>
                <th class="center vertical_middle nowrap" style="min-width:100px">����� ����������</th>
                <th class="center vertical_middle">�����������������</th>
            </tr>
            '.$rows.'
        </table>
        '.$pagesList.'
        </div>';
        # style="min-width:315px"
    }
    
    return $result;
} # /��������� ������ ���� �������

# ������� ������
function deleteItem(){
	
	global $dbh;
	
	# �������� ����������
	if (empty($_GET['itemID']))	{
		# ������� ������
		$GLOBALS['tpl_failure'] = '������ �� ������. ����������, ���������� � ������������� �����.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
		# ������� ������ �������
        showItems();
	}
	else {
		# ������� ������ �� ��
        $sql = '
        delete from '.DB_PREFIX.'mango_calls
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute()) {
			$GLOBALS['tpl_success'] = '������ ������� ������.';
            
			# ������� ������ �������
			return showItems();
		}
		else
		{
            if (empty($GLOBALS['tpl_failure'])) $GLOBALS['tpl_failure'] = '� ���������, ������ �� ������. ����������, ���������� � ������������� �����.';
			# ������� ������ �������
			return showItems();
		}
	}
} # /������� ������

# /����������