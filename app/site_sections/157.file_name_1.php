<?php if (!empty($GLOBALS['tpl_news'])): # ����� �������� ?>
<?php foreach ($GLOBALS['tpl_news'] as $item): ?>
<div class="news_item">
    <div class="date"><?php echo $item['date_add_day'].' '.getRusMonthName($item['date_add_month']).' '.$item['date_add_year']; ?></div>
    <a class="news_item_a" href="/novosti/<?php echo $item['date_add_formatted_2']; ?>/"><?php echo $item['h1']; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/novosti/<?php echo $item['date_add_formatted_2']; ?>/">��������� &raquo;</a>
</div>
<?php endforeach; ?>
<?php endif; # /����� �������� ?>
               
<?php if (!empty($GLOBALS['tpl_news_pages'])): # ����� ������� ?>
<div class="paging_b">
    <div class="paging">
        <?php echo $GLOBALS['tpl_news_pages']; ?>
    </div>

    <div class="sum_pages">�����: <?php echo $GLOBALS['tpl_news_count']; ?></div>
    <div class="clear"></div>
</div>
<?php endif; # /����� ������� ?>