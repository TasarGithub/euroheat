<div class="owl-carousel owl-carousel_b_reviews">
             <div class="item">
                    <a href="" class="fancybox" data-fancybox-group="gallery2"><img src=""></a>
                </div>
              <div class="item">
                    <a href="" class="fancybox" data-fancybox-group="gallery2"><img src=""></a>
                </div>
              <div class="item">
                    <a href="" class="fancybox" data-fancybox-group="gallery2"><img src=""></a>
                </div>
              <div class="item">
                    <a href="" class="fancybox" data-fancybox-group="gallery2"><img src=""></a>
                </div>
              <div class="item">
                    <a href="" class="fancybox" data-fancybox-group="gallery2"><img src=""></a>
                </div>
              <div class="item">
                    <a href="" class="fancybox" data-fancybox-group="gallery2"><img src=""></a>
                </div>
              <div class="item">
                    <a href="" class="fancybox" data-fancybox-group="gallery2"><img src=""></a>
                </div>
</div>

<a class="order_btn_1" href="#review_modal" data-toggle="modal">Написать отзыв</a>

<?php if (!empty($GLOBALS['tpl_feedback'])): # вывод отзывов ?>
<div class="reviews_p">
<?php foreach ($GLOBALS['tpl_feedback'] as $item): ?>
<div class="review_b">
    <div class="cont">
        <?php echo $item['feedback']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/otzyvy/<?php echo $item['id']; ?>/">Подробнее &raquo;</a>
    </div>
    
    <div class="est" data-feedback-id="<?php echo $item['id']; ?>">
        <a href="#" class="plus customer-feedback-vote" data-action="plus"></a><div class="feedback-votes-plus-<?php echo $item['id']; ?>">+<?php echo $item['votes_plus']; ?></div>
        <a href="#" class="minus customer-feedback-vote" data-action="minus"></a><div class="feedback-votes-minus-<?php echo $item['id']; ?>">-<?php echo $item['votes_minus']; ?></div>
    </div>
    <div class="name"><?php echo $item['name']; ?>, <?php echo $item['date_add_day'].' '.$item['date_add_month'].' '.$item['date_add_year']; ?></div>
    <div class="clear"></div>
</div>
<?php endforeach; ?>
<div class="clear"></div>
</div> <!-- /.reviews_p -->
<?php endif; # /вывод отзывов ?>

<?php if (!empty($GLOBALS['tpl_feedback_pages'])): # вывод страниц ?>
<div class="paging_b">
    <div class="paging">
        <?php echo $GLOBALS['tpl_feedback_pages']; ?>
    </div>

    <div class="sum_pages">Всего: <?php echo $GLOBALS['tpl_feedback_count']; ?></div>
    <div class="clear"></div>
</div>
<?php endif; # /вывод страниц ?>

<a class="order_btn_1" href="#review_modal" data-toggle="modal">Написать отзыв</a>