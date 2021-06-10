<?php if (!empty($GLOBALS['tpl_faq'])): # вывод вопросов-ответов ?>
<?php foreach ($GLOBALS['tpl_faq'] as $item): ?>
<div class="news_item">
    <a class="news_item_a" href="/vopros/<?php echo $item['url']; ?>/"><?php echo $item['h1']; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/vopros/<?php echo $item['url']; ?>/">ѕодробнее &raquo;</a>
</div>
<?php endforeach; ?>
<?php endif; # /вывод вопросов-ответов ?>

<br />   

<?php if (!empty($GLOBALS['tpl_faq_pages'])): # вывод страниц ?>
<div class="paging_b">
    <div class="paging">
        <?php echo $GLOBALS['tpl_faq_pages']; ?>
    </div>

    <div class="sum_pages">¬сего: <?php echo $GLOBALS['tpl_faq_count']; ?></div>
    <div class="clear"></div>
</div>
<?php endif; # /вывод страниц ?>