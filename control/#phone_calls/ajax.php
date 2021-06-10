<?php

### ОТЛАДКА
# print_r($_POST);
# print_r($_POST);

# $a = unserialize($_POST['form']); print_r($a);

# sleep(5);

# защита от запроса c другого сайта
if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) exit('');

# указываем кодировку, которую будет отдавать javascript'у ajax-скрипт
header('Content-type: text/html; charset=windows-1251');

# подключаем и инициализируем класс для работы с БД через PDO
include($_SERVER['DOCUMENT_ROOT'].'/control/db.connection.pdo.php');

# подключаем конфиг
include($_SERVER['DOCUMENT_ROOT'].'/control/config.control.php');

# подключаем функции общего назначения для ajax-скриптов
include($_SERVER['DOCUMENT_ROOT'].'/control/functions.common.ajax.php');

# подключаем общие функции для index.php и ajax.php
include('common.functions.php');

# проверка + нужная кодировка POST-переменных
preparePOSTVariables(); # print_r($_POST); exit;

# подготавливаем поля формы
if (!empty($_POST['params'])) parse_str($_POST['params'], $params); # print_r($params);

# ЛОГИКА

# Поиск по звонкам по дате (датам)
if ($_POST['action'] == 'search_by_date') {
    # проверка входящих переменных
    if (empty($_POST['date_from']) && empty($_POST['date_to'])) exit('Variables "date_from" and "date_to" are both empty in "search_by_date" method.');

    # модификация sql-запроса
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
    # /модификация sql-запроса

    $sql = '
    select id,
           date_format(date_add, "%e") as date_add_day,
           elt(month(date_add), "янв.", "фев.", "мар.", "апр.", "мая", "июня", "июля", "авг.", "сен.", "окт.", "ноя.", "дек.") as date_add_month,
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
            # talk_duration: переводим секунды в менуты и секунды
            if ($_[$i]['talk_duration'] > 60) {
                $duration = floor($_[$i]['talk_duration'] / 60);
                $remainder = $_[$i]['talk_duration'] % 60;
                $talkDurationModified = $duration.' м. '.$remainder.' с.';
            }
            else $talkDurationModified = $_[$i]['talk_duration'] . ' с.';

            # talk_duration
            if (empty($_[$i]['is_missed_call'])) $talk_duration = $talkDurationModified;
            else {
                if (empty($_[$i]['reason'])) $_[$i]['reason'] = 'Причина не указана';

                $talk_duration = '<span class="red">'.$_[$i]['reason'].'</span> (' . $talkDurationModified . ')';
            }

            # date_add_day
            if ($_[$i]['date_add_modified'] == date('Y-m-d')) $dateAdd = '<span class="bold">сегодня в '.$_[$i]['date_add_time'].'</span>';
            else $dateAdd = $_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year'].', '.$_[$i]['date_add_time'];

            # проигрыватель записи разговора
            if (!empty($_[$i]['call_record'])) $listen = '
            <a class="audio {skin:\'blue\', showTime:true, downloadable:true }" href="/control/phone_calls/get_call_record.php?call_record='.$_[$i]['call_record'].'"></a>';
            else unset($listen);

            # имя файла для скачивания
            $callRecord = HTTP_HOST.'_'.$_[$i]['date_add_for_file_name_for_download'].'_s_nomera_'.$_[$i]['caller'].'.mp3';

            # caller
            unset($caller);
            # if ($_[$i]['caller'] == '74996775485') $caller = '74996775485<sup>чайзер</sup>';
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
        if (empty($rows)) $result .= 'По заданному запросу ничего не найдено.';
        else {
            if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
            else unset($rows);

            $result .= '
            <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list" style="width:75%;overflow:hidden">
                <tr>
                    <th class="center vertical_middle nowrap">Прослушать</th>
                    <th class="center vertical_middle nowrap">Дата и время</th>
                    <th class="center vertical_middle nowrap">Номер звонившего</th>
                    <th class="center vertical_middle">Продолжительность</th>
                </tr>
                '.$rows.'
            </table>
            ';
        }

        $array['result_set'] = iconv('windows-1251', 'UTF-8//TRANSLIT', $result);
        $array['statistics'] = iconv('windows-1251', 'UTF-8//TRANSLIT', getCallsStatistics(array('date_from' => $_POST['date_from'], 'date_to' => $_POST['date_to'])));
        echo json_encode($array);
    }
    else echo 'По заданному запросу ничего не найдено.';
} # /Поиск по звонкам по дате (датам)

# Поиск по сортировке
elseif ($_POST['action'] == 'search_by_sorting') {
    # проверка входящих переменных
    if (empty($_POST['date_from']) && empty($_POST['date_to']) && empty($_POST['type_of_sorting'])) exit('Variables "date_from" and "date_to" and "type_of_sorting" are empty in "search_by_sorting" method.');

    # модификация sql-запроса
    unset($sqlCondition);
    # даты
    if (!empty($_POST['date_from']) && empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from';
    }
    elseif (empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) <= :date_to';
    }
    elseif (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from and date(date_add) <= :date_to';
    }
    # /даты
    # сортировка
    switch ($_POST['type_of_sorting']) {
        case 'accepted': $sqlCondition .= ' and is_missed_call is null'; break;
        case 'missed': $sqlCondition .= ' and is_missed_call = 1'; break;
        case 'chaser': $sqlCondition .= ' and caller = "74996775485"'; break;
        # default: break;
    }
    # /сортировка
    # /модификация sql-запроса

    $sql = '
    select id,
           date_format(date_add, "%e") as date_add_day,
           elt(month(date_add), "янв.", "фев.", "мар.", "апр.", "мая", "июня", "июля", "авг.", "сен.", "окт.", "ноя.", "дек.") as date_add_month,
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
            # talk_duration: переводим секунды в менуты и секунды
            if ($_[$i]['talk_duration'] > 60) {
                $duration = floor($_[$i]['talk_duration'] / 60);
                $remainder = $_[$i]['talk_duration'] % 60;
                $talkDurationModified = $duration.' м. '.$remainder.' с.';
            }
            else $talkDurationModified = $_[$i]['talk_duration'] . ' с.';

            # talk_duration
            if (empty($_[$i]['is_missed_call'])) $talk_duration = $talkDurationModified;
            else {
                if (empty($_[$i]['reason'])) $_[$i]['reason'] = 'Причина не указана';

                $talk_duration = '<span class="red">'.$_[$i]['reason'].'</span> (' . $talkDurationModified . ')';
            }

            # date_add_day
            if ($_[$i]['date_add_modified'] == date('Y-m-d')) $dateAdd = '<span class="bold">сегодня в '.$_[$i]['date_add_time'].'</span>';
            else $dateAdd = $_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year'].', '.$_[$i]['date_add_time'];

            # проигрыватель записи разговора
            if (!empty($_[$i]['call_record'])) $listen = '
            <a class="audio {skin:\'blue\', showTime:true, downloadable:true }" href="/control/phone_calls/get_call_record.php?call_record='.$_[$i]['call_record'].'"></a>';
            else unset($listen);

            # имя файла для скачивания
            $callRecord = HTTP_HOST.'_'.$_[$i]['date_add_for_file_name_for_download'].'_s_nomera_'.$_[$i]['caller'].'.mp3';

            # caller
            unset($caller);
            # if ($_[$i]['caller'] == '74996775485') $caller = '74996775485<sup>чайзер</sup>';
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
        if (empty($rows)) $result .= 'По заданному запросу ничего не найдено.';
        else {
            if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
            else unset($rows);

            $result .= '
            <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list" style="width:75%;overflow:hidden">
                <tr>
                    <th class="center vertical_middle nowrap">Прослушать</th>
                    <th class="center vertical_middle nowrap">Дата и время</th>
                    <th class="center vertical_middle nowrap">Номер звонившего</th>
                    <th class="center vertical_middle">Продолжительность</th>
                </tr>
                '.$rows.'
            </table>
            ';
        }

        $array['result_set'] = iconv('windows-1251', 'UTF-8//TRANSLIT', $result);
        $array['statistics'] = iconv('windows-1251', 'UTF-8//TRANSLIT', getCallsStatistics(array('date_from' => $_POST['date_from'], 'date_to' => $_POST['date_to'], 'type_of_sorting' => $_POST['type_of_sorting'])));
        echo json_encode($array);
    }
    else echo 'По заданному запросу ничего не найдено.';
} # /Поиск по сортировке

# Выгрузка в Excel
elseif ($_POST['action'] == 'export_to_excel') {
    # проверка входящих переменных
    if (empty($_POST['date_from']) && empty($_POST['date_to'])) exit('Variables "date_from" and "date_to" are both empty in "export_to_excel" method.');

    # PHPExcel
    require_once($_SERVER['DOCUMENT_ROOT'].'/app/library/PHPExcel_1.8.0/Classes/PHPExcel.php');

    # PHPExcel_Writer_Excel2007
    require_once($_SERVER['DOCUMENT_ROOT'].'/app/library/PHPExcel_1.8.0/Classes/PHPExcel/Writer/Excel2007.php');

    # Create new PHPExcel object
    # echo date('H:i:s') . " Create new PHPExcel object\n";
    $objPHPExcel = new PHPExcel();

    # СТИЛИ

    # стиль для красного цвета текста
    $redStyle = array('font'  => array('color' => array('rgb' => 'FF0000')));

    # жирный стиль
    $boldStyle = array('font'  => array('bold'  => true));

    # стиль для выранивания по горизонтали по центру
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));

    # стиль для синего цвета текста
    $blueStyle = array('font'  => array('color' => array('rgb' => '2E79BE')));

    # /СТИЛИ

    # модификация sql-запроса
    unset($sqlCondition);
    if (!empty($_POST['date_from']) && empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from';
    }
    elseif (empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) <= :date_to';
    }
    elseif (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from and date(date_add) <= :date_to';
    } # /модификация sql-запроса

    $sql = '
    select id,
           date_format(date_add, "%e") as date_add_day,
           elt(month(date_add), "янв.", "фев.", "мар.", "апр.", "мая", "июня", "июля", "авг.", "сен.", "окт.", "ноя.", "дек.") as date_add_month,
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
        # Счетчик строк
        $rowsCount = 4; # начинаем выводить данные с 4 строки
        # Проходим по столбцам
        for ($i=0;$i<$_c;$i++) {
            # talk_duration: переводим секунды в менуты и секунды
            if ($_[$i]['talk_duration'] > 60) {
                $duration = floor($_[$i]['talk_duration'] / 60);
                $remainder = $_[$i]['talk_duration'] % 60;
                $talkDurationModified = $duration.' м. '.$remainder.' с.';
            }
            else $talkDurationModified = $_[$i]['talk_duration'] . ' с.';

            # talk_duration
            if (empty($_[$i]['is_missed_call'])) $talk_duration = $talkDurationModified;
            else {
                if (empty($_[$i]['reason'])) $_[$i]['reason'] = 'Причина не указана';

                $talk_duration = $_[$i]['reason'].' (' . $talkDurationModified . ')';

                # выделяем красным пропущенные звонки
                $objPHPExcel->getActiveSheet()->getStyle('D'.$rowsCount)->applyFromArray($redStyle);
            }

            # date_add_day
            /* if ($_[$i]['date_add_modified'] == date('Y-m-d')) $dateAdd = 'сегодня в '.$_[$i]['date_add_time'];
            else $dateAdd = $_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year'].', '.$_[$i]['date_add_time']; */
            $dateAdd = $_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year'].', '.$_[$i]['date_add_time'];

            # проигрыватель записи разговора
            if (!empty($_[$i]['call_record'])) {
                # Столбец "Прослушать"
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowsCount, iconv('windows-1251', 'UTF-8//TRANSLIT', 'Прослушать'), PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->getCell('A'.$rowsCount)->getHyperlink()->setUrl('http://'.$_SERVER['HTTP_HOST'].'/control/phone_calls/get_call_record.php?call_record='.$_[$i]['call_record']);
                # $objPHPExcel->getActiveSheet()->getCell('A'.$rowsCount)->getHyperlink('123')->setTooltip('Прослушать запись');
            }
            else {
                # Столбец "Прослушать"
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowsCount, '', PHPExcel_Cell_DataType::TYPE_STRING);
            }

            # caller
            unset($caller);
            # if ($_[$i]['caller'] == '74996775485') $caller = '74996775485<sup>чайзер</sup>';
            if ($_[$i]['caller'] == '74996775485') {
                $caller = $_[$i]['caller'].' (чейзер)';
                # выделяем синим
                $objPHPExcel->getActiveSheet()->getStyle('C'.$rowsCount)->applyFromArray($blueStyle);
            }
            else $caller = $_[$i]['caller'];

            # Пишем данные в excel-файл

            # Столбец "Дата и время"
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowsCount, iconv('windows-1251', 'UTF-8//TRANSLIT', trim($dateAdd)), PHPExcel_Cell_DataType::TYPE_STRING);

            # Столбец "Номер звонившего"
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowsCount, iconv('windows-1251', 'UTF-8//TRANSLIT', trim($caller)), PHPExcel_Cell_DataType::TYPE_STRING);

            # Столбец "Продолжительность"
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowsCount, iconv('windows-1251', 'UTF-8//TRANSLIT', trim($talk_duration)), PHPExcel_Cell_DataType::TYPE_STRING);

            # /Пишем данные в excel-файл

            # разрешаем перенос строки
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowsCount.':'.'D'.$rowsCount)->getAlignment()->setWrapText(true);

            # выравниваем содержимое по центру
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowsCount.':'.'D'.$rowsCount)->applyFromArray($style);

            # Счетчик строк
            $rowsCount++;
        } # /Проходим по столбцам

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

        # стиль для выравнивания содержимого по центру
        $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));

        # фиксируем плавающую шапку
        $objPHPExcel->getActiveSheet()->freezePane('A4');

        # Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle(iconv('windows-1251', 'UTF-8//TRANSLIT', 'Выгрузка от '.date('j.n.Y H.i')));

        # СТРОКА 1: ВЫВОДИМ НАЗВАНИЕ ПРОЕКТА

        # Объединяем 4 столбца
        $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
        # формируем контент
        $content = HTTP_HOST.' - выгрузка телефонных звонков';
        if (!empty($_POST['date_from']) && empty($_POST['date_to'])) {
            $content .= ' с ' . $_POST['date_from'].' до конца';
        }
        elseif (empty($_POST['date_from']) && !empty($_POST['date_to'])) {
            $content .= ' с начала до ' . $_POST['date_to'];
        }
        elseif (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
            $content .= ' с '.$_POST['date_from'].' по ' . $_POST['date_to'];
        }
        # /формируем контент
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', iconv('windows-1251', 'UTF-8//TRANSLIT', trim($content)), PHPExcel_Cell_DataType::TYPE_STRING);
        # устанавливаем размер шрифта для 2 строки
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
        # выделяем жирным
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($boldStyle);
        # выраниваем содержимое по горизонтали по центру
        $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style);

        # /СТРОКА 1: ВЫВОДИМ НАЗВАНИЕ ПРОЕКТА

        # СТРОКА 2: ВЫВОДИМ СТАТИСТИКУ
        $statistics = getCallsStatistics(array('date_from' => $_POST['date_from'], 'date_to' => $_POST['date_to'], 'type_of_sorting' => $_POST['type_of_sorting']));
        # переносы строк
        $statistics = str_replace(array('<br>', '<br/>', '<br />'), PHP_EOL, $statistics);
        # убираем теги
        $statistics = strip_tags($statistics);
        # фиксируем контент
        $objPHPExcel->getActiveSheet()->SetCellValue('A2', iconv('windows-1251', 'UTF-8//TRANSLIT', trim($statistics)), PHPExcel_Cell_DataType::TYPE_STRING);
        # Объединяем 4 столбца для 2 строки
        $objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
        # разрешаем перенос строки
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);
        # выставляем высоту первой строки (авто-высота не работает)
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
        # устанавливаем размер шрифта для 2 строки
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
        # выравниваем по вертикали по центру
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);

        # /СТРОКА 2: ВЫВОДИМ СТАТИСТИКУ

        # СТРОКА 3: ШАПКА ТАБЛИЦЫ

        # Выводим название столбца 1
        $objPHPExcel->getActiveSheet()->SetCellValue('A3', iconv('windows-1251', 'UTF-8//TRANSLIT', trim('Прослушать')), PHPExcel_Cell_DataType::TYPE_STRING);
        # Выводим название столбца 2
        $objPHPExcel->getActiveSheet()->SetCellValue('B3', iconv('windows-1251', 'UTF-8//TRANSLIT', trim('Дата и время')), PHPExcel_Cell_DataType::TYPE_STRING);
        # Выводим название столбца 3
        $objPHPExcel->getActiveSheet()->SetCellValue('C3', iconv('windows-1251', 'UTF-8//TRANSLIT', trim('Номер звонившего')), PHPExcel_Cell_DataType::TYPE_STRING);
        # Выводим название столбца 4
        $objPHPExcel->getActiveSheet()->SetCellValue('D3', iconv('windows-1251', 'UTF-8//TRANSLIT', trim('Продолжительность')), PHPExcel_Cell_DataType::TYPE_STRING);

        # устанавливаем размер шрифта
        $objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFont()->setSize(16);

        # выраниваем содержимое столбцов по горизонтали по центру
        $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $objPHPExcel->getActiveSheet()->getStyle('A3:D3')->applyFromArray($style);

        # растягиваем ширину столбцов по содержимому
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

        # /СТРОКА 3: ШАПКА ТАБЛИЦЫ

        # формируем имя файла
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

        # удаляем все файлы в папке: /control/public/excel_export/
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
    else echo 'По заданному запросу ничего не найдено.';
} # /Выгрузка в Excel

# /ЛОГИКА

# ФУНКЦИИ

# /ФУНКЦИИ