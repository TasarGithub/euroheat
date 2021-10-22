<?php if (!empty($GLOBALS['tpl_articles'])): # вывод статей ?>
<?php foreach ($GLOBALS['tpl_articles'] as $item): ?>
<div class="news_item">
    <a class="news_item_a" href="/sovet/<?php echo $item['url']; ?>/"><?php echo $item['h1']; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/sovet/<?php echo $item['url']; ?>/">Подробнее &raquo;</a>
</div>
<?php endforeach; ?>
<?php endif; # /вывод статей ?>

<?php if (!empty($GLOBALS['tpl_articles_pages'])): # вывод страниц ?>
<div class="paging_b">
    <div class="paging">
        <?php echo $GLOBALS['tpl_articles_pages']; ?>
    </div>

    <div class="sum_pages">Всего: <?php echo $GLOBALS['tpl_articles_count']; ?></div>
    <div class="clear"></div>
</div>
<?php endif; # /вывод страниц ?>