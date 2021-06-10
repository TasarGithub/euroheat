<?php

### �������
# print_r($_POST);
# print_r($_POST);

# $a = unserialize($_POST['form']); print_r($a);

# sleep(5);

# ������ �� ������� c ������� �����
if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) exit('');

# ��������� ���������, ������� ����� �������� javascript'� ajax-������
header('Content-type: text/html; charset=windows-1251');

# ���������� � �������������� ����� ��� ������ � �� ����� PDO
include($_SERVER['DOCUMENT_ROOT'].'/control/db.connection.pdo.php');

# ���������� ������
include($_SERVER['DOCUMENT_ROOT'].'/control/config.control.php');

# ���������� ������� ������ ���������� ��� ajax-��������
include($_SERVER['DOCUMENT_ROOT'].'/control/functions.common.ajax.php');

# ���������� ����� ������� ��� index.php � ajax.php
include('common.functions.php');

# �������� + ������ ��������� POST-����������
preparePOSTVariables(); # print_r($_POST); exit;

# �������������� ���� �����
if (!empty($_POST['params'])) parse_str($_POST['params'], $params); # print_r($params);

# ������

# ����� �� ������� �� ���� (�����)
if ($_POST['action'] == 'search_by_date') {
    # �������� �������� ����������
    if (empty($_POST['date_from']) && empty($_POST['date_to'])) exit('Variables "date_from" and "date_to" are both empty in "search_by_date" method.');

    # ����������� sql-�������
    unset($sqlCondition);
    if (!empty($_POST['date_from']) && empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from';
    }
    elseif (empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) <= :date_to';
    }
    elseif (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from and date(date_add) <= :date_to';
    }
    # /����������� sql-�������

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
    where 1
          '.$sqlCondition.'
    order by date_add desc
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);

    if (!empty($_POST['date_from']) && empty($_POST['date_to'])) {
        $dateFrom = date("Y-m-d", strtotime($_POST['date_from']));
        $sth->bindValue(':date_from', $dateFrom);
    }
    elseif (empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $dateTo = date("Y-m-d", strtotime($_POST['date_to']));
        $sth->bindValue(':date_to', $dateTo);
    }
    elseif (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $dateFrom = date("Y-m-d", strtotime($_POST['date_from']));
        $dateTo = date("Y-m-d", strtotime($_POST['date_to']));
        $sth->bindValue(':date_from', $dateFrom);
        $sth->bindValue(':date_to', $dateTo);
    }

    $sth->execute();
    if ($_ = $sth->fetchAll()) {
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
        }
        if (empty($rows)) $result .= '�� ��������� ������� ������ �� �������.';
        else {
            if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
            else unset($rows);

            $result .= '
            <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list" style="width:75%;overflow:hidden">
                <tr>
                    <th class="center vertical_middle nowrap">����������</th>
                    <th class="center vertical_middle nowrap">���� � �����</th>
                    <th class="center vertical_middle nowrap">����� ����������</th>
                    <th class="center vertical_middle">�����������������</th>
                </tr>
                '.$rows.'
            </table>
            ';
        }

        $array['result_set'] = iconv('windows-1251', 'UTF-8//TRANSLIT', $result);
        $array['statistics'] = iconv('windows-1251', 'UTF-8//TRANSLIT', getCallsStatistics(array('date_from' => $_POST['date_from'], 'date_to' => $_POST['date_to'])));
        echo json_encode($array);
    }
    else echo '�� ��������� ������� ������ �� �������.';
} # /����� �� ������� �� ���� (�����)

# ����� �� ����������
elseif ($_POST['action'] == 'search_by_sorting') {
    # �������� �������� ����������
    if (empty($_POST['date_from']) && empty($_POST['date_to']) && empty($_POST['type_of_sorting'])) exit('Variables "date_from" and "date_to" and "type_of_sorting" are empty in "search_by_sorting" method.');

    # ����������� sql-�������
    unset($sqlCondition);
    # ����
    if (!empty($_POST['date_from']) && empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from';
    }
    elseif (empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) <= :date_to';
    }
    elseif (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from and date(date_add) <= :date_to';
    }
    # /����
    # ����������
    switch ($_POST['type_of_sorting']) {
        case 'accepted': $sqlCondition .= ' and is_missed_call is null'; break;
        case 'missed': $sqlCondition .= ' and is_missed_call = 1'; break;
        case 'chaser': $sqlCondition .= ' and caller = "74996775485"'; break;
        # default: break;
    }
    # /����������
    # /����������� sql-�������

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
    where 1
          '.$sqlCondition.'
    order by date_add desc
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);

    if (!empty($_POST['date_from']) && empty($_POST['date_to'])) {
        $dateFrom = date("Y-m-d", strtotime($_POST['date_from']));
        $sth->bindValue(':date_from', $dateFrom);
    }
    elseif (empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $dateTo = date("Y-m-d", strtotime($_POST['date_to']));
        $sth->bindValue(':date_to', $dateTo);
    }
    elseif (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $dateFrom = date("Y-m-d", strtotime($_POST['date_from']));
        $dateTo = date("Y-m-d", strtotime($_POST['date_to']));
        $sth->bindValue(':date_from', $dateFrom);
        $sth->bindValue(':date_to', $dateTo);
    }

    $sth->execute();
    if ($_ = $sth->fetchAll()) {
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
        }
        if (empty($rows)) $result .= '�� ��������� ������� ������ �� �������.';
        else {
            if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
            else unset($rows);

            $result .= '
            <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list" style="width:75%;overflow:hidden">
                <tr>
                    <th class="center vertical_middle nowrap">����������</th>
                    <th class="center vertical_middle nowrap">���� � �����</th>
                    <th class="center vertical_middle nowrap">����� ����������</th>
                    <th class="center vertical_middle">�����������������</th>
                </tr>
                '.$rows.'
            </table>
            ';
        }

        $array['result_set'] = iconv('windows-1251', 'UTF-8//TRANSLIT', $result);
        $array['statistics'] = iconv('windows-1251', 'UTF-8//TRANSLIT', getCallsStatistics(array('date_from' => $_POST['date_from'], 'date_to' => $_POST['date_to'], 'type_of_sorting' => $_POST['type_of_sorting'])));
        echo json_encode($array);
    }
    else echo '�� ��������� ������� ������ �� �������.';
} # /����� �� ����������

# �������� � Excel
elseif ($_POST['action'] == 'export_to_excel') {
    # �������� �������� ����������
    if (empty($_POST['date_from']) && empty($_POST['date_to'])) exit('Variables "date_from" and "date_to" are both empty in "export_to_excel" method.');

    # PHPExcel
    require_once($_SERVER['DOCUMENT_ROOT'].'/app/library/PHPExcel_1.8.0/Classes/PHPExcel.php');

    # PHPExcel_Writer_Excel2007
    require_once($_SERVER['DOCUMENT_ROOT'].'/app/library/PHPExcel_1.8.0/Classes/PHPExcel/Writer/Excel2007.php');

    # Create new PHPExcel object
    # echo date('H:i:s') . " Create new PHPExcel object\n";
    $objPHPExcel = new PHPExcel();

    # �����

    # ����� ��� �������� ����� ������
    $redStyle = array('font'  => array('color' => array('rgb' => 'FF0000')));

    # ������ �����
    $boldStyle = array('font'  => array('bold'  => true));

    # ����� ��� ����������� �� ����������� �� ������
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));

    # ����� ��� ������ ����� ������
    $blueStyle = array('font'  => array('color' => array('rgb' => '2E79BE')));

    # /�����

    # ����������� sql-�������
    unset($sqlCondition);
    if (!empty($_POST['date_from']) && empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from';
    }
    elseif (empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) <= :date_to';
    }
    elseif (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from and date(date_add) <= :date_to';
    } # /����������� sql-�������

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
    where 1
          '.$sqlCondition.'
    order by date_add desc
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);

    if (!empty($_POST['date_from']) && empty($_POST['date_to'])) {
        $dateFrom = date("Y-m-d", strtotime($_POST['date_from']));
        $sth->bindValue(':date_from', $dateFrom);
    }
    elseif (empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $dateTo = date("Y-m-d", strtotime($_POST['date_to']));
        $sth->bindValue(':date_to', $dateTo);
    }
    elseif (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $dateFrom = date("Y-m-d", strtotime($_POST['date_from']));
        $dateTo = date("Y-m-d", strtotime($_POST['date_to']));
        $sth->bindValue(':date_from', $dateFrom);
        $sth->bindValue(':date_to', $dateTo);
    }

    $sth->execute();
    if ($_ = $sth->fetchAll()) {
        $_c = count($_);
        $rows = array();
        # ������� �����
        $rowsCount = 4; # �������� �������� ������ � 4 ������
        # �������� �� ��������
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

                $talk_duration = $_[$i]['reason'].' (' . $talkDurationModified . ')';

                # �������� ������� ����������� ������
                $objPHPExcel->getActiveSheet()->getStyle('D'.$rowsCount)->applyFromArray($redStyle);
            }

            # date_add_day
            /* if ($_[$i]['date_add_modified'] == date('Y-m-d')) $dateAdd = '������� � '.$_[$i]['date_add_time'];
            else $dateAdd = $_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year'].', '.$_[$i]['date_add_time']; */
            $dateAdd = $_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year'].', '.$_[$i]['date_add_time'];

            # ������������� ������ ���������
            if (!empty($_[$i]['call_record'])) {
                # ������� "����������"
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowsCount, iconv('windows-1251', 'UTF-8//TRANSLIT', '����������'), PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->getCell('A'.$rowsCount)->getHyperlink()->setUrl('http://'.$_SERVER['HTTP_HOST'].'/control/phone_calls/get_call_record.php?call_record='.$_[$i]['call_record']);
                # $objPHPExcel->getActiveSheet()->getCell('A'.$rowsCount)->getHyperlink('123')->setTooltip('���������� ������');
            }
            else {
                # ������� "����������"
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowsCount, '', PHPExcel_Cell_DataType::TYPE_STRING);
            }

            # caller
            unset($caller);
            # if ($_[$i]['caller'] == '74996775485') $caller = '74996775485<sup>������</sup>';
            if ($_[$i]['caller'] == '74996775485') {
                $caller = $_[$i]['caller'].' (������)';
                # �������� �����
                $objPHPExcel->getActiveSheet()->getStyle('C'.$rowsCount)->applyFromArray($blueStyle);
            }
            else $caller = $_[$i]['caller'];

            # ����� ������ � excel-����

            # ������� "���� � �����"
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowsCount, iconv('windows-1251', 'UTF-8//TRANSLIT', trim($dateAdd)), PHPExcel_Cell_DataType::TYPE_STRING);

            # ������� "����� ����������"
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowsCount, iconv('windows-1251', 'UTF-8//TRANSLIT', trim($caller)), PHPExcel_Cell_DataType::TYPE_STRING);

            # ������� "�����������������"
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowsCount, iconv('windows-1251', 'UTF-8//TRANSLIT', trim($talk_duration)), PHPExcel_Cell_DataType::TYPE_STRING);

            # /����� ������ � excel-����

            # ��������� ������� ������
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowsCount.':'.'D'.$rowsCount)->getAlignment()->setWrapText(true);

            # ����������� ���������� �� ������
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowsCount.':'.'D'.$rowsCount)->applyFromArray($style);

            # ������� �����
            $rowsCount++;
        } # /�������� �� ��������

        # Set properties
        # echo date('H:i:s') . " Set properties\n";
        $objPHPExcel->getProperties()->setCreator(HTTP_HOST);
        # $objPHPExcel->getProperties()->setLastModifiedBy(HTTP_HOST);
        $objPHPExcel->getProperties()->setTitle(HTTP_HOST);
        $objPHPExcel->getProperties()->setSubject(HTTP_HOST);
        # $objPHPExcel->getProperties()->setDescription(HTTP_HOST);

        # echo $result;
        // Add some data
        # echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0);

        # ����� ��� ������������ ����������� �� ������
        $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));

        # ��������� ��������� �����
        $objPHPExcel->getActiveSheet()->freezePane('A4');

        # Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle(iconv('windows-1251', 'UTF-8//TRANSLIT', '�������� �� '.date('j.n.Y H.i')));

        # ������ 1: ������� �������� �������

        # ���������� 4 �������
        $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
        # ��������� �������
        $content = HTTP_HOST.' - �������� ���������� �������';
        if (!empty($_POST['date_from']) && empty($_POST['date_to'])) {
            $content .= ' � ' . $_POST['date_from'].' �� �����';
        }
        elseif (empty($_POST['date_from']) && !empty($_POST['date_to'])) {
            $content .= ' � ������ �� ' . $_POST['date_to'];
        }
        elseif (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
            $content .= ' � '.$_POST['date_from'].' �� ' . $_POST['date_to'];
        }
        # /��������� �������
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', iconv('windows-1251', 'UTF-8//TRANSLIT', trim($content)), PHPExcel_Cell_DataType::TYPE_STRING);
        # ������������� ������ ������ ��� 2 ������
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        # �������� ������
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($boldStyle);
        # ���������� ���������� �� ����������� �� ������
        $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style);

        # /������ 1: ������� �������� �������

        # ������ 2: ������� ����������
        $statistics = getCallsStatistics(array('date_from' => $_POST['date_from'], 'date_to' => $_POST['date_to'], 'type_of_sorting' => $_POST['type_of_sorting']));
        # �������� �����
        $statistics = str_replace(array('<br>', '<br/>', '<br />'), PHP_EOL, $statistics);
        # ������� ����
        $statistics = strip_tags($statistics);
        # ��������� �������
        $objPHPExcel->getActiveSheet()->SetCellValue('A2', iconv('windows-1251', 'UTF-8//TRANSLIT', trim($statistics)), PHPExcel_Cell_DataType::TYPE_STRING);
        # ���������� 4 ������� ��� 2 ������
        $objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
        # ��������� ������� ������
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);
        # ���������� ������ ������ ������ (����-������ �� ��������)
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
        # ������������� ������ ������ ��� 2 ������
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
        # ����������� �� ��������� �� ������
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);

        # /������ 2: ������� ����������

        # ������ 3: ����� �������

        # ������� �������� ������� 1
        $objPHPExcel->getActiveSheet()->SetCellValue('A3', iconv('windows-1251', 'UTF-8//TRANSLIT', trim('����������')), PHPExcel_Cell_DataType::TYPE_STRING);
        # ������� �������� ������� 2
        $objPHPExcel->getActiveSheet()->SetCellValue('B3', iconv('windows-1251', 'UTF-8//TRANSLIT', trim('���� � �����')), PHPExcel_Cell_DataType::TYPE_STRING);
        # ������� �������� ������� 3
        $objPHPExcel->getActiveSheet()->SetCellValue('C3', iconv('windows-1251', 'UTF-8//TRANSLIT', trim('����� ����������')), PHPExcel_Cell_DataType::TYPE_STRING);
        # ������� �������� ������� 4
        $objPHPExcel->getActiveSheet()->SetCellValue('D3', iconv('windows-1251', 'UTF-8//TRANSLIT', trim('�����������������')), PHPExcel_Cell_DataType::TYPE_STRING);

        # ������������� ������ ������
        $objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFont()->setSize(16);

        # ���������� ���������� �������� �� ����������� �� ������
        $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $objPHPExcel->getActiveSheet()->getStyle('A3:D3')->applyFromArray($style);

        # ����������� ������ �������� �� �����������
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

        # /������ 3: ����� �������

        # ��������� ��� �����
        if (!empty($_POST['date_from']) && empty($_POST['date_to'])) {
            $fileName = HTTP_HOST.'_calls_from_'.$_POST['date_from'].'.xlsx';
        }
        elseif (empty($_POST['date_from']) && !empty($_POST['date_to'])) {
            $fileName = HTTP_HOST.'_calls_to_'.$_POST['date_to'].'.xlsx';
        }
        elseif (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
            $fileName = HTTP_HOST.'_calls_from_'.$_POST['date_from'].'_to_'.$_POST['date_to'].'.xlsx';
        }
        $fullFilePath = $_SERVER['DOCUMENT_ROOT'].'/control/public/excel_export/'.$fileName;
        $shortFilePath = '/control/public/excel_export/'.$fileName;

        # ������� ��� ����� � �����: /control/public/excel_export/
        # print_r(glob($_SERVER['DOCUMENT_ROOT'].'/control/public/excel_export/*')); #
        array_map('unlink', glob($_SERVER['DOCUMENT_ROOT'].'/control/public/excel_export/*'));
        # if (file_exists($filePath)) unlink($filePath);

        # Save Excel 2007 file
        # echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        try {
            $objWriter->save($fullFilePath);
            $resultArray = array(
                'result' => 'success',
                'path_to_file' => $shortFilePath
            );
            echo json_encode($resultArray);
        }
        catch (Exception $e) {
            # echo 'Caught exception: '.$e->getMessage().PHP_EOL;
            $resultArray = array(
                'result' => 'fail',
                'error' => $e->getMessage()
            );
            echo json_encode($resultArray);
        }
    }
    else echo '�� ��������� ������� ������ �� �������.';
} # /�������� � Excel

# /������

# �������

# /�������